<?php
/**
 * Interface for any clients used for communicating with the OneID service.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Client;

interface OneIdClientInterface {
	/**
	 * Authenticate with the OneId service.
	 *
	 * @return bool
	 */
	public function authenticate(); // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType;

	/**
	 * Request age verification from the OneId service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 */
	public function request_age_verification( $attribute = null ); // phpcs:ignore NeutronStandard.Functions.TypeHint

	/**
	 * Request user information from the OneId service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 */
	public function request_user_info( $attribute = null ); // phpcs:ignore NeutronStandard.Functions.TypeHint
}
