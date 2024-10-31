<?php
/**
 * Age verification messages.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );
?>

<?php foreach ( $error_messages as $error_message ) : ?>
	<div class="woocommerce-error oneid-notice-message" role="alert">
		<?php echo esc_html( $error_message ?? '' ); ?>
	</div>
<?php endforeach; ?>

<?php foreach ( $info_messages as $info_message ) : ?>
	<div class="woocommerce-info oneid-notice-message" role="alert">
		<?php echo esc_html( $info_message ?? '' ); ?>
	</div>
<?php endforeach; ?>

<?php foreach ( $success_messages as $success_message ) : ?>
	<div class="woocommerce-message oneid-notice-message" role="alert">
		<?php echo esc_html( $success_message ?? '' ); ?>
	</div>
<?php endforeach; ?>
