<?php
/**
 * Age Restriction Checkout frontend setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionOrderStatus {
	/*
	 * Name of the pending age verification order status. Cannot be bigger than 20 characters.
	 *
	 * @var string
	 */
	const PENDING_AGE_VERIFICATION_KEY_STRING = '-pending-av';
	const ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION = 'wc-' . ONEID_PREFIX . self::PENDING_AGE_VERIFICATION_KEY_STRING;

	/**
	 * Initialise age restriction order statuses.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'woocommerce_register_shop_order_post_statuses', [ __CLASS__, 'register_post_status' ] );
		add_filter( 'wc_order_statuses', [ __CLASS__, 'order_statuses' ] );
		add_filter( 'bulk_actions-edit-shop_order', [ __CLASS__, 'define_bulk_actions' ] );
		add_action( 'admin_action_mark_' . self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION, [ __CLASS__, 'handle_bulk_pending_age_verification' ] );
		add_action( 'admin_notices', [ __CLASS__, 'order_status_notices' ] );
		add_action( 'woocommerce_order_status_' . ONEID_PREFIX . self::PENDING_AGE_VERIFICATION_KEY_STRING . '_to_processing', [ __CLASS__, 'on_order_status_changed' ], 10 , 4);
		add_action( 'woocommerce_order_status_pending_to_' . ONEID_PREFIX . self::PENDING_AGE_VERIFICATION_KEY_STRING , [ __CLASS__, 'on_order_skip_age_verification' ], 10 , 4);
	}

	/**
	 * Add our custom post status to the list of available order post statuses.
	 *
	 * @param array $order_statuses Existing order post statuses.
	 *
	 * @return array
	 */
	public static function register_post_status( array $order_statuses ): array {
		// Pending Age Verification.
		$order_statuses[ self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION ] = [
			'label' => _x( 'Age Restricted Purchase: Age Verification Outstanding', 'Order status', 'oneid' ),
			'public' => false,
			'exclude_from_search' => false,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count' => _n_noop(
				'Age Restricted Purchase: Age Verification Outstanding <span class="count">(%s)</span>',
				'Age Restricted Purchase: Age Verification Outstanding <span class="count">(%s)</span>',
				'oneid'
			),
		];

		return $order_statuses;
	}

	/**
	 * Add our custom order status to the list of available order statuses.
	 *
	 * @param array $order_statuses Existing order statuses.
	 *
	 * @return array
	 */
	public static function order_statuses( array $order_statuses ): array {
		$statuses_to_add = [];
		if ( ! isset( $order_statuses[ self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION ] ) ) {
			$statuses_to_add[ self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION ] = _x( 'Age Restricted Purchase: Age Verification Outstanding', 'Order status', 'oneid' );
		}

		$new_order_statuses = $order_statuses;

		// Insert our new statuses after the 'pending' status.
		$processing_position = array_search( 'wc-pending', array_keys( $order_statuses ), true );
		if ( false !== $processing_position ) {
			$statuses_after_processing = array_splice( $order_statuses, $processing_position + 1 );
			$new_order_statuses = array_merge( $order_statuses, $statuses_to_add, $statuses_after_processing );
		}

		return $new_order_statuses;
	}

	/**
	 * Add a bulk action for marking orders as pending age verification.
	 *
	 * @param array $bulk_actions Array of existing bulk actions.
	 *
	 * @return array
	 */
	public static function define_bulk_actions( array $bulk_actions ): array {
		$bulk_actions[ 'mark_' . self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION ] = 'Change status to age-verification-pending';
		return $bulk_actions;
	}

	/**
	 * Handle bulk action for marking orders as pending age verification.
	 *
	 * @return void
	 */
	public static function handle_bulk_pending_age_verification() {
		$posted = filter_input_array(
			INPUT_GET,
			[
				'post' => [
					'filter' => FILTER_VALIDATE_INT,
					'flags' => FILTER_REQUIRE_ARRAY,
				],
			]
		) ?? [];

		// If not within the GET super global try the POST (maybe they have JS disabled?).
		if ( empty( $posted ) ) {
			$posted = filter_input_array(
				INPUT_POST,
				[
					'post' => [
						'filter' => FILTER_VALIDATE_INT,
						'flags' => FILTER_REQUIRE_ARRAY,
					],
				]
			) ?? [];
		}

		$order_ids = $posted['post'] ?? [];

		// If still nothing, return.
		if ( empty( $order_ids ) ) {
			return;
		}

		foreach ( $order_ids as $order_id ) {
			$order = new \WC_Order( $order_id );
			$order_note = __( 'Marked as pending age verification', 'oneid' );
			$order->update_status( self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION, $order_note, true );

		}

		$location = add_query_arg(
			[
				'post_type' => 'shop_order',
				'marked_' . self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION => 1,
				'changed' => count( $order_ids ),
				'ids' => implode( ',', $order_ids ),
				'post_status' => 'all',
			],
			'edit.php'
		);

		wp_safe_redirect( admin_url( $location ) );
		exit;
	}

	/**
	 * Add a notice to the admin if there are updated orders with the pending age verification status.
	 *
	 * @return void
	 */
	public static function order_status_notices() {
		global $pagenow, $typenow;

		$pending_marked = filter_input( INPUT_GET, 'marked_' . self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION, FILTER_VALIDATE_BOOLEAN ) ?? false;
		if ( false === $pending_marked ) {
			$pending_marked = filter_input( INPUT_POST, 'marked_' . self::ORDER_STATUS_NAME_PENDING_AGE_VERIFICATION, FILTER_VALIDATE_BOOLEAN ) ?? false;
		}

		if ( false === $pending_marked ) {
			return;
		}

		$changed = filter_input( INPUT_GET, 'changed', FILTER_VALIDATE_INT ) ?? 0;
		if ( 0 === $changed ) {
			$changed = filter_input( INPUT_POST, 'changed', FILTER_VALIDATE_INT ) ?? 0;
		}

		if ( 0 === $changed ) {
			return;
		}

		if ( 'shop_order' !== $typenow || 'edit.php' !== $pagenow ) {
			return;
		}

		/* translators: %d: orders count */
		$message = sprintf( _n( '%d order status changed.', '%d order statuses changed.', $changed, 'woocommerce' ), number_format_i18n( $changed ) );
		echo '<div class="updated"><p>' . esc_html( $message ) . '</p></div>';
	}

	/**
	 * Trigger email notification on order status change.
	 *
	 * @param int    $order_id Order id.
	 * @param object $order Order instance.
	 *
	 * @return void
	 */
	public static function on_order_status_changed( $order_id, $order ): void {
		
		/*
		* On skip age verification, when order status changed to processing from Age Restricted Purchase: Age Verification Outstanding.
		* Trigger WC default "WC_Email_New_Order" - An email to the admin when a new order status changed to processing.
		* Trigger WC default "WC_Email_Customer_Processing_Order" - An email to the customer when order status changed to processing.
		* By defautl new order email notification set to false, so have set "woocommerce_new_order_email_allows_resend" to true and then remove the filter.
		*/
		$wc_mailer = WC()->mailer();
		if ( $wc_mailer->emails['WC_Email_New_Order']->is_enabled() ) {
			add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );
			$wc_mailer->emails['WC_Email_New_Order']->trigger( $order_id, $order, true );
			remove_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );
		}

		if ( $wc_mailer->emails['WC_Email_Customer_Processing_Order']->is_enabled() ) {
			$wc_mailer->emails['WC_Email_Customer_Processing_Order']->trigger( $order_id );
		}
	}

	/**
	 * Trigger skip age verification email notification.
	 *
	 * @param int    $order_id Order id.
	 * @param object $order Order instance.
	 *
	 * @return void
	 */
	public static function on_order_skip_age_verification( $order_id, $order ): void {
		
		/*
		* On skip age verification, while placing the order.
		* Trigger custom email "WC_Email_Age_Restriction_Pending_Email" - An email to the admin when a new order placed by skipping age verification.
		*/
		$wc_mailer = WC()->mailer();
		if ( $wc_mailer->emails['WC_Email_Age_Restriction_Pending_Email']->is_enabled() ) {
			$wc_mailer->emails['WC_Email_Age_Restriction_Pending_Email']->trigger( $order_id );
		}
	}
}
