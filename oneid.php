<?php
/**
 * OneID plugin for WordPress.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 * @author OneID Limited
 * @copyright 2021 OneID Limited
 * @license GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: OneID
 * Description: OneID - the official WordPress Plugin for OneID from OneID Limited.
 * Author: OneID Limited
 * Author URI: https://www.oneid.uk/
 * Version: 2.3.4
 * WC requires at least: 4.0.0
 * WC tested up to: 6.4.1
 * License: GPLv3+
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: oneid
 * Domain Path: /languages/
 * Requires PHP: 7.0
 * Requires at least: 5.0
 * Tested up to: 6.4.1
 */

declare ( strict_types=1 );

use DigitalIdentityNet\OneId\WordPress\Plugin\Activation;
use DigitalIdentityNet\OneId\WordPress\Plugin\FeatureManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdApp;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdCallbackFactory;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdCallbackHandler;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdSettingsManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Client\OpenIdConnectClientFactory;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	exit;
}

define( 'ONEID_VERSION', '2.3.4' );
define( 'ONEID_PREFIX', 'oneid' );

$oneid_plugin_base_url = plugin_dir_url( __FILE__ );
define( 'ONEID_PLUGIN_URL', $oneid_plugin_base_url );

/**
 * Make sure we can access the autoloader and it works.
 *
 * @return bool
 */
function oneid_autoload(): bool { // phpcs:ignore NeutronStandard.Globals.DisallowGlobalFunctions.GlobalFunctions
	$autoloader = __DIR__ . '/vendor/autoload.php';
	if ( file_exists( $autoloader ) ) {
		require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
	}

	return class_exists( OneIdApp::class );
}

/**
 * Add error message to admin if woocommerce plugin not activated.
 *
 * @return bool
 */
function oneid_admin_notice() {
	echo '<div class="error">Plugin deactivated. Please add and activate Woocommerce Plugin.</div>';
}

if ( ! oneid_autoload() ) {
	return;
}

if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
	include_once(ABSPATH.'wp-admin/includes/plugin.php');
}

if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	deactivate_plugins( 'oneid/oneid.php' );
	add_action ( 'admin_notices', 'oneid_admin_notice' );
	return;
}

register_activation_hook( __FILE__, [ Activation::class, 'activate' ] );

$app = new OneIdApp(
	new FeatureManager(),
	new OneIdCallbackHandler(
		new OneIdCallbackFactory(
			OpenIdConnectClientFactory::create_client(
				OneIdSettingsManager::get_current_provider_url(),
				OneIdSettingsManager::get_current_client_id(),
				OneIdSettingsManager::get_current_client_secret()
			)
		)
	)
);
$app->run();
