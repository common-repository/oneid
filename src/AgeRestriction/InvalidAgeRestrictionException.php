<?php
/**
 * Invalid Age Restriction Exception class.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class InvalidAgeRestrictionException extends \Exception {
	const INVALID_AGE_RESTRICTION_CODE = 1;
}
