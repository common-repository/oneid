<?php
/**
 * Age Restriction Product frontend setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionProduct {
	/**
	 * Initialisaton for OneID AgeRestriction Product.
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * Hook: oneid_age_restriction_notice_show_on_product_page
		 *
		 * @param bool $show_notice Set to true to show age restriction on product page.
		 */
		$show_notice_on_product_page = apply_filters( ONEID_PREFIX . '_age_restriction_notice_show_on_product_page', false );

		if ( true === $show_notice_on_product_page ) {
			add_action(
				'woocommerce_single_product_summary',
				[ __CLASS__, 'output_product_age_restriction_notice' ],
				/**
				 * Hook: oneid_product_age_restriction_notice_priority
				 *
				 * @param int $priority Priority for the age restriction notice on products. Lower to move up where it is displayed, higher for lower.
				 */
				apply_filters( ONEID_PREFIX . '_product_age_restriction_notice_priority', 40 )
			);
		}
	}

	/**
	 * Output age restriction notice on the product if it has an age restriction applied.
	 *
	 * @return void
	 * @throws InvalidAgeRestrictionException If the name of the age restriction is invalid.
	 */
	public static function output_product_age_restriction_notice() {
		global $product;

		if ( class_exists( 'WC_Product_Bundle' ) && isset( $product->product_type ) && $product->product_type == 'bundle' ) {
			$ages = [];

			foreach ( $product->get_bundled_items() as $bundled_product ) {
				// For all products in the bundle, get the age restriction if there is one.
				if ( isset( $bundled_product->product ) && $bundled_product->product instanceof \WC_Product ) {
					$age_restriction = ( new AgeRestrictedWooCommerceProduct( $bundled_product->product ) )->get_age_restriction();
					if ( $age_restriction instanceof AgeRestriction ) {
						$ages[] = $age_restriction->get_age();
					}
				}
			}

			// Find the highest age restriction, this will be used by the product bundle age restriction notice template.
			$highest_age_restriction = max( $ages );
			include __DIR__ . '/views/product-bundle-age-restriction-notice.php';
		} else {
			$age_restricted_product = new AgeRestrictedWooCommerceProduct( $product );
			include __DIR__ . '/views/product-age-restriction-notice.php';
		}
	}
}
