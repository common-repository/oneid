<?php
/**
 * Uninstaller for OneID plugin.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

class OneIdUninstaller {
	/**
	 * FeatureManager instance.
	 *
	 * @var FeatureManager
	 */
	private $feature_manager;

	/**
	 * Constructor for OneIdApp.
	 *
	 * @param FeatureManager $feature_manager FeatureManager instance.
	 */
	public function __construct( FeatureManager $feature_manager ) {
		$this->feature_manager = $feature_manager;
	}

	/**
	 * Main uninstall method.
	 *
	 * @return void
	 */
	public function uninstall() {
		$this->feature_manager->uninstall_features();
		$this->delete_settings();
		wp_cache_flush();
		flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules
	}

	/**
	 * Delete settings.
	 *
	 * @return void
	 */
	private function delete_settings() {
		global $wpdb;

		$oneid_prefix = ONEID_PREFIX;
		$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '{$oneid_prefix}_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
