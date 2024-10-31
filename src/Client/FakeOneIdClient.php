<?php
/**
 * Fake OneID client, useful for testing purposes.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Client;

use OneId_Vendor\Jumbojett\OpenIDConnectClientException;

class FakeOneIdClient implements OneIdClientInterface {
	/**
	 * Mock response code.
	 *
	 * @var int
	 */
	private $response_code;

	/**
	 * Json mock response for age verification.
	 *
	 * @var string
	 */
	private $age_verification_response;

	/**
	 * Mock user info.
	 *
	 * @var string
	 */
	private $user_info;

	/**
	 * Set a mock response code.
	 *
	 * @param int $reponse_code Response code.
	 *
	 * @return void
	 */
	public function set_response_code( int $reponse_code ) {
		$this->response_code = $reponse_code;
	}

	/**
	 * Set a mock json response for age verification.
	 *
	 * @param string $json Json mock response.
	 *
	 * @return void
	 */
	public function set_age_verification_response( string $json ) {
		$this->age_verification_response = $json;
	}

	/**
	 * Set mock user info.
	 *
	 * @param array $user_info Json mock response.
	 *
	 * @return void
	 */
	public function set_user_info( array $user_info ) {
		$this->user_info = $user_info;
	}

	/**
	 * Get the response code if one is set or fallback to a fake 200.
	 *
	 * @return int
	 */
	public function get_response_code() { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		return $this->response_code ?? 200;
	}

	/**
	 * Fake an authentication with the OneID service.
	 *
	 * @return bool
	 */
	public function authenticate() { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		return true;
	}

	/**
	 * Fake a request age verification from the OneID service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 * @throws OpenIDConnectClientException If response code is not a 200.
	 */
	public function request_age_verification( $attribute = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		if ( $this->get_response_code() !== 200 ) {
			throw new OpenIDConnectClientException( 'The communication to retrieve age verification has failed with status code ' . $this->get_response_code() );
		}

		if ( ! empty( $this->age_verification_response ) ) {
			return json_decode( $this->age_verification_response );
		}

		return null;
	}

	/**
	 * Fake a request user info from the OneId service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 */
	public function request_user_info( $attribute = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint

		if ( empty( $this->user_info ) ) {
			return null;
		}

		if ( null === $attribute ) {
			return $this->user_info;
		}

		if ( property_exists( $this->user_info, $attribute ) ) {
			return $this->user_info->$attribute;
		}

		return null;
	}
}
