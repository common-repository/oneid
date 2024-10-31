<?php
/**
 * Age Restriction Value Object.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\Activation;

final class AgeRestriction {
	const NAME_FORMAT_REGEX = 'Age over \d';

	/**
	 * Name for the age restriction, e.g. Age over 18.
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Slug for the age restriction, e.g. age_over_18.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * Numeric value of the age for this age restriction.
	 *
	 * @var int
	 */
	private $age;

	/**
	 * Private Constructor, use the named constructor(s).
	 *
	 * @param string $name Name of the age restriction.
	 *
	 * @throws InvalidAgeRestrictionException If name if invalid.
	 */
	private function __construct( string $name ) {
		$this->assert_name_is_valid( $name );
		$this->name = $name;
		$this->slug = $this->slugify( $name );
		$this->age = $this->extract_age_from_name( $name );
	}

	/**
	 * Construct the AgeRestriction object from a name.
	 *
	 * @param string $name Name of the age restriction.
	 *
	 * @return static
	 * @throws InvalidAgeRestrictionException If name is invalid.
	 */
	public static function from_name( string $name ): self {
		return new self( trim( $name ) );
	}

	/**
	 * Construct the AgeRestriction object from a slug.
	 *
	 * @param string $slug Slug of the age restriction.
	 *
	 * @return static
	 * @throws InvalidAgeRestrictionException If name is invalid.
	 */
	public static function from_slug( string $slug ): self {
		$name = ucfirst( str_replace( '_', ' ', strtolower( trim( $slug ) ) ) );
		return new self( $name );
	}

	/**
	 * Get the name of the age restriction.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the slug of the age restriction.
	 *
	 * @return string
	 */
	public function get_slug(): string {
		return $this->slug;
	}

	/**
	 * Get the age of the age restriction as an integer.
	 *
	 * @return int
	 */
	public function get_age(): int {
		return $this->age;
	}

	/**
	 * Return string of the age verification, i.e. the name.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->name;
	}

	/**
	 * Assert if name is valid or throw exception if not.
	 *
	 * @param string $name Name to check.
	 *
	 * @throws InvalidAgeRestrictionException If name is invalid.
	 */
	private function assert_name_is_valid( string $name ) {
		if ( ! preg_match( '/' . self::NAME_FORMAT_REGEX . '/', $name ) ) {
			throw new InvalidAgeRestrictionException(
				'Must be in format "Age over {age}"',
				InvalidAgeRestrictionException::INVALID_AGE_RESTRICTION_CODE
			);
		}

		if ( ! in_array( $name, Activation::get_default_age_restrictions(), true ) ) {
			throw new InvalidAgeRestrictionException(
				'Age restriction not permitted',
				InvalidAgeRestrictionException::INVALID_AGE_RESTRICTION_CODE
			);
		}

		$age = $this->extract_age_from_name( $name );

		if ( $age < 1 ) {
			throw new InvalidAgeRestrictionException(
				'Age must be a number above 0',
				InvalidAgeRestrictionException::INVALID_AGE_RESTRICTION_CODE
			);
		}
	}

	/**
	 * Extract the age number from the name of the age restriction.
	 *
	 * @param string $name Name of the age restriction, should have been validated before this point.
	 *
	 * @return int
	 */
	private function extract_age_from_name( string $name ): int {
		return (int) filter_var( $name, FILTER_SANITIZE_NUMBER_INT );
	}

	/**
	 * Slugify the name.
	 *
	 * @param string $name The name of the age restriction, can be assumed to be valid at this point.
	 *
	 * @return string
	 */
	private function slugify( string $name ): string {
		return str_replace( ' ', '_', strtolower( $name ) );
	}
}
