<?php
/**
 * View for cart age restriction notice.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );
?>

<div class="woocommerce-info oneid-notice-message oneid-restricted-cart-warning" role="alert">
	<?php
	/* translators: %s: age restriction age, e.g. 18 */
	echo esc_html( sprintf( __( 'This cart contains age restricted product(s). You must be aged %s years or older in order to complete this purchase.', 'oneid' ), $age ) );
	echo '<br />';
	echo esc_html( __( 'You will be able to verify your age using the OneID service from the Checkout page.', 'oneid' ) );
	echo sprintf( '<div class="oneid-learn-link"><a href="https://www.oneid.uk/individuals" target="_blank">' . esc_html__( 'Learn more about OneID', 'oneid' ) . '</a></div>');
	?>
</div>
