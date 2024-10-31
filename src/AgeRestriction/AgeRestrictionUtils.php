<?php
/**
 * Age Restriction Utility functions.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionUtils {
	/**
	 * Loop over cart contents and work out the highest age restriction.
	 *
	 * @param array $cart_contents Cart contents array.
	 *
	 * @return int
	 * @throws InvalidAgeRestrictionException If age restriction name is invalid.
	 */
	public static function get_highest_age_restriction_from_cart_contents( array $cart_contents ): int {
		$ages = [];
		foreach ( self::get_age_restricted_products_from_cart_contents( $cart_contents ) as $age_restricted_product ) {
			$age_restriction = $age_restricted_product->get_age_restriction();
			if ( $age_restriction instanceof AgeRestriction ) {
				$ages[] = $age_restriction->get_age();
			}
		}

		if ( [] === $ages ) {
			return -1;
		}

		return max( $ages );
	}

	/**
	 * Get all the age restricted products from the cart contents.
	 *
	 * @param array $cart_contents Cart contents array.
	 *
	 * @return AgeRestrictedWooCommerceProduct[]
	 * @throws InvalidAgeRestrictionException If age restriction name is invalid.
	 */
	public static function get_age_restricted_products_from_cart_contents( array $cart_contents ): array {
		$age_restricted_products = [];
		foreach ( $cart_contents as $cart_content ) {
			$wc_product = wc_get_product( $cart_content['product_id'] ?? false );
			if ( ! $wc_product instanceof \WC_Product ) {
				continue;
			}

			/**
			 * Ignore any 'bundle' products added by WooCommerce Product Bundles. Any products 
			 * which are contained in the bundle will also be in $cart_contents, so age restrictions 
			 * set on any of the products within the bundle will still be accounted for.
			 */
			if ( class_exists( 'WC_Product_Bundle' ) && isset( $wc_product->product_type ) && $wc_product->product_type == 'bundle' ) {
				continue;
			}

			$product = new AgeRestrictedWooCommerceProduct( $wc_product );
			$age_restriction = $product->get_age_restriction();
			if ( $age_restriction instanceof AgeRestriction ) {
				$age_restricted_products[] = $product;
			}
		}

		return $age_restricted_products;
	}

	/**
	 * Loop over cart contents and work out the highest age restriction and convert to its scope string.
	 *
	 * @param array $cart_contents Cart contents array.
	 *
	 * @return string
	 * @throws InvalidAgeRestrictionException If age restriction name is invalid.
	 */
	public static function get_highest_age_restriction_scope_from_cart_contents( array $cart_contents ): string {
		$age = self::get_highest_age_restriction_from_cart_contents( $cart_contents );

		if ( -1 === $age ) {
			return '';
		}

		return 'age_over_' . $age;
	}
}
