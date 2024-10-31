<?php
/**
 * Age Restriction Taxonomy Walker to turn tags input to a select dropdown options.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction;

class AgeRestrictionTaxonomyWalker extends \Walker {
	/**
	 * What the class handles.
	 *
	 * @var string
	 */
	public $tree_type = 'taxonomy_select';

	/**
	 * DB fields to use.
	 *
	 * @var string[]
	 */
	public $db_fields = [
		'parent' => 'parent',
		'id' => 'term_id',
	];

	/**
	 * Start the element output.
	 *
	 * @param string $output            Used to append additional content (passed by reference).
	 * @param object $object            The data object.
	 * @param int    $depth             Depth of the item.
	 * @param array  $args              An array of additional arguments.
	 * @param int    $current_object_id ID of the current item.
	 */
	public function start_el( &$output, $object, $depth = 0, $args = [], $current_object_id = 0 ) { // phpcs:ignore NeutronStandard.Functions.TypeHint.NoArgumentType
		$indent = str_repeat( "\t", $depth );

		$taxonomy = $args['taxonomy'] ?? '';
		$disabled = $args['disabled'] ?? false;
		$selected_cats = $args['selected_cats'] ?? [];

		$class = '';
		$output .= "\n<option id='{$taxonomy}-{$object->term_id}' $class value='" . esc_attr( $object->name ) . "'" .
				selected( in_array( $object->term_id, $selected_cats, true ), true, false ) .
				disabled( empty( $disabled ), false, false ) . ' /> ' . $indent .
				esc_html( apply_filters( 'the_category', $object->name ) ) . '</option>';
	}
}
