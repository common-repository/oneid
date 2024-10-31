<?php
/**
 * Uninstaller for OneID plugin.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

use DigitalIdentityNet\OneId\WordPress\Plugin\FeatureManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdUninstaller;

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$autoloader = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $autoloader ) ) {
	require_once $autoloader; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingVariable
}

if ( ! class_exists( OneIdUninstaller::class ) ) {
	return;
}

if ( ! defined( 'ONEID_PREFIX' ) ) {
	define( 'ONEID_PREFIX', 'oneid' );
}

add_action(
	ONEID_PREFIX . '_uninstall',
	function () {
		$uninstaller = new OneIdUninstaller( new FeatureManager() );
		$uninstaller->uninstall();
	}
);

do_action( ONEID_PREFIX . '_uninstall' );
