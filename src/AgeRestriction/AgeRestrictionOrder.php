<?php
/**
 * Age Restriction Order frontend setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\Admin\OneIdMetaFields;
use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification\AgeVerificationCollection;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\SessionManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\WooCommerceSessionStorage;

class AgeRestrictionOrder {
	/**
	 * Initialisaton for OneID AgeRestriction Order.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'woocommerce_checkout_order_processed', [ __CLASS__, 'on_order_processed' ], 10, 3 );
	}

	/**
	 * Handle the order processed event.
	 *
	 * @param int                $order_id Order ID.
	 * @param array              $posted_data Posted data.
	 * @param \WC_Abstract_Order $order The order, WC_Order or WC_Order_Refund.
	 *
	 * @return void
	 * @throws InvalidAgeRestrictionException Should an invalid age restriction exist.
	 */
	public static function on_order_processed( int $order_id, array $posted_data, \WC_Abstract_Order $order ) {
		if ( $order instanceof \WC_Order_Refund ) {
			return;
		}

		$cart_contents = $order->get_items() ?? [];
		$scope = AgeRestrictionUtils::get_highest_age_restriction_scope_from_cart_contents( $cart_contents );
		$age = AgeRestrictionUtils::get_highest_age_restriction_from_cart_contents( $cart_contents );
		$user_info = WooCommerceSessionStorage::get_instance()->get( AgeRestrictionCallback::USER_INFO_SESSION_KEY );

		// If we have no scope, there are no age restricted products, we don't need to do anything.
		if ( '' === $scope ) {
			return;
		}

		// If we've got this far we know the order contains age restriced products.
		$products_note = __( 'Age Restricted Product(s): ', 'oneid' ) . "\n";
		foreach ( AgeRestrictionUtils::get_age_restricted_products_from_cart_contents( $cart_contents ) as $age_restricted_product ) {
			/* translators: %1$s is the product name, %2$s is the age restriction in format Age over <number> */
			$products_note .= sprintf( __( '%1$s . Age Restriction: %2$s', 'oneid' ), $age_restricted_product->get_formatted_name(), $age_restricted_product->get_age_restriction()->get_name() );
			$products_note .= "\n";
		}

		/**
		 * AgeVerificationCollection object.
		 *
		 * @var AgeVerificationCollection $age_verifications
		 */
		$age_verifications = SessionManager::get_instance()->get( AgeRestrictionCallback::AGE_VERIFICATIONS_SESSION_KEY );

		// Can't proceed if the session does not contain an age verification collection.
		if ( ! $age_verifications instanceof AgeVerificationCollection ) {
			if ( function_exists( 'wc_get_logger' ) ) {
				$logger = wc_get_logger();
				$logger->error( 'AgeVerificationCollection not found in session for order ' . $order_id, [ 'source' => __FUNCTION__ ] );
			}
		}

		if ( $age_verifications->is_empty() || ! $age_verifications->has_age_verification( $age ) ) {
			/*
			 * Order status changed from Pending payment to Age Restricted Purchase: Age Verification Outstanding
			 * Age Restricted Order - Pending Age Verification: User has skipped age verification using the OneID service. Action required.
			 * Product(s): [Age Restricted Products]. Age Restriction: [Age Restriction]
			 */
			$order->update_status( AgeRestrictionOrderStatus::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION );
			$order->add_order_note( __( 'Age Restricted Order - Pending Age Verification: User has skipped age verification using the OneID service. Action required.', 'oneid' ) );
			$order->add_order_note( $products_note );

			$order->update_meta_data( OneIdMetaFields::AGE_RESTRICTION_VERIFICATION_STATUS_META_KEY, OneIdMetaFields::AGE_VERIFICATION_BYPASSED_STRING );
			$order->save_meta_data();
		} elseif ( $age_verifications->has_verified_age( $age ) ) {
			/*
			 * Order status changed from Pending payment to Processing
			 * Age Restricted Order - Age Verification Completed: User has verified their age using the OneID service.
			 * Product(s): [Age Restricted Products]. Age Restriction: [Age Restriction]
			 */
			$order->add_order_note( __( 'Age Restricted Order - Age Verification Completed: User has verified their age using the OneID service.', 'oneid' ) );
			$order->add_order_note( $products_note );

			$order->update_meta_data( OneIdMetaFields::AGE_RESTRICTION_VERIFICATION_STATUS_META_KEY, OneIdMetaFields::AGE_VERIFICATION_VERIFIED_STRING );

			if ( $user_info && $user_info->name ) {
				$order->update_meta_data( OneIdMetaFields::NAME_USED_FOR_VERIFICATION_META_KEY, $user_info->name );
			}

			$order->save_meta_data();
		} elseif ( ! $age_verifications->has_verified_age( $age ) ) {
			/*
			 * Order status changed from Pending payment to Age Restricted Purchase: Age Verification Outstanding
			 * Age Restricted Order - Pending Age Verification: User has attempted but failed age verification using the OneID service. Action required.
			 * Product(s): [Age Restricted Products]. Age Restriction: [Age Restriction]
			 */
			$order->update_status( AgeRestrictionOrderStatus::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION );
			$order->add_order_note( __( 'Age Restricted Order - Pending Age Verification: User has attempted but failed age verification using the OneID service. Action required.', 'oneid' ) );
			$order->add_order_note( $products_note );

			$order->update_meta_data( OneIdMetaFields::AGE_RESTRICTION_VERIFICATION_STATUS_META_KEY, OneIdMetaFields::AGE_VERIFICATION_FAILED_STRING );
			$order->save_meta_data();
		}

		// Finally, clear session data.
		WooCommerceSessionStorage::get_instance()->clear();
	}
}
