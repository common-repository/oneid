<?php
/**
 * Age Restriction uninstall class.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

use DigitalIdentityNet\OneId\WordPress\Plugin\Session\OneIdSessionStorageInterface;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\SessionManager;
use DigitalIdentityNet\OneId\WordPress\Plugin\Session\WooCommerceSessionStorage;

final class AgeRestrictionUninstaller {
	/**
	 * Uninstall method to remove any data added as part of age restriction.
	 *
	 * @return void
	 */
	public function uninstall() {
		// We need to make sure the taxonomy is registered in order to use the functions to get terms and taxonomy ids. It will get unregistered later on.
		if ( ! taxonomy_exists( AgeRestrictionTaxonomy::TAXONOMY_NAME ) ) {
			AgeRestrictionTaxonomy::add_age_taxonomy();
		}

		// Remove all associations between these terms and products, i.e. remove any age restrictions from products.
		$this->remove_age_restrictions_from_products();
		// Remove age restriction terms.
		$this->remove_age_restriction_terms();
		// Remove age restriction taxonomies.
		$this->remove_age_restriction_taxonomy();
		// Clear out any age verifications session data if the hook is enabled.
		/**
		 * Hook: oneid_uninstall_remove_age_verification_session_data
		 *
		 * @param bool $remove_age_verification_session_data Hook to use for removal of age verification session data on uninstall.
		 */
		if ( true === apply_filters( ONEID_PREFIX . '_uninstall_remove_age_verification_session_data', false ) ) {
			$this->remove_age_verification_session_data();
		}
	}

	/**
	 * Remove all associations between age restrictions and products.
	 *
	 * @return void
	 */
	private function remove_age_restrictions_from_products() {
		global $wpdb;

		// Find the term taxonomy ids.
		$term_taxonomy_ids = get_terms(
			[
				'taxonomy' => AgeRestrictionTaxonomy::TAXONOMY_NAME,
				'fields' => 'tt_ids',
			]
		);

		// If the taxonomy doesn't exist just bail.
		if ( $term_taxonomy_ids instanceof \WP_Error ) {
			return;
		}

		$escaped_term_taxonomy_ids = array_map(
			function ( int $tt_id ): string {
				return "'" . esc_sql( $tt_id ) . "'";
			},
			$term_taxonomy_ids
		);
		$imploded_term_taxonomy_ids = implode( ',', $escaped_term_taxonomy_ids );
		// Delete direct from db, as there could be a large number of products and hyrating each could be ill perfoming.
		$wpdb->query( "DELETE FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN ({$imploded_term_taxonomy_ids})" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Remove all age restriction terms.
	 *
	 * @return void
	 */
	private function remove_age_restriction_terms() {
		$terms = get_terms( [ 'taxonomy' => AgeRestrictionTaxonomy::TAXONOMY_NAME ] );
		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}
			wp_delete_term( $term->term_id, AgeRestrictionTaxonomy::TAXONOMY_NAME );
		}
	}

	/**
	 * Remove the age restriction taxonomy.
	 *
	 * @return void
	 */
	private function remove_age_restriction_taxonomy() {
		unregister_taxonomy_for_object_type( AgeRestrictionTaxonomy::TAXONOMY_NAME, 'product' );
		unregister_taxonomy( AgeRestrictionTaxonomy::TAXONOMY_NAME );
	}

	/**
	 * Remove the age verification session data.
	 *
	 * @return void
	 */
	private function remove_age_verification_session_data() {
		global $wpdb;

		$session_table = $wpdb->prefix . WooCommerceSessionStorage::SESSION_TABLE;

		foreach ( $this->get_sessions_iterator() as $sessions ) {
			foreach ( $sessions as $session ) {
				$session_value = maybe_unserialize( $session->session_value );

				if ( isset( $session_value[ OneIdSessionStorageInterface::SESSION_NAMESPACE ] ) ) {
					unset( $session_value[ OneIdSessionStorageInterface::SESSION_NAMESPACE ] );
					$session->session_value = maybe_serialize( $session_value );
					$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$session_table,
						[ 'session_value' => $session->session_value ],
						[ 'session_id' => $session->session_id ]
					);
				}
			}
		}
	}

	/**
	 * Get the sessions via an iterator using yield.
	 *
	 * @return \Iterator
	 */
	private function get_sessions_iterator(): \Traversable {
		$offset = 0;
		$limit = 100;
		while ( $sessions = $this->get_sessions( $offset, $limit ) ) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition
			yield $sessions;
			$offset += $limit;
		}
	}

	/**
	 * Get sessions with an offset and limit.
	 *
	 * @param int $offset Offset for the select query.
	 * @param int $limit Limit for the select query.
	 *
	 * @return array
	 */
	private function get_sessions( int $offset = 0, int $limit = 100 ): array {
		global $wpdb;
		$session_table = $wpdb->prefix . WooCommerceSessionStorage::SESSION_TABLE;
		return $wpdb->get_results( "SELECT session_id, session_value FROM {$session_table} LIMIT {$limit} OFFSET {$offset}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}
