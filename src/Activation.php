<?php
/**
 * Activation tasks for the OneID plugin.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestrictionTaxonomy;

class Activation {
	/**
	 * Run any tasks we need at activation.
	 *
	 * @return void
	 */
	public static function activate() {
		// Ensure taxonomy exists.
		if ( ! taxonomy_exists( AgeRestrictionTaxonomy::TAXONOMY_NAME ) ) {
			AgeRestrictionTaxonomy::add_age_taxonomy();
		}

		// Add default age restriction tags if not already added.
		foreach ( self::get_default_age_restrictions() as $age_restriction ) {
			// If on VIP, use the more performant function.
			$term_exists_function = function_exists( 'wpcom_vip_term_exists' ) ? 'wpcom_vip_term_exists' : 'term_exists';

			if ( ! $term_exists_function( $age_restriction, AgeRestrictionTaxonomy::TAXONOMY_NAME ) ) { // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
				wp_insert_term( $age_restriction, AgeRestrictionTaxonomy::TAXONOMY_NAME );
			}
		}

		// Add our rewrite rule and flush permalinks.
		OneIdCallbackHandler::add_callback_rewrite();
		flush_rewrite_rules(); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.flush_rewrite_rules_flush_rewrite_rules

	}

	/**
	 * Get default age restrictions, through translation.
	 *
	 * @return array
	 */
	public static function get_default_age_restrictions(): array {
		return [
			__( 'Age over 18', 'oneid' ),
		];
	}
}
