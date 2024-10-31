<?php
/**
 * A collection of AgeVerification objects. To be stored in the users session so we can keep track of what ages they have verified against.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeVerification;

class AgeVerificationCollection {
	const SORT_DESCENDING = 'desc';
	const SORT_ASCENDING = 'asc';

	/**
	 * AgeVerification collection.
	 *
	 * @var AgeVerification[]
	 */
	private $age_verifications = [];

	/**
	 * Add age verification to the collection.
	 *
	 * @param AgeVerification $age_verification AgeVerification to add.
	 *
	 * @return void
	 */
	public function add_age_verification( AgeVerification $age_verification ) {
		// Want non-strict comparison of objects here, see: https://www.php.net/manual/en/language.oop5.object-comparison.php.
		if ( in_array( $age_verification, $this->age_verifications ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return;
		}

		/**
		 * We don't want to end up with multiple age verifications for the same age restriction. We have to assume that a new
		 * age verification overwrites the old one. For example, if we have an age verification for 18+ which has failed verification
		 * should a new age verification for 18+ be added which has succeeded verification we want to replace the existing one rather
		 * than have two verifications for the same age restriction.
		 */
		foreach ( $this->age_verifications as $key => $existing_age_verification ) {
			// Want non-strict comparison of objects here, see: https://www.php.net/manual/en/language.oop5.object-comparison.php.
			if ( $existing_age_verification->get_age_restriction() == $age_verification->get_age_restriction() ) { // phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
				$this->age_verifications[ $key ] = $age_verification;
				return;
			}
		}

		// New age verification so we can just append it.
		$this->age_verifications[] = $age_verification;
	}

	/**
	 * Remove age verification from the collection.
	 *
	 * @param AgeVerification $age_verification AgeVerification to remove.
	 *
	 * @return void
	 */
	public function remove_age_verification( AgeVerification $age_verification ) {
		// Want non-strict comparison of objects here, see: https://www.php.net/manual/en/language.oop5.object-comparison.php.
		if ( ! in_array( $age_verification, $this->age_verifications ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return;
		}

		$key = array_search( $age_verification, $this->age_verifications ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict

		if ( false === $key ) {
			return;
		}

		unset( $this->age_verifications[ $key ] );
	}

	/**
	 * See if the collection contains the age verification for a specific age and that they have verified it.
	 *
	 * @param int $age Age to check.
	 *
	 * @return bool
	 */
	public function has_verified_age( int $age ): bool {
		foreach ( $this->get_ordered_age_verifications() as $age_verification ) {
			if ( $age_verification->get_age_restriction()->get_age() >= $age ) {
				return $age_verification->is_verified();
			}
		}

		return false;
	}

	/**
	 * See if the collection contains the age verification for a specific age or older. This can be used to work out
	 * if a user has skipped as opposed to failed age verification. Just returns whether an age verification exists not
	 * whether it has been verified or not. Use has_verified_age() to check if a user has verified an age verification.
	 *
	 * @param int $age Age to check.
	 *
	 * @return bool
	 */
	public function has_age_verification( int $age ): bool {
		foreach ( $this->get_ordered_age_verifications() as $age_verification ) {
			if ( $age_verification->get_age_restriction()->get_age() >= $age ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the age verifications.
	 *
	 * @return AgeVerification[]
	 */
	public function get_age_verifications(): array {
		return $this->age_verifications;
	}

	/**
	 * Get the age verifications ordered either ascending or descending by age.
	 *
	 * @param string $direction Direction to sort by, default descending.
	 *
	 * @return AgeVerification[]
	 * @throws \InvalidArgumentException Thrown if the direction is not valid.
	 */
	public function get_ordered_age_verifications( string $direction = self::SORT_DESCENDING ): array {
		if ( ! in_array( $direction, [ self::SORT_ASCENDING, self::SORT_DESCENDING ], true ) ) {
			throw new \InvalidArgumentException( 'Invalid direction provided.' );
		}

		$age_verifications = $this->get_age_verifications();

		usort(
			$age_verifications,
			function( AgeVerification $a, AgeVerification $b ) use ( $direction ): int {
				if ( $a->get_age_restriction()->get_age() === $b->get_age_restriction()->get_age() ) {
					return 0;
				}

				switch ( $direction ) {
					case self::SORT_ASCENDING:
						return $a->get_age_restriction()->get_age() < $b->get_age_restriction()->get_age() ? -1 : 1;
					case self::SORT_DESCENDING:
					default:
						return $a->get_age_restriction()->get_age() > $b->get_age_restriction()->get_age() ? -1 : 1;
				}
			}
		);

		return $age_verifications;
	}

	/**
	 * Is the collection of age verifiations empty?
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return 0 === count( $this->age_verifications );
	}
}
