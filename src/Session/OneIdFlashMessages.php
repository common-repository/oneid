<?php
/**
 * Flash messages using our session manager.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Session;

class OneIdFlashMessages implements OneIdFlashMessagesInterface {
	const SESSION_KEY = ONEID_PREFIX . '_flash_messages';

	/**
	 * Array of messages stored within the session.
	 *
	 * @var array
	 */
	private static $messages = [];

	/**
	 * Add messages to the array of messages within the flash session.
	 *
	 * @param string $message Message to add.
	 * @param string $type Type of message, must be one of the MESSAGE_TYPES.
	 *
	 * @return void
	 * @throws \InvalidArgumentException If the message type is invalid.
	 */
	public static function add_message( string $message, string $type = self::DEFAULT_TYPE ) {
		if ( ! in_array( $type, self::MESSAGE_TYPES, true ) ) {
			throw new \InvalidArgumentException( 'Invalid message type' );
		}
		self::$messages[ $type ][] = $message;
		SessionManager::get_instance()->set( self::SESSION_KEY, self::$messages );
	}

	/**
	 * Return messages of a given type.
	 *
	 * @param string $type Type of message, must be one of the MESSAGE_TYPES.
	 *
	 * @return array
	 * @throws \InvalidArgumentException If the message type is invalid.
	 */
	public static function get_messages( string $type = self::DEFAULT_TYPE ): array {
		if ( ! in_array( $type, self::MESSAGE_TYPES, true ) ) {
			throw new \InvalidArgumentException( 'Invalid message type' );
		}
		self::$messages = SessionManager::get_instance()->get( self::SESSION_KEY, [] );
		$messages = self::$messages[ $type ] ?? [];
		unset( self::$messages[ $type ] );
		SessionManager::get_instance()->set( self::SESSION_KEY, self::$messages );
		return $messages;
	}
}
