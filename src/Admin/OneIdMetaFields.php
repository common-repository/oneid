<?php
/**
 * OneID Meta fields.
 * 
 * Used for storing information about the verification status on the WooCommerce order.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Admin;

class OneIdMetaFields {
	const AGE_RESTRICTION_VERIFICATION_STATUS_META_KEY = '_oneid_age_restriction_verification_status';
	const NAME_USED_FOR_VERIFICATION_META_KEY = '_oneid_verification_name';
	const AGE_VERIFICATION_VERIFIED_STRING = 'Verified';
	const AGE_VERIFICATION_FAILED_STRING = 'Failed';
	const AGE_VERIFICATION_BYPASSED_STRING = 'Bypassed';

	public function init() {
		add_action( 'woocommerce_admin_order_data_after_billing_address', [ __CLASS__, 'display_meta_fields' ] );
	}

	/**
	 * Display relevant read-only meta fields on the WooCommerce order page.
	 * 
	 * @param WC_Order $order WooCommerce order object.
	 * @return void 
	 */
	public static function display_meta_fields( $order ) {
		$name_used = self::get_verification_name_for_order( $order->get_id() );
		$verification_status = self::get_verification_status_for_order( $order->get_id() ) ?: 'N/A';
		$name_label = __( 'Name', 'oneid' );
		$verification_status_label = __( 'Verification status', 'oneid' );

		echo '<h3>' . __( 'OneID Verification Details', 'oneid' ) . '</h3>';
		if ( $name_used ) {
			echo "<p><strong>{$name_label}:</strong> " . esc_html($name_used) . '</p>';
		}
		echo "<p><strong>{$verification_status_label}:</strong> " . esc_html($verification_status) . '</p>';
	}

	/**
	 * Return the verification status for a given order ID.
	 * 
	 * @param int $order_id Order ID.
	 * @return mixed A verification status string if order is found, otherwise false.
	 */
	public static function get_verification_status_for_order( int $order_id ) {
		return get_post_meta( $order_id, OneIdMetaFields::AGE_RESTRICTION_VERIFICATION_STATUS_META_KEY, true );
	}

	/**
	 * Return the name used to verify with OneID.
	 * 
	 * @param int $order_id Order ID.
	 * @return mixed False if order not found, otherwise the value of the verification name meta field (which could be blank).
	 */
	public static function get_verification_name_for_order( int $order_id ) {
		return get_post_meta( $order_id, OneIdMetaFields::NAME_USED_FOR_VERIFICATION_META_KEY, true );
	}
}
