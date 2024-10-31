<?php
/**
 * OneID Callback Factory used to determine which callback to call based off the label of the feature passed.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\Client\OneIdClientInterface;

class OneIdCallbackFactory {
	/**
	 * Client to communicate with OneID.
	 *
	 * @var OneIdClientInterface
	 */
	private $client;

	/**
	 * OneIdCallback constructor.
	 *
	 * @param OneIdClientInterface $client OneID client.
	 */
	public function __construct( OneIdClientInterface $client ) {
		$this->client = $client;
	}

	/**
	 * Create a callback based from the label of the feature passed.
	 *
	 * @param string $feature_label Label of the feature we are attempting to create a callback for.
	 *
	 * @return OneIdCallback
	 */
	public function create( string $feature_label ): OneIdCallback {
		foreach ( FeatureManager::FEATURES as $feature_class ) {
			if ( $feature_class::get_label() === $feature_label ) {
				$callback = $feature_class::get_callback_class();
				return new $callback( $this->client ); // phpcs:ignore NeutronStandard.Functions.VariableFunctions.VariableFunction
			}
		}

		// No callback has been found, return a null callback.
		return new NullCallback();
	}
}
