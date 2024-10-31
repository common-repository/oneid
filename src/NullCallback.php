<?php
/**
 * Null Callback. To handle situations where an adequate callback could not be identified.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

class NullCallback implements OneIdCallback {

	/**
	 * Handle for null callbacks, essentially do nothing.
	 *
	 * @param string|null $code Code returned from OneID.
	 * @param string|null $state State returned from OneID that we passed initially.
	 *
	 * @return void
	 */
	public function handle( $code, $state ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		// Do nothing.
	}
}
