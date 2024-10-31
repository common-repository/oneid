<?php
/**
 * Interface that all features should adhere to.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

interface FeatureInterface {
	/**
	 * Get the label of the feature. Should be a slugified string, e.g. age_restriction.
	 *
	 * @return string
	 */
	public static function get_label(): string;

	/**
	 * Return the FQN class name of the callback used for this feature.
	 *
	 * @return string
	 */
	public static function get_callback_class(): string;

	/**
	 * Whether a feature is enabled or not.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool;

	/**
	 * Init method for the feature, used for calling hooks for that feature.
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Uninstall method for the feature, used for uninstalling any feature specific data.
	 *
	 * @return void
	 */
	public function uninstall();
}
