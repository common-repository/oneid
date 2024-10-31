<?php
/**
 * View for product age restriction notice.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestriction;

/**
 * The age restricted product should be passed through from where this file is included.
 *
 * @var $age_restriction AgeRestriction AgeRestriction instance.
 */
$age_restriction = $age_restricted_product->get_age_restriction();
if ( null === $age_restriction ) {
	return;
}
?>

<div class="woocommerce-info oneid-notice-message oneid-restricted-product-warning">
	<?php
	/* translators: %s: age restriction age, e.g. 18 */
	echo esc_html( sprintf( __( 'This is an age restricted product. You must be %s or older in order to purchase this item.', 'oneid' ), $age_restriction->get_age() ) );
	echo '<br />';
	echo esc_html( __( 'You will be able to verify your age using the OneID service from the Checkout page.', 'oneid' ) );
	echo sprintf( '<div class="oneid-learn-link"><a href="https://www.oneid.uk/individuals" target="_blank">' . esc_html__( 'Learn more about OneID', 'oneid' ) . '</a></div>');
	?>
</div>
