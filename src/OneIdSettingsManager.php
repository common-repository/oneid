<?php
/**
 * Class for accessing various OneId settings.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\Client\OpenIdConnectClientFactory;
use OneId_Vendor\phpseclib\Crypt\AES;

class OneIdSettingsManager {
	const TEST_HOST = 'localhost:8000';

	/**
	 * Check if the plugin is enabled.
	 *
	 * @return bool
	 */
	public static function is_enabled(): bool {
		$host = filter_input( INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_STRING );
		$is_test_server = self::TEST_HOST === $host;

		$checks = [
			is_ssl() || $is_test_server,
			filter_var( get_option( ONEID_PREFIX . '_enable_oneid', '' ), FILTER_VALIDATE_BOOLEAN ),
		];

		return ! in_array( false, $checks, true );
	}

	/**
	 * Check if the plugin is set to use the production environment.
	 *
	 * @return bool
	 */
	public static function is_using_production_environment(): bool {
		return 'production' === get_option( ONEID_PREFIX . '_environment', '' );
	}

	/**
	 * Check if the plugin is set to use the sandbox environment.
	 *
	 * @return bool
	 */
	public static function is_using_sandbox_environment(): bool {
		return 'sandbox' === get_option( ONEID_PREFIX . '_environment', '' );
	}

	/**
	 * Get the current client id depending on whether sandbox or production option is selected.
	 *
	 * @return string
	 */
	public static function get_current_client_id(): string {
		if ( self::is_using_production_environment() ) {
			return self::get_production_client_id();
		}

		return self::get_sandbox_client_id();
	}

	/**
	 * Retrieve the client ID for the sandbox environment.
	 *
	 * @return string
	 */
	public static function get_sandbox_client_id(): string {
		return get_option( ONEID_PREFIX . '_sandbox_client_id', '' );
	}

	/**
	 * Retrieve the client secret for the sandbox environment.
	 *
	 * @return string
	 */
	public static function get_sandbox_client_secret(): string {
		add_filter( 'option_' . ONEID_PREFIX . '_sandbox_client_secret', [ __CLASS__, 'decrypt_option_on_retrieval' ] );
		return get_option( ONEID_PREFIX . '_sandbox_client_secret', '' );
	}

	/**
	 * Retrieve the client ID for the production environment.
	 *
	 * @return string
	 */
	public static function get_production_client_id(): string {
		return get_option( ONEID_PREFIX . '_production_client_id', '' );
	}

	/**
	 * Retrieve the client secret for the production environment.
	 *
	 * @return string
	 */
	public static function get_production_client_secret(): string {
		add_filter( 'option_' . ONEID_PREFIX . '_production_client_secret', [ __CLASS__, 'decrypt_option_on_retrieval' ] );
		return get_option( ONEID_PREFIX . '_production_client_secret', '' );
	}

	/**
	 * Get the current client secret depending on whether sandbox or production option is selected.
	 *
	 * @return string
	 */
	public static function get_current_client_secret(): string {
		if ( self::is_using_production_environment() ) {
			return self::get_production_client_secret();
		}

		return self::get_sandbox_client_secret();
	}

	/**
	 * Get the current provider url depending on whether sandbox or production option is selected.
	 *
	 * @return string
	 */
	public static function get_current_provider_url(): string {
		if ( self::is_using_production_environment() ) {
			return OpenIdConnectClientFactory::PROVIDER_URL_PRODUCTION;
		}

		return OpenIdConnectClientFactory::PROVIDER_URL_SANDBOX;
	}

	/**
	 * Check if age verification is enabled.
	 *
	 * @return bool
	 */
	public static function is_age_verification_enabled(): bool {
		return filter_var( get_option( ONEID_PREFIX . '_enable_age_verification', '' ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Check if age verification is enabled.
	 *
	 * @return bool
	 */
	public static function is_age_verification_plus_enabled(): bool {
		/**
		 * Age Verification Plus setting has been removed temporarily, but the
		 * code has been kept so it can be used at a later date. To re-enable,
		 * the setting just needs to be re-added and this function updated to
		 * use the setting.
		 */
		return false;
	}

	/**
	 * Check if option to skip age verification is enabled.
	 *
	 * @return bool
	 */
	public static function is_age_verification_skip_enabled(): bool {
		return filter_var( get_option( ONEID_PREFIX . '_enable_skip_age_verification', '' ), FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Called on an option being retrieved that we need to decrypt.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/option_option/
	 *
	 * @param string $value The encrypted value of the option we need to decrypt.
	 *
	 * @return string
	 */
	public static function decrypt_option_on_retrieval( string $value ): string {
		$cipher = new AES( AES::MODE_CTR );
		$matches = [];

		// The string is stored as "ONE_ID_REFIX_$iv_$ciphertext".
		$has_matches = preg_match( '/' . ONEID_PREFIX . '_(.+)_(.+)$/', $value, $matches );

		if ( ! $has_matches ) {
			return $value;
		}

		$iv = $matches[1];
		$ciphertext = $matches[2];

		$cipher->setIV( hex2bin( $iv ) );
		$cipher->setKey( AUTH_SALT );

		$decrypted_value = $cipher->decrypt( hex2bin( $ciphertext ) );

		return $decrypted_value;
	}
}
