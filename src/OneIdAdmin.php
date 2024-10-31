<?php
/**
 * Controller for OneID Admin App.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\Admin\OneIdMetaFields;
use DigitalIdentityNet\OneId\WordPress\Plugin\Admin\OneIdSettings;

class OneIdAdmin {
	/**
	 * Main run method for the OneID Admin app.
	 *
	 * @return void
	 */
	public function init() {
		( new OneIdSettings() )->init();
		( new OneIdMetaFields() )->init();

		add_action( 'admin_notices', [ __CLASS__, 'maybe_display_ssl_notice' ] );

		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_styles' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_scripts' ] );
	}

	/**
	 * Display a notice if the site is not using SSL.
	 *
	 * @return void
	 */
	public static function maybe_display_ssl_notice() {
		global $pagenow, $plugin_page;

		$is_our_page = 'admin.php' === $pagenow && 'oneid-settings' === $plugin_page;
		$is_ssl = is_ssl();
		// Note the following uses get_option() as home_url() will alter the url depending on is_ssl() status.
		$site_url_uses_https = strpos( get_option( 'siteurl' ), 'https://' ) === 0;
		$home_url_uses_https = strpos( get_option( 'home' ), 'https://' ) === 0;

		if ( $is_our_page && in_array( false, [ $is_ssl, $site_url_uses_https, $home_url_uses_https ], true ) ) {
			echo '<div class="notice notice-error oneid-ssl-notice"><p>' . esc_html__( 'This plugin is only available for sites using HTTPS. As your site is not using the TLS protocol, you are unable to use the OneID integration.', 'oneid' ) . '</p></div>';
		}
	}

	/**
	 * Enqueue any CSS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_styles() {
		wp_enqueue_style( ONEID_PREFIX . '-admin-styles', ONEID_PLUGIN_URL . 'assets/css/oneid-admin.css', [], ONEID_VERSION );
		wp_enqueue_style( ONEID_PREFIX . '-dashicons', ONEID_PLUGIN_URL . 'assets/css/oneid-icon.css', [], ONEID_VERSION );
	}

	/**
	 * Enqueue any JS needed for the admin.
	 *
	 * @return void
	 */
	public static function enqueue_admin_scripts() {
		wp_enqueue_script( ONEID_PREFIX . '-admin-script', ONEID_PLUGIN_URL . 'assets/js/oneid-admin-settings.js', [], ONEID_VERSION, true );

		$oneid_prefix = [ 'PREFIX' => ONEID_PREFIX ];
		wp_localize_script( ONEID_PREFIX . '-admin-script', 'ONEID', $oneid_prefix );
	}
}
