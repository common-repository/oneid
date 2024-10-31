<?php
/**
 * Interface for session storage.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Session;

interface OneIdSessionStorageInterface {
	const SESSION_NAMESPACE = ONEID_PREFIX . '_session_data';

	/**
	 * Check if session data exists.
	 *
	 * @param string $key Session key.
	 *
	 * @return bool
	 */
	public function has( string $key ): bool;

	/**
	 * Get session data.
	 *
	 * @param string     $key Session key.
	 * @param null|mixed $default Default value.
	 *
	 * @return mixed
	 */
	public function get( string $key, $default = null ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType

	/**
	 * Set session data.
	 *
	 * @param string $key Session key.
	 * @param mixed  $value Session value.
	 * @return void
	 */
	public function set( string $key, $value ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType

	/**
	 * Delete session data.
	 *
	 * @param string $key Session key.
	 * @return void
	 */
	public function delete( string $key );
}
