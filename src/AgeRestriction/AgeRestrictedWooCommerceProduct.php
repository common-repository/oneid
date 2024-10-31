<?php
/**
 * Decorator of WC_Product which adds AgeRestriction functionality.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictedWooCommerceProduct extends \WC_Product {
	/**
	 * Product type of the product we are decorating.
	 *
	 * @var string
	 */
	protected $product_type;

	/**
	 * Instance of our age restriction value object.
	 *
	 * @var AgeRestriction|null
	 */
	private $age_restriction = null;

	/**
	 * AgeRestrictedWooCommerceProduct constructor.
	 *
	 * @param \WC_Product $product Product to init.
	 *
	 * @throws InvalidAgeRestrictionException If the age restriction name is invalid.
	 */
	public function __construct( \WC_Product $product ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		$this->product_type = $product->get_type();
		parent::__construct( $product );

		$age_restrictions = get_the_terms( $this->get_id(), AgeRestrictionTaxonomy::TAXONOMY_NAME );

		if ( empty( $age_restrictions ) || $age_restrictions instanceof \WP_Error ) {
			return;
		}

		// Only one age restriction term can be applied to the product so grab the first one or exit it not present.
		$age_restriction = $age_restrictions[0] ?? null;
		if ( null === $age_restriction ) {
			return;
		}

		$this->set_age_restriction( AgeRestriction::from_name( $age_restriction->name ) );
	}

	/**
	 * Return the type stored from the product we are decorating.
	 *
	 * @return string
	 */
	public function get_type() { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		return $this->product_type ?? 'simple';
	}

	/**
	 * Set the AgeRestriction object.
	 *
	 * @param AgeRestriction $age_restriction AgeRestriction object.
	 *
	 * @return void
	 */
	public function set_age_restriction( AgeRestriction $age_restriction ) {
		$this->age_restriction = $age_restriction;
	}

	/**
	 * Get the age restriction for this product.
	 *
	 * @return AgeRestriction|null
	 */
	public function get_age_restriction() { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoReturnType
		return $this->age_restriction;
	}
}
