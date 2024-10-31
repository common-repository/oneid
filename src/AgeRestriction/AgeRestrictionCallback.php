<?php
/**
 * AgeRestriction Callback.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification\AgeVerification;
use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification\AgeVerificationCollection;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdCallback;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdCallbackHandler;
use DigitalIdentityNet\OneId\WordPress\Plugin\Client\OneIdClientInterface;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdSettingsManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\SessionManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\OneIdFlashMessages;
use OneId_Vendor\Jumbojett\OpenIDConnectClientException;

class AgeRestrictionCallback implements OneIdCallback {
	const AGE_VERIFICATIONS_SESSION_KEY = ONEID_PREFIX . '_age_verifications';
	const USER_INFO_SESSION_KEY = ONEID_PREFIX . '_user_info';
	const DEFAULT_SHOULD_REDIRECT = true;

	/**
	 * Client to communicate with OneID.
	 *
	 * @var OneIdClientInterface
	 */
	private $client;

	/**
	 * Should the callback redirect? In almost all instances this should be true but can be useful to disable for testing.
	 *
	 * @var bool
	 */
	private $should_redirect;

	/**
	 * OneIdCallback constructor.
	 *
	 * @param OneIdClientInterface $client OneID client.
	 * @param bool                 $should_redirect Should the callback redirect?.
	 */
	public function __construct( OneIdClientInterface $client, bool $should_redirect = self::DEFAULT_SHOULD_REDIRECT ) {
		$this->client = $client;
		$this->should_redirect = $should_redirect;
	}

	/**
	 * Handler for the callback.
	 *
	 * @param string|null $code Code returned from OneID.
	 * @param string|null $state State returned from OneID that we passed initially.
	 *
	 * @return void
	 */
	public function handle( $code, $state ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		try {
			$this->client->authenticate();

			$age_verification_response = (array) $this->client->request_age_verification();
			$age_restriction = AgeRestriction::from_slug( key( $age_verification_response ) );
			$is_over_age = true === $age_verification_response[ $age_restriction->get_slug() ];
			$age_verification = AgeVerification::from_age_restriction_and_status( $age_restriction, $is_over_age );

			if ( ! $age_verification->is_verified() ) {
				OneIdFlashMessages::add_message(
					__( 'Unfortunately OneID was unable to verify that you are over the specified age. Please either retry, or proceed to use the vendor’s standard age verification checks.', 'oneid' ),
					'error'
				);
			}

			/**
			 * Grab age verifications from the session and add the new one.
			 *
			 * @var AgeVerificationCollection $age_verifications
			 */
			$age_verifications = SessionManager::get_instance()->get( self::AGE_VERIFICATIONS_SESSION_KEY );

			if ( ! $age_verifications ) {
				/**
				 * It looks like the session hasn't been set up properly yet. It's likely that they've opened this callback page in a new
				 * browser.  This can happen if the user's banking app opens a different browser than the one which the user used to begin
				 * checking out. 
				 * 
				 * For now, we'll show the user an error message saying they need to try again.
				 */
				do_action( 'woocommerce_set_cart_cookies',  true ); // required for wc_add_notice to work on a new session
				wc_add_notice( __( 'Oops, something went wrong! Please try again using your current web browser.', 'oneid' ), 'error' );
			} else {
				$age_verifications->add_age_verification( $age_verification );
				SessionManager::get_instance()->set( self::AGE_VERIFICATIONS_SESSION_KEY, $age_verifications );

				// Get user info from client -> then do something with it.
				$user_info = $this->client->request_user_info();
				SessionManager::get_instance()->set( self::USER_INFO_SESSION_KEY, $user_info );
			}
		} catch ( InvalidAgeRestrictionException $e ) {
			OneIdFlashMessages::add_message( __( 'A technical error has occurred. Please retry.', 'oneid' ), 'error' );
		} catch ( OpenIDConnectClientException $e ) {
			// TODO: Abstract out logging.
			if ( function_exists( 'wc_get_logger' ) ) {
				$logger = wc_get_logger();
				$logger->error( $e->getMessage(), [ 'source' => OneIdCallbackHandler::PAGE_NAME ] );
			}

			switch ( true ) {
				case false !== strpos( $e->getMessage(), 'access_denied' ):
				case false !== strpos( $e->getMessage(), 'invalid_scope' ):
				case false !== strpos( $e->getMessage(), 'unauthorized_client' ):
				case false !== strpos( $e->getMessage(), 'unsupported_response_type' ):
					OneIdFlashMessages::add_message( __( 'We were unable to complete authentication with your bank. Please retry or select a different bank.', 'oneid' ), 'error' );
					break;
				case false !== strpos( $e->getMessage(), 'temporarily_unavailable' ):
				case false !== strpos( $e->getMessage(), 'server_error' ):
				default:
					OneIdFlashMessages::add_message( __( 'A technical error has occurred. Please retry.', 'oneid' ), 'error' );
					break;
			}
		}

		if ( true === $this->should_redirect ) {
			$redirect = wc_get_checkout_url();
			wp_redirect( wp_validate_redirect( $redirect ) ); //phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
			exit;
		}
	}
}
