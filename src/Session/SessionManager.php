<?php
/**
 * In light of a DI container, use a simple manager to allow us to switch out the session driver easily.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Session;

class SessionManager {
	const CURRENT_DRIVER = 'woocommerce';

	/**
	 * Return an instance of our session storage depending on driver.
	 *
	 * @return OneIdSessionStorageInterface
	 */
	public static function get_instance(): OneIdSessionStorageInterface {
		switch ( self::CURRENT_DRIVER ) {
			case 'woocommerce':
			default:
				return WooCommerceSessionStorage::get_instance();
		}
	}
}
