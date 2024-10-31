<?php
/**
 * Factory class for creating an OpenID Connect client.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Client;

class OpenIdConnectClientFactory {
	const PROVIDER_URL_SANDBOX = 'https://controller.sandbox.myoneid.co.uk';
	const PROVIDER_URL_PRODUCTION = 'https://controller.myoneid.co.uk';

	/**
	 * Create the client from the passed id and secret.
	 *
	 * @param string $provider_url The provider url.
	 * @param string $client_id The client ID.
	 * @param string $client_secret The client secret.
	 *
	 * @return OneIdClientInterface
	 */
	public static function create_client( string $provider_url, string $client_id, string $client_secret ): OneIdClientInterface {
		$client = new OneIdOpenIdConnectClient( $provider_url, $client_id, $client_secret );

		// Not strictly necessary but saves some http requests.
		$client->providerConfigParam(
			[
				'authorization_endpoint' => $provider_url . '/authorize',
				'token_endpoint' => $provider_url . '/token',
				'age_verification_endpoint' => $provider_url . '/ageverification',
			]
		);

		// Only in dev envs when debugging.
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$client->setVerifyHost( false );
			$client->setVerifyPeer( false );
		}

		return $client;
	}
}
