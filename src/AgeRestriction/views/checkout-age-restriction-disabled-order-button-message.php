<?php
/**
 * View to display OneID message above disabled place order button.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );
?>

<span class="woocommerce-terms-and-conditions-checkbox-text oneid oneid-required">
	<?php echo esc_html( __( 'You must verify your age through One ID before you can place an order.', 'oneid' ) ); ?>
</span>
