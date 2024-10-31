<?php
/**
 * The management and co-ordination of the main features of the plugin.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestrictionFeature;

class FeatureManager {
	/**
	 * List of all the feature FQCNs.
	 *
	 * @var AgeRestrictionFeature[]
	 */
	const FEATURES = [
		AgeRestrictionFeature::class,
	];

	/**
	 * Collection of features.
	 *
	 * @var FeatureInterface[]
	 */
	private $features = [];

	/**
	 * FeatureManager constructor.
	 */
	public function __construct() {
		$this->discover_features();
	}

	/**
	 * Loop over features and init them if they are enabled.
	 *
	 * @return void
	 */
	public function init_features() {
		foreach ( $this->features as $feature ) {
			if ( $feature->is_enabled() ) {
				$feature->init();
			}
		}
	}

	/**
	 * Loop over features and uninstall them.
	 *
	 * @return void
	 */
	public function uninstall_features() {
		foreach ( $this->features as $feature ) {
			$feature->uninstall();
		}
	}

	/**
	 * Check configured features implement the necessary interface and exist.
	 *
	 * @todo Use reflection to check if the class implements the interface.
	 * @return void
	 */
	private function discover_features() {
		foreach ( self::FEATURES as $feature ) {
			if ( class_exists( $feature ) ) {
				$feature_instance = new $feature(); // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
				if ( $feature_instance instanceof FeatureInterface ) {
					$this->features[] = $feature_instance;
				}
			}
		}
	}
}
