<?php
/**
 * OneID Callback Interface, all callbacks should implement.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

interface OneIdCallback {
	/**
	 * Handler for the callback.
	 *
	 * @param string|null $code Code returned from OneID.
	 * @param string|null $state State returned from OneID that we passed initially.
	 *
	 * @return void
	 */
	public function handle( $code, $state ); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
}
