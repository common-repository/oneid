<?php
/**
 * OneID Callback used for the callback from the OneId service.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestrictionFeature;

class OneIdCallbackHandler {
	const CALLBACK_URL = 'oneid-woocommerce-callback';
	const PAGE_NAME = self::CALLBACK_URL;
	const QUERY_VAR_CODE = 'code';
	const QUERY_VAR_STATE = 'state';

	/**
	 * Factory to allow us to create the necessary callback handler depending on feature we're calling back for.
	 *
	 * @var OneIdCallbackFactory
	 */
	private $callback_factory;

	/**
	 * OneIdCallback constructor.
	 *
	 * @param OneIdCallbackFactory $callback_factory OneID callback factory.
	 */
	public function __construct( OneIdCallbackFactory $callback_factory ) {
		$this->callback_factory = $callback_factory;
	}

	/**
	 * Initialise the OneID callback.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ __CLASS__, 'add_callback_rewrite' ] );
		add_action( 'wp_loaded', [ __CLASS__, 'flush_rewrite_rules' ] );
		add_filter( 'query_vars', [ __CLASS__, 'add_token_query_param' ] );
		add_filter( 'template_include', [ $this, 'add_callback_template' ] );
	}

	/**
	 * Add rewrite for the callback.
	 *
	 * @return void
	 */
	public static function add_callback_rewrite() {
		add_rewrite_rule(
			'^' . self::CALLBACK_URL . '[/]?$',
			'index.php?pagename=' . self::PAGE_NAME,
			'top'
		);
	}

	/**
	 * Flush rewrite rules if rewrite rule does not exists
	 *
	 * @return void
	 */
	public static function flush_rewrite_rules() {
		$rules = get_option( 'rewrite_rules' );
		if ( ! isset( $rules[ '^' . self::CALLBACK_URL . '[/]?$' ] ) ) { 
			global $wp_rewrite; 
			$wp_rewrite->flush_rules();
		}
	}

	/**
	 * Allow our custom query var for the callback.
	 *
	 * @param array $query_vars Array of existing query vars.
	 *
	 * @return array
	 */
	public static function add_token_query_param( array $query_vars ): array {
		$query_vars[] = self::QUERY_VAR_CODE;
		$query_vars[] = self::QUERY_VAR_STATE;
		return $query_vars;
	}

	/**
	 * Include template if we need or maybe just redirect to login page with a notice.
	 *
	 * @param string $template Template being included, will use if no token found.
	 *
	 * @return string
	 */
	public function add_callback_template( string $template ): string {
		global $wp_query;

		if ( self::PAGE_NAME === $wp_query->query_vars['pagename'] ) {
			$code = $wp_query->query_vars[ self::QUERY_VAR_CODE ];
			$state = $wp_query->query_vars[ self::QUERY_VAR_STATE ];

			if ( ! isset( $_SESSION['openid_connect_state'] ) ) {
				$_SESSION['openid_connect_state'] = $state; // phpcs:ignore WordPressVIPMinimum.Variables.RestrictedVariables.session___SESSION
			}

			// TODO: Find out a way to discern the type of callback we have, i.e. is it an age verification call or token?
			$this->callback_factory->create( AgeRestrictionFeature::get_label() )->handle( $code, $state );
		}

		return $template;
	}
}
