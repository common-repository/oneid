<?php
/**
 * OneId Email Settings tab.
 * 
 * Used for adding age verification email setting in the WooCommerce Email settings section.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionEmail {

	public function init() {
		add_filter( 'woocommerce_email_classes', [ __CLASS__, 'register_age_verification_email' ] );
	}
	/**
	 * Register woocommerce email classes.
	 * 
	 * @param $emails WooCommerce emails array.
	 * @return array 
	 */
	public static function register_age_verification_email( $emails ) {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'AgeRestriction/Email/AgeRestrictionVerificationPendingEmail.php';
		$emails['WC_Email_Age_Restriction_Pending_Email'] = new \AgeRestrictionVerificationPendingEmail();
		return $emails;
	}

}
