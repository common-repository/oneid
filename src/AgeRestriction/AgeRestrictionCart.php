<?php
/**
 * Age Restriction Checkout frontend setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionCart {
	const DEFAULT_CART_HOOK = 'woocommerce_before_cart';

	/**
	 * Initialisaton for OneID AgeRestriction Checkout.
	 *
	 * @return void
	 */
	public function init() {
		/**
		 * Hook: oneid_age_restriction_notice_cart_hook
		 *
		 * @param string $hook Hook to use for display of the age restriction notice on the cart.
		 */
		$hook = apply_filters( ONEID_PREFIX . '_age_restriction_notice_cart_hook', self::DEFAULT_CART_HOOK );
		add_action( $hook, [ $this, 'output_cart_age_restriction_notice' ] );
	}

	/**
	 * Output the age restriction cart notice.
	 *
	 * @return void
	 * @throws InvalidAgeRestrictionException If cart contains a product with an invalid age restriction.
	 */
	public function output_cart_age_restriction_notice() {
		$cart_contents = WC()->cart->get_cart_contents() ?? [];
		$age = AgeRestrictionUtils::get_highest_age_restriction_from_cart_contents( $cart_contents );

		if ( -1 === $age ) {
			return;
		}

		include __DIR__ . '/views/cart-age-restriction-notice.php';
	}
}
