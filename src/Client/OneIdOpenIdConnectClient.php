<?php
/**
 * OneID OpenID Connect Client which includes support for requestAgeVerification().
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Client;

use OneId_Vendor\Jumbojett\OpenIDConnectClient;
use OneId_Vendor\Jumbojett\OpenIDConnectClientException;

class OneIdOpenIdConnectClient extends OpenIDConnectClient implements OneIdClientInterface {
	/**
	 * Holder for age verification response data.
	 *
	 * @var array
	 */
	protected $age_verification = [];

	/**
	 * Holder for user information response data.
	 *
	 * @var array
	 */
	protected $user_information = [];

	/**
	 * Request age verification from the OneId service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 * @throws OpenIDConnectClientException If the response is anything other than 200.
	 */
	public function request_age_verification( $attribute = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		$age_verification_endpoint = $this->getProviderConfigValue(
			'ageverification_endpoint',
			$this->getProviderURL() . '/ageverification'
		);

		// The accessToken has to be sent in the Authorization header.
		// Accept json to indicate response type.
		$headers = [
			"Authorization: Bearer {$this->accessToken}",
			'Accept: application/json',
		];

		$age_verification = json_decode( $this->fetchURL( $age_verification_endpoint, null, $headers ) );
		if ( $this->getResponseCode() !== 200 ) {
			throw new OpenIDConnectClientException( 'The communication to retrieve age verification has failed with status code ' . $this->getResponseCode() );
		}
		$this->age_verification = $age_verification;

		if ( null === $attribute ) {
			return $this->age_verification;
		}

		if ( property_exists( $this->age_verification, $attribute ) ) {
			return $this->age_verification->$attribute;
		}

		return null;
	}

	/**
	 * Request user info from the OneId service.
	 *
	 * @param string|null $attribute Attribute to pass if you wish to return a single value from the response.
	 *
	 * @return mixed|null
	 */
	public function request_user_info( $attribute = null ) { // phpcs:ignore NeutronStandard.Functions.TypeHint

		return $this->requestUserInfo( $attribute );

	}
}
