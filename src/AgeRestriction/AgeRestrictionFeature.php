<?php
/**
 * Age Restrictio feature class.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\FeatureInterface;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdButtonRenderer;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdSettingsManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\SessionManager;

final class AgeRestrictionFeature implements FeatureInterface {
	const FEATURE_LABEL = 'age_restriction';
	const FEATURE_CALLBACK_CLASS = AgeRestrictionCallback::class;

	/**
	 * Label for this feature.
	 *
	 * @return string
	 */
	public static function get_label(): string {
		return self::FEATURE_LABEL;
	}

	/**
	 * Name of class to use for the callback.
	 *
	 * @return string
	 */
	public static function get_callback_class(): string {
		return self::FEATURE_CALLBACK_CLASS;
	}

	/**
	 * Whether the age restriction feature is enabled.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return OneIdSettingsManager::is_age_verification_enabled();
	}

	/**
	 * Init method to call the various init methods across this feature.
	 *
	 * @return void
	 */
	public function init() {
		add_action(
			'woocommerce_loaded',
			function() {
				// Admin.
				( new AgeRestrictionTaxonomy() )->init();
				( new AgeRestrictionProductAdmin() )->init();
				( new AgeRestrictionOrderStatus() )->init();
				( new AgeRestrictionEmail() )->init();
				
				// Frontend.
				( new AgeRestrictionProduct() )->init();
				( new AgeRestrictionShortcode() )->init();
				( new AgeRestrictionCart() )->init();
				( new AgeRestrictionCheckout( new OneIdButtonRenderer( SessionManager::get_instance() ) ) )->init();
				( new AgeRestrictionOrder() )->init();
			}
		);
	}

	/**
	 * Uninstall method to remove any data added as part of age restriction.
	 *
	 * @return void
	 */
	public function uninstall() {
		( new AgeRestrictionUninstaller() )->uninstall();
	}
}
