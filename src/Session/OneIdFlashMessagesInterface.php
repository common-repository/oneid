<?php
/**
 * Interface flash messaging within the OneID plugin.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Session;

interface OneIdFlashMessagesInterface {
	const MESSAGE_TYPES = [
		'info',
		'error',
		'success',
	];

	const DEFAULT_TYPE = 'info';

	/**
	 * Add messages to the array of messages within the flash session.
	 *
	 * @param string $message Message to add.
	 * @param string $type Type of message, must be one of the MESSAGE_TYPES.
	 *
	 * @return void
	 */
	public static function add_message( string $message, string $type = 'info' );

	/**
	 * Return messages of a given type.
	 *
	 * @param string $type Type of message, must be one of the MESSAGE_TYPES.
	 *
	 * @return array
	 */
	public static function get_messages( string $type = self::DEFAULT_TYPE ): array;
}
