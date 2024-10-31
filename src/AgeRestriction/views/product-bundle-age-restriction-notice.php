<?php
/**
 * View for product bundle age restriction notice. This is for use on product bundles added by
 * WooCommerce Product Bundles.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

/**
 * The highest age restriction in the bundle should be passed through from where this file is included.
 *
 * @var $highest_age_restriction int Highest age restriction of all products in this bundle.
 */
if ( !isset( $highest_age_restriction ) || null === $highest_age_restriction ) {
	return;
}
?>

<div class="woocommerce-info oneid-notice-message oneid-restricted-product-warning">
	<?php
	/* translators: %s: age restriction age, e.g. 18 */
	echo esc_html( sprintf( __( 'This is an age restricted product. You must be %s or older in order to purchase this item.', 'oneid' ), $highest_age_restriction ) );
	echo '<br />';
	echo esc_html( __( 'You will be able to verify your age using the OneID service from the Checkout page.', 'oneid' ) );
	echo sprintf( '<div class="oneid-learn-link"><a href="https://www.oneid.uk/individuals" target="_blank">' . esc_html__( 'Learn more about OneID', 'oneid' ) . '</a></div>');
	?>
</div>
