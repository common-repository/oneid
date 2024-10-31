<?php
/**
 * Success notice for age verification.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );
?>

<div class="woocommerce-message oneid-notice-message oneid-age-verification-success-notice" role="alert">
	<?php
	/* translators: %s: age the user has to verify they are over */
	echo esc_html( sprintf( __( 'Success! You have successfully verified your age is over %s using OneID.', 'oneid' ), $age_restriction_age ) );
	?>
</div>
