<?php
/**
 * Age Restriction Checkout frontend setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification\AgeVerificationCollection;
use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\Checkout\AddressPrePopulation;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdButtonRenderer;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\SessionManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\OneIdFlashMessages;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdSettingsManager;

class AgeRestrictionCheckout {
	const DEFAULT_CHECKOUT_HOOK = 'woocommerce_before_checkout_form';
	const SKIP_COOKIE_ID = ONEID_PREFIX . '-skip';
	const SKIP_STATUS = 'skip';
	const CART_HASH_COOKIE_ID = 'woocommerce_cart_hash';
	const AV_SCOPES = [ 'profile' ];
	const AV_PLUS_SCOPES = [ 'profile', 'address', 'email', 'phone' ];

	/**
	 * OneIdButtonRenderer instance.
	 *
	 * @var OneIdButtonRenderer
	 */
	private $button_renderer;

	/**
	 * AgeRestrictionCheckout constructor.
	 *
	 * @param OneIdButtonRenderer $button_renderer ButtonRenderer instance.
	 */
	public function __construct( OneIdButtonRenderer $button_renderer ) {
		$this->button_renderer = $button_renderer;
	}

	/**
	 * Initialisaton for OneID AgeRestriction Checkout.
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * Hook: oneid_button_checkout_hook
		 *
		 * @param string $hook Hook to use for display of oneid button on checkout.
		 */
		$hook = apply_filters( ONEID_PREFIX . '_button_checkout_hook', self::DEFAULT_CHECKOUT_HOOK );
		add_action( $hook, [ $this, 'render_oneid_button' ] );
		add_action( 'woocommerce_review_order_before_submit', [ __CLASS__, 'render_oneid_skip_button_message' ] );
		add_filter( 'woocommerce_order_button_html', [ __CLASS__, 'render_place_order_button_html' ] );

		if ( OneIdSettingsManager::is_age_verification_plus_enabled() ) {
			( new AddressPrePopulation() )->init();
		}
	}

	/**
	 * Render the oneid button JS snippet using the button renderer.
	 *
	 * @return void
	 */
	public function render_oneid_button() {
		try {
			$cart_contents = WC()->cart->get_cart_contents() ?? [];
			$scope = AgeRestrictionUtils::get_highest_age_restriction_scope_from_cart_contents( $cart_contents );

			$age = AgeRestrictionUtils::get_highest_age_restriction_from_cart_contents( $cart_contents );

			if ( '' === $scope ) {
				return;
			}

			if ( OneIdSettingsManager::is_age_verification_plus_enabled() ) {
				$scope .= ' ' . implode( ' ', self::AV_PLUS_SCOPES );
			} else {
				$scope .= ' ' . implode( ' ', self::AV_SCOPES );
			}

			// Check if the user has an age verification collection in their session and create one if not.
			if ( ! SessionManager::get_instance()->has( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY ) ) {
				SessionManager::get_instance()->set( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY, new AgeVerificationCollection() );
			}

			// Before we do anything, are there flash messages we need to show?
			$this->render_flash_messages();

			/**
			 * AgeVerificationCollection object.
			 *
			 * @var AgeVerificationCollection $age_verifications
			 */
			$age_verifications = SessionManager::get_instance()->get( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY );

			// Are they old enough?
			if ( $age_verifications->has_verified_age( $age ) ) {
				$this->render_success( $age );
				return;
			}

			// Only check to see if user has skipped if the option is enabled.
			if ( OneIdSettingsManager::is_age_verification_skip_enabled() ) {
				// if user has skipped or confirmed they want to skip and cart hasn't changed, don't show oneid button.
				if ( self::has_user_skipped() || ( self::has_user_confirmed() && ! self::has_cart_changed() ) ) {
					return;
				}
			}

			$this->button_renderer->render_button( [ $scope ] );
		} catch ( InvalidAgeRestrictionException $e ) {
			// Do nothing.
			return;
		}
	}

	/**
	 * Render any flash messages that may be present.
	 *
	 * @return void
	 */
	private function render_flash_messages() {
		$error_messages = OneIdFlashMessages::get_messages( 'error' );
		$info_messages = OneIdFlashMessages::get_messages( 'info' );
		$success_messages = OneIdFlashMessages::get_messages( 'success' );

		include __DIR__ . '/views/age-verification-messages.php';
	}

	/**
	 * Render success message.
	 *
	 * @param int $age_restriction_age Age of restriction for the cart.
	 *
	 * @return void
	 */
	private function render_success( int $age_restriction_age ) {
		include __DIR__ . '/views/age-verification-success.php';
	}

	/**
	 * Render skip button.
	 *
	 * @return void
	 */
	public static function render_oneid_skip_button_message() {
		try {
			$cart_contents = WC()->cart->get_cart_contents() ?? [];

			if ( ! $cart_contents ) {
				return;
			}

			$age = AgeRestrictionUtils::get_highest_age_restriction_from_cart_contents( $cart_contents );

			if ( -1 === $age ) {
				return;
			}

			if ( SessionManager::get_instance()->has( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY ) ) {
				// Check if the user has an age verification collection in their session.
				$age_verifications = SessionManager::get_instance()->get( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY );
				// If they are old enough don't show skip button.
				if ( $age_verifications->has_verified_age( $age ) ) {
					return;
				}
			}

			if ( ! OneIdSettingsManager::is_age_verification_skip_enabled() ) {
				include __DIR__ . '/views/checkout-age-restriction-disabled-order-button-message.php';
				return;
			}

			if ( self::has_user_skipped() ) {
				// user has skipped so we need to show confirmation message.
				include __DIR__ . '/views/checkout-age-restriction-skip-message.php';
			} elseif ( ! self::is_skip_cookie_set() || ( self::has_user_confirmed() && self::has_cart_changed() ) ) {
				// cookie is false / not set or cart has changed since verified, so show skip button.
				include __DIR__ . '/views/checkout-age-restriction-skip-button.php';
			}
		} catch ( InvalidAgeRestrictionException $e ) {
			// Do nothing.
			return;
		}

	}

	/**
	 * Render disabled place order button.
	 *
	 * @param string $button default WC place order button.
	 *
	 * @return string $button
	 */
	public static function render_place_order_button_html( string $button ): string {
		try {
			$cart_contents = WC()->cart->get_cart_contents() ?? [];

			if ( ! $cart_contents ) {
				return $button;
			}

			$age = AgeRestrictionUtils::get_highest_age_restriction_from_cart_contents( $cart_contents );

			if ( -1 === $age ) {
				return $button;
			}

			if ( SessionManager::get_instance()->has( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY ) ) {
				// Check if the user has an age verification collection in their session.
				$age_verifications = SessionManager::get_instance()->get( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY );
				// If they are old enough show place order button.
				if ( $age_verifications->has_verified_age( $age ) ) {
					return $button;
				}
			}

			// if user has confirmed they want to skip and cart hasn't changed, show default button.
			if ( OneIdSettingsManager::is_age_verification_skip_enabled() && self::has_user_confirmed() && ! self::has_cart_changed() ) {
				return $button;
			}

			return preg_replace( '#^<(button|input)([^\/\>]*)(\/)?>#', '<$1$2 disabled$3>', $button );
		} catch ( InvalidAgeRestrictionException $e ) {
			// Do nothing. Still show default button, so site isn't unusable.
			return $button;
		}
	}

	/**
	 * Check to see if the skip cookie is set.
	 *
	 * @return bool
	 */
	private static function is_skip_cookie_set(): bool {
		$skip_cookie = filter_var( ( $_COOKIE[ self::SKIP_COOKIE_ID ] ?? '' ), FILTER_SANITIZE_STRING ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE

		return '' !== $skip_cookie;
	}

	/**
	 * Check cookie to see if user has requested to skip age verification.
	 *
	 * @return bool
	 */
	private static function has_user_skipped(): bool {
		$skip_cookie = filter_var( ( $_COOKIE[ self::SKIP_COOKIE_ID ] ?? '' ), FILTER_SANITIZE_STRING ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE

		return self::SKIP_STATUS === $skip_cookie;
	}

	/**
	 * Check cookie to see if user has confirmed they wish to skip age verification.
	 *
	 * @return bool
	 */
	private static function has_user_confirmed(): bool {
		$skip_cookie = filter_var( ( $_COOKIE[ self::SKIP_COOKIE_ID ] ?? '' ), FILTER_SANITIZE_STRING ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE

		return $skip_cookie && self::SKIP_STATUS !== $skip_cookie;
	}

	/**
	 * Check cookie to see cart has changed since user confimed to skip age verification.
	 *
	 * @return bool
	 */
	private static function has_cart_changed(): bool {
		$skip_cookie = filter_var( ( $_COOKIE[ self::SKIP_COOKIE_ID ] ?? '' ), FILTER_SANITIZE_STRING ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE
		$cart_hash = filter_var( ( $_COOKIE[ self::CART_HASH_COOKIE_ID ] ?? '' ), FILTER_SANITIZE_STRING ); // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.cache_constraints___COOKIE

		return $skip_cookie !== $cart_hash;
	}
}
