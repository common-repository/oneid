<?php
/**
 * Age Restriction Taxonomy setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionTaxonomy {
	const TAXONOMY_NAME = ONEID_PREFIX . '_age_restriction';
	const WOOCOMMERCE_POST_TYPE = 'product';

	/**
	 * Initialisaton for OneID AgeRestriction Taxonomy.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ __CLASS__, 'add_age_taxonomy' ] );

		if ( is_admin() ) {
			add_action( 'add_meta_boxes', [ __CLASS__, 'age_restriction_meta_boxes' ] );
			add_filter( 'pre_insert_term', [ __CLASS__, 'validate_age_restriction' ], 10, 2 );
			add_filter( 'wp_insert_term_data', [ __CLASS__, 'set_slug_on_insert' ], 10, 2 );
			add_filter( 'wp_update_term_data', [ __CLASS__, 'set_slug_on_update' ], 10, 3 );
			add_action( 'admin_menu', [ __CLASS__, 'age_restriction_tab_hide' ] );
		}
	}

	/**
	 * Add age restriction taxonomy.
	 *
	 * @return void
	 */
	public static function add_age_taxonomy() {
		$labels = [
			'name' => __( 'Age restrictions', 'oneid' ),
			'singular_name' => __( 'Age restriction', 'oneid' ),
			'menu_name' => __( 'Age restrictions' ),
			'all_items' => __( 'All Age restrictions' ),
			'new_item_name' => __( 'New Age restriction' ),
			'add_new_item' => __( 'Add Age restriction' ),
			'edit_item' => __( 'Edit Age restriction' ),
			'update_item' => __( 'Update Age restriction' ),
			'separate_items_with_commas' => __( 'Separate Age restrictions with commas' ),
			'search_items' => __( 'Search Age restrictions' ),
			'add_or_remove_items' => __( 'Add or remove Age restrictions' ),
			'choose_from_most_used' => __( 'Choose from the most used Age restrictions' ),
		];

		$args = [
			'labels' => $labels,
			'hierarchical' => false,
			'public' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud' => true,
			'show_in_quick_edit' => false,
		];

		register_taxonomy( self::TAXONOMY_NAME, 'product', $args );
		register_taxonomy_for_object_type( self::TAXONOMY_NAME, 'product' );
	}

	/**
	 * Return the collection of active age restrictions.
	 *
	 * @return array
	 */
	public static function get_age_restrictions(): array {
		$terms = get_terms(
			[
				'taxonomy' => self::TAXONOMY_NAME,
				'hide_empty' => false,
			]
		);

		$age_restrictions = [];
		foreach ( $terms as $term ) {
			$age_restrictions[ $term->term_id ] = $term->name;
		}

		return $age_restrictions;
	}

	/**
	 * Convert age restriction tag option to a select dropdown.
	 *
	 * @return void
	 */
	public static function age_restriction_meta_boxes() {
		foreach ( get_taxonomies( [ 'show_ui' => true ], 'object' ) as $taxonomy ) {
			if ( self::TAXONOMY_NAME !== $taxonomy->name ) {
				continue;
			}

			foreach ( $taxonomy->object_type as $object_type ) {
				remove_meta_box( 'tagsdiv-' . $taxonomy->name, $object_type, 'side' );
				add_meta_box(
					'tagsdiv-' . $taxonomy->name,
					$taxonomy->labels->singular_name,
					self::class . '::age_restriction_meta_box',
					$object_type,
					'side',
					'default',
					[ 'taxonomy' => $taxonomy->name ]
				);
			}
		}
	}

	/**
	 * Output meta box for age restriction.
	 *
	 * @param \WP_Post $post The current post being edited.
	 * @param array    $box_args The args for the meta box.
	 *
	 * @return void
	 */
	public static function age_restriction_meta_box( \WP_Post $post, array $box_args ) {
		$args = $box_args['args'] ?? [];
		$taxonomy_name = $args['taxonomy'] ?? '';

		$taxonomy = get_taxonomy( $taxonomy_name );
		$disabled = ! current_user_can( $taxonomy->cap->assign_terms ) ? ' disabled="' . esc_attr( 'disabled' ) . '"' : '';

		if ( class_exists( 'WC_Product_Bundle' ) ) {
			$wc_product = wc_get_product( $post );
			if ( isset( $wc_product->product_type ) && $wc_product->product_type == 'bundle' ) {
				echo '<p>' . esc_html__( 'Age Restrictions should be set on product level, rather than on the product bundle.', 'oneid' ) . '</p>';
				return;
			}
		} 

		echo '<select name="tax_input[' . esc_attr( $taxonomy_name ) . ']"' . esc_attr( $disabled ) . '>';
		echo '<option value="">' . esc_html__( 'No Age Restriction', 'oneid' ) . '</option>';

		wp_terms_checklist(
			$post->ID,
			[
				'taxonomy' => $taxonomy->name,
				'selected_cats' => $post->ID,
				'walker' => new AgeRestrictionTaxonomyWalker(),
			]
		);

		echo '</select>';
	}

	/**
	 * Validate age restriction name is in the required format.
	 *
	 * @param string|\WP_Error $term The term name to add, or a WP_Error object if there's an error.
	 * @param string           $taxonomy Taxonomy slug.
	 *
	 * @return mixed|string|\WP_Error
	 */
	public static function validate_age_restriction( $term, string $taxonomy ) { // phpcs:ignore NeutronStandard.Functions.TypeHint
		if ( ! is_string( $term ) ) {
			return $term;
		}

		if ( self::TAXONOMY_NAME !== $taxonomy ) {
			return $term;
		}

		try {
			$age_restriction = AgeRestriction::from_name( $term );
		} catch ( InvalidAgeRestrictionException $e ) {
			return new \WP_Error(
				$e->getCode(),
				/* translators: %s: the reason age restriction is invalid */
				sprintf( __( 'Invalid age restriction: %s', 'oneid' ), $e->getMessage() )
			);
		}

		return $age_restriction->get_name();
	}

	/**
	 * Set the slug on the age restriction before insert to ensure it's on our required format.
	 *
	 * @param array  $data Term data to be inserted.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array
	 * @throws InvalidAgeRestrictionException If name of the tag is invalid.
	 */
	public static function set_slug_on_insert( array $data, string $taxonomy ): array {
		return self::set_slug( $data, $taxonomy );
	}

	/**
	 * Set the slug on the age restriction before update to ensure it's on our required format.
	 *
	 * @param array  $data Term data to be inserted.
	 * @param int    $term_id Term ID.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array
	 * @throws InvalidAgeRestrictionException If name of the tag is invalid.
	 */
	public static function set_slug_on_update( array $data, int $term_id, string $taxonomy ): array {
		return self::set_slug( $data, $taxonomy );
	}

	/**
	 * Set the slug on the age restriction to ensure it's on our required format.
	 *
	 * @param array  $data Term data to be inserted.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array
	 * @throws InvalidAgeRestrictionException If name of the tag is invalid.
	 */
	private static function set_slug( array $data, string $taxonomy ): array {
		if ( self::TAXONOMY_NAME !== $taxonomy ) {
			return $data;
		}

		// As slug is only hidden via CSS a modified slug could still be sent, so create a new one from the name.
		$age_restriction = AgeRestriction::from_name( $data['name'] );
		$data['slug'] = $age_restriction->get_slug();

		return $data;
	}

	/**
	 * Remove age restriction link from admin menu list.
	 *
	 * @return void
	 */
	public static function age_restriction_tab_hide() {
		remove_submenu_page( 'edit.php?post_type=' . self::WOOCOMMERCE_POST_TYPE, 'edit-tags.php?taxonomy=' . self::TAXONOMY_NAME . '&amp;post_type=' . self::WOOCOMMERCE_POST_TYPE );
	}
}
