<?php
/**
 * Main Controller for OneID App.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

class OneIdApp {
	/**
	 * FeatureManager instance.
	 *
	 * @var FeatureManager
	 */
	private $feature_manager;

	/**
	 * Callback handler.
	 *
	 * @var OneIdCallbackHandler
	 */
	private $callback_handler;

	/**
	 * Constructor for OneIdApp.
	 *
	 * @param FeatureManager       $feature_manager FeatureManager instance.
	 * @param OneIdCallbackHandler $callback_handler Callback handler for the OneId service callback.
	 */
	public function __construct( FeatureManager $feature_manager, OneIdCallbackHandler $callback_handler ) {
		$this->feature_manager = $feature_manager;
		$this->callback_handler = $callback_handler;
	}

	/**
	 * Main run method for the OneID App.
	 *
	 * @return void
	 */
	public function run() {
		if ( is_admin() ) {
			$admin_app = new OneIdAdmin();
			$admin_app->init();
		}

		// Bail early if plugin is disabled, note we still need admin stuff even if disabled hence the above is not wrapped.
		if ( ! OneIdSettingsManager::is_enabled() ) {
			return;
		}

		$this->feature_manager->init_features();
		$this->callback_handler->init();

		if ( ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_styles' ] );
			add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_scripts' ] );
		}
	}

	/**
	 * Enqueue any CSS needed for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_styles() {
		wp_enqueue_style( ONEID_PREFIX . '-frontend-styles', ONEID_PLUGIN_URL . 'assets/css/oneid-frontend-min.css', [], ONEID_VERSION );
	}

	/**
	 * Enqueue any CSS needed for the frontend.
	 *
	 * @return void
	 */
	public static function enqueue_scripts() {
		if ( is_checkout() ) {
			wp_enqueue_script( ONEID_PREFIX . '-checkout', ONEID_PLUGIN_URL . 'assets/js/oneid-checkout-min.js', [], ONEID_VERSION, true );
			if ( OneIdSettingsManager::is_age_verification_skip_enabled() ) {
				wp_enqueue_script( ONEID_PREFIX . '-checkout-jquery', ONEID_PLUGIN_URL . 'assets/js/oneid-checkout-jquery-min.js', [ 'jquery' ], ONEID_VERSION, true );
			}
		}
		if ( is_cart() ) {
			wp_enqueue_script( ONEID_PREFIX . '-cart-jquery', ONEID_PLUGIN_URL . 'assets/js/oneid-cart-jquery-min.js', [ 'jquery' ], ONEID_VERSION, true );
		}
	}
}
