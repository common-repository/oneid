<?php
/**
 * View to display skip OneID message.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );
?>

<span class="woocommerce-terms-and-conditions-checkbox-text oneid oneid-skip-confirm-message">
	<?php echo esc_html( __( 'Are you sure you want to continue without verifying your age through One ID? You will be required to provide proof of age through another means at a later stage', 'oneid' ) ); ?>
	<input type="button" name="oneid-skip-cancel-button" id="oneid-skip-cancel-button" class="button btn cancel oneid oneid-skip-cancel-button" value="<?php echo esc_attr( __( 'Cancel', 'oneid' ) ); ?>">
	<input type="button" name="oneid-skip-confirm-button" id="oneid-skip-confirm-button" class="button btn confirm oneid oneid-skip-confirm-button" value="<?php echo esc_attr( __( 'Yes, skip verification', 'oneid' ) ); ?>">
</span>
