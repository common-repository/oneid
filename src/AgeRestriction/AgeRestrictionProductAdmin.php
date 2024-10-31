<?php
/**
 * Age Restriction Product admin setup.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionProductAdmin {
	/**
	 * Initialisaton for OneID AgeRestriction Product Admin.
	 *
	 * @return void
	 */
	public function init() {
		if ( ! is_admin() ) {
			return;
		}

		// Bulk / quick edit.
		add_action( 'bulk_edit_custom_box', [ __CLASS__, 'bulk_edit' ], 10, 2 );
		add_action( 'quick_edit_custom_box', [ __CLASS__, 'quick_edit' ], 10, 2 );
		add_action( 'save_post', [ __CLASS__, 'bulk_and_quick_edit_hook' ], 10, 2 );
		add_action( ONEID_PREFIX . '_product_bulk_and_quick_edit', [ __CLASS__, 'bulk_and_quick_edit_save_post' ], 10, 2 );
	}

	/**
	 * Bulk edit form for age restriction.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post type.
	 *
	 * @return void
	 */
	public static function bulk_edit( string $column_name, string $post_type ) {
		if ( 'taxonomy-' . AgeRestrictionTaxonomy::TAXONOMY_NAME !== $column_name || 'product' !== $post_type ) {
			return;
		}

		include __DIR__ . '/views/admin/bulk-edit-product.php';
	}

	/**
	 * Quick edit form for age restriction.
	 *
	 * @param string $column_name Column name.
	 * @param string $post_type Post type.
	 *
	 * @return void
	 */
	public static function quick_edit( string $column_name, string $post_type ) {
		if ( 'taxonomy-' . AgeRestrictionTaxonomy::TAXONOMY_NAME !== $column_name || 'product' !== $post_type ) {
			return;
		}

		include __DIR__ . '/views/admin/quick-edit-product.php';
	}

	/**
	 * Offers a way to hook into save post without causing an infinite loop
	 * when quick/bulk saving product info.
	 *
	 * @param int      $post_id Post ID being saved.
	 * @param \WP_Post $post Post object being saved.
	 */
	public static function bulk_and_quick_edit_hook( int $post_id, $post ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		remove_action( 'save_post', [ __CLASS__, 'bulk_and_quick_edit_hook' ] );
		do_action( ONEID_PREFIX . '_product_bulk_and_quick_edit', $post_id, $post );
		add_action( 'save_post', [ __CLASS__, 'bulk_and_quick_edit_hook' ], 10, 2 );
	}

	/**
	 * Quick and bulk edit saving.
	 *
	 * @param int      $post_id Post ID being saved.
	 * @param \WP_Post $post Post object being saved.
	 * @return int
	 */
	public static function bulk_and_quick_edit_save_post( int $post_id, $post ): int { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		$is_bulk_edit = ! empty( $_REQUEST[ ONEID_PREFIX . '_bulk_edit' ] );
		$is_quick_edit = ! empty( $_REQUEST[ ONEID_PREFIX . '_quick_edit' ] );

		// Only save here if doing a bulk or quick edit.
		if ( ! $is_bulk_edit && ! $is_quick_edit ) {
			return $post_id;
		}

		$bulk_or_quick = $is_quick_edit ? 'quick' : 'bulk';

		// Check nonce.
		$nonce = filter_input( INPUT_GET, ONEID_PREFIX . '_' . $bulk_or_quick . '_edit_nonce', FILTER_SANITIZE_STRING );

		// If no nonce in the GET super global, attempt POST.
		if ( null === $nonce ) {
			$nonce = filter_input( INPUT_POST, ONEID_PREFIX . '_' . $bulk_or_quick . '_edit_nonce', FILTER_SANITIZE_STRING );
		}

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, ONEID_PREFIX . '_' . $bulk_or_quick . '_edit_nonce' ) ) {
			return $post_id;
		}

		$age_restriction = (int) filter_input( INPUT_GET, AgeRestrictionTaxonomy::TAXONOMY_NAME, FILTER_VALIDATE_INT );

		// If no age restriction in the GET super global, attempt POST.
		if ( 0 === $age_restriction ) {
			$age_restriction = (int) filter_input( INPUT_POST, AgeRestrictionTaxonomy::TAXONOMY_NAME, FILTER_VALIDATE_INT );
		}

		// If value is -1 user has requested to remove age restriction.
		if ( -1 === $age_restriction ) {
			wp_set_post_terms( $post_id, [], AgeRestrictionTaxonomy::TAXONOMY_NAME );
		} elseif ( 0 !== $age_restriction ) {
			wp_set_post_terms( $post_id, [ (int) $age_restriction ], AgeRestrictionTaxonomy::TAXONOMY_NAME );
		}

		return $post_id;
	}
}
