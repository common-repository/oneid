<?php
/**
 * Age Restriction Shortcode setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionShortcode {
	/**
	 * Initialise the shortcode.
	 *
	 * @return void
	 */
	public function init() {
		add_shortcode( ONEID_PREFIX . '_age_restriction_notice', [ __CLASS__, 'render_age_restriction_notice' ] );
	}

	/**
	 * Render the age restriction notice for the product.
	 * Will output nothing if used outside the context of a product or the product has no age restriction.
	 *
	 * @return string
	 * @throws InvalidAgeRestrictionException If the name of the age restriction is invalid.
	 */
	public static function render_age_restriction_notice(): string {
		if ( ! is_product() ) {
			return '';
		}

		ob_start();
		AgeRestrictionProduct::output_product_age_restriction_notice();
		return ob_get_clean();
	}
}
