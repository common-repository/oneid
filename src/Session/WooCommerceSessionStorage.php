<?php
/**
 * WooCommerce session adapter.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Session;

class WooCommerceSessionStorage implements OneIdSessionStorageInterface {
	const SESSION_TABLE = 'woocommerce_sessions';

	/**
	 * Singleton instance of the class.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * WooCommerce session object.
	 *
	 * @var \WC_Session|\WC_Session_Handler|null
	 */
	private $wc_session = null;

	/**
	 * Used for some basic memoisation of data.
	 *
	 * @var array
	 */
	private static $data = [];

	/**
	 * Get singleton instance of the class.
	 *
	 * @return $this
	 */
	public static function get_instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		WC()->initialize_session();
		$this->wc_session = WC()->session;
	}

	/**
	 * Does the session contain the given key?
	 *
	 * @param string $key Key of the session data to check.
	 *
	 * @return bool
	 */
	public function has( string $key ): bool {
		if ( empty( self::$data ) ) {
			self::$data = $this->wc_session->get( self::SESSION_NAMESPACE );
		}

		return isset( self::$data[ $key ] );
	}

	/**
	 * Get session value.
	 *
	 * @param string $key Key of the session data to retrieve.
	 * @param mixed  $default Default value in case the key is not found.
	 *
	 * @return mixed
	 */
	public function get( string $key, $default = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		if ( empty( self::$data ) ) {
			self::$data = $this->wc_session->get( self::SESSION_NAMESPACE );
		}

		return self::$data[ $key ] ?? $default;
	}

	/**
	 * Set session value.
	 *
	 * @param string $key Key of the session data to set.
	 * @param mixed  $value Value to set.
	 *
	 * @return void
	 */
	public function set( string $key, $value ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		if ( ! $this->has( $key ) || $this->get( $key ) !== $value ) {
			self::$data[ $key ] = $value;
		}
		$this->wc_session->set( self::SESSION_NAMESPACE, self::$data );
	}

	/**
	 * Delete session value.
	 *
	 * @param string $key Key of the session data to remove.
	 *
	 * @return void
	 */
	public function delete( string $key ) {
		$exists = $this->has( $key );

		if ( true === $exists ) {
			$data = $this->wc_session->get( self::SESSION_NAMESPACE );
			unset( $data[ $key ] );
			self::$data = $data;
			$this->wc_session->set( self::SESSION_NAMESPACE, $data );
		}
	}

	/**
	 * Clear the session data.
	 *
	 * @return void
	 */
	public function clear() {
		self::$data = [];
		$this->wc_session->__unset( self::SESSION_NAMESPACE );
	}

	/**
	 * Get customer ID.
	 *
	 * @return int|string
	 */
	public function get_customer_id() { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		return $this->wc_session->get_customer_id();
	}

	/**
	 * Returns the session.
	 *
	 * @param string|int $customer_id Customer ID.
	 * @param mixed      $default Default session value.
	 * @return string|array
	 */
	public function get_session( $customer_id, $default = false ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		$session = $this->wc_session->get_session( $customer_id, $default );

		return $session[ self::SESSION_NAMESPACE ] ?? $default;
	}
}
