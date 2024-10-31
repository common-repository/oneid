<?php
/**
 * Address Pre-Population based off response from OneId stored in our session.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\Checkout;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestrictionCallback;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\WooCommerceSessionStorage;

class AddressPrePopulation {
	const BILLING_FIELDS_MAP = [
		'billing_email' => 'email',
		'billing_first_name' => 'given_name',
		'billing_last_name' => 'family_name',
		'billing_address_1' => 'address.street_address',
		'billing_city' => 'address.locality',
		'billing_state' => 'address.region',
		'billing_postcode' => 'address.postal_code',
		'billing_country' => 'address.country',
		'billing_phone' => 'phone_number',
	];

	/**
	 * Store existing address data, so we can check if it's safe to pre-populate or not.
	 *
	 * @var array
	 */
	private $existing_address_data = [];

	/**
	 * AddressPrePopulation constructor.
	 */
	public function __construct() {
		// Initialise our existing address data.
		foreach ( $this->get_billing_address_fields() as $address_field ) {
			$this->existing_address_data[ $address_field ] = null;
		}
	}

	/**
	 * Initialisaton for Address Pre-Population of the Checkout.
	 *
	 * @return void
	 */
	public function init() {
		// It's important we use the following hooks non statically because we are storing state within the object.

		// Store initial address data.
		add_action( 'woocommerce_before_checkout_billing_form', [ $this, 'store_billing_fields' ] );

		// Pre-populate address fields.
		add_filter( 'default_checkout_billing_first_name', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_last_name', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_address_1', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_address_2', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_city', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_state', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_postcode', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_phone', [ $this, 'preload_billing_field' ], 10, 2 );
		add_filter( 'default_checkout_billing_email', [ $this, 'preload_billing_field' ], 10, 2 );
	}

	/**
	 * Check user session for user info from OneID and pre-fill billing details.
	 *
	 * @param mixed  $value Value for the billing field.
	 * @param string $field Name of the billing field.
	 *
	 * @return mixed $fields billing fields with placeholders added from oneid if available.
	 */
	public function preload_billing_field( $value, string $field ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		// Don't overwrite any already filled out data.
		if ( ! empty( $value ) ) {
			return $value;
		}

		// If we don't have a mapping for this field, do nothing.
		if ( ! isset( self::BILLING_FIELDS_MAP[ $field ] ) ) {
			return $value;
		}

		$user_info = WooCommerceSessionStorage::get_instance()->get( AgeRestrictionCallback::USER_INFO_SESSION_KEY );

		$user_info_field = self::BILLING_FIELDS_MAP[ $field ];

		if ( false === strpos( $user_info_field, 'address.' ) ) {
			$user_info_main_field = $user_info_field;
			$user_info_sub_field = null;
		} else {
			list( $user_info_main_field, $user_info_sub_field ) = explode( '.', $user_info_field );
		}

		if ( ! empty( $user_info_sub_field ) ) {
			// Only set the subfield if it's an address field, but no address data is already set.
			if ( array_key_exists( $field, $this->existing_address_data ) && empty( array_filter( $this->existing_address_data ) ) ) {
				return $user_info->$user_info_main_field->$user_info_sub_field ?? $value;
			}

			return $this->existing_address_data[ $field ] ?? $value;
		}

		return $user_info->$user_info_main_field ?? $value;
	}

	/**
	 * Store billing data on our object for use later on when setting the value.
	 *
	 * @param \WC_Checkout $checkout WooCommerce Checkout object.
	 *
	 * @return void
	 */
	public function store_billing_fields( \WC_Checkout $checkout ) {
		$address_fields = $this->get_billing_address_fields();

		// We want to get the field values without our hook intercepting, so temporarily turn off the hooks.
		foreach ( $address_fields as $field ) {
			remove_filter( 'default_checkout_' . $field, [ $this, 'preload_billing_field' ] );
		}

		foreach ( $checkout->get_checkout_fields( 'billing' ) as $field => $field_data ) {
			if ( in_array( $field, $address_fields, true ) ) {
				$this->existing_address_data[ $field ] = $checkout->get_value( $field );
			}
		}

		// Once we've set the address data, put the hooks back in.
		foreach ( $address_fields as $field ) {
			add_filter( 'default_checkout_' . $field, [ $this, 'preload_billing_field' ], 10, 2 );
		}
	}

	/**
	 * Filter our billing fields map to return only the address fields (not including country).
	 *
	 * @return array|int[]|string[]
	 */
	private function get_billing_address_fields(): array {
		$address_fields = array_keys(
			array_filter(
				self::BILLING_FIELDS_MAP,
				function ( string $oneid_field ): bool {
					// Only want the address fields.
					if ( false === strpos( $oneid_field, 'address.' ) ) {
						return false;
					}

					// We don't include country because we always map as they send us.
					if ( 'address.country' === $oneid_field ) {
						return false;
					}

					return true;
				}
			)
		);

		return $address_fields;
	}
}
