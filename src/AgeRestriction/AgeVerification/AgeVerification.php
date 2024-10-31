<?php
/**
 * Value object for an AgeVerification.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification;

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestriction;

class AgeVerification {
	/**
	 * AgeRestriction instance.
	 *
	 * @var AgeRestriction
	 */
	private $age_restriction;

	/**
	 * Boolean as to whether the user is over the specified age or not.
	 *
	 * @var bool
	 */
	private $is_over_age;

	/**
	 * AgeVerification constructor.
	 *
	 * @param AgeRestriction $age_restriction AgeRestriction instance.
	 * @param bool           $is_over_age Boolean as to whether the user is over the specified age or not.
	 */
	private function __construct( AgeRestriction $age_restriction, bool $is_over_age ) {
		$this->age_restriction = $age_restriction;
		$this->is_over_age = $is_over_age;
	}

	/**
	 * Factory method to create an AgeVerification instance.
	 *
	 * @param AgeRestriction $age_restriction AgeRestriction instance.
	 * @param bool           $is_over_age Boolean as to whether the user is over the specified age or not.
	 *
	 * @return static
	 */
	public static function from_age_restriction_and_status( AgeRestriction $age_restriction, bool $is_over_age ): self {
		return new self( $age_restriction, $is_over_age );
	}

	/**
	 * Get the AgeRestriction instance.
	 *
	 * @return AgeRestriction
	 */
	public function get_age_restriction(): AgeRestriction {
		return $this->age_restriction;
	}

	/**
	 * Return the status of the age verification.
	 *
	 * @return bool
	 */
	public function is_verified(): bool {
		return $this->is_over_age;
	}
}
