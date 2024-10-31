<?php
/**
 * View for quick editing products for the age restriction field.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

use DigitalIdentityNet\OneId\WordPress\Plugin\AgeRestriction\AgeRestrictionTaxonomy;
?>
<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">

		<?php do_action( 'oneid_age_restriction_product_quick_edit_start' ); ?>

		<div class="inline-edit-group wp-clearfix">
			<label class="alignleft">
				<span class="title"><?php echo esc_html__( 'Age Restriction', 'oneid' ); ?></span>
				<select name="<?php echo esc_attr( AgeRestrictionTaxonomy::TAXONOMY_NAME ); ?>">
					<?php
					$options = [
						'' => __( '— No change —', 'oneid' ),
						'-1' => __( 'No Age Restriction', 'oneid' ),
					];

					$age_restrictions = AgeRestrictionTaxonomy::get_age_restrictions();

					if ( ! empty( $age_restrictions ) ) {
						foreach ( $age_restrictions as $term_id => $age_restriction ) {
							$options[ $term_id ] = esc_html( $age_restriction );
						}
					}

					foreach ( $options as $key => $value ) {
						echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
					}
					?>
				</select>`
			</label>
		</div>

		<?php do_action( 'oneid_age_restriction_product_quick_edit_end' ); ?>

		<input type="hidden" name="oneid_quick_edit" value="1" />
		<input type="hidden" name="oneid_quick_edit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'oneid_quick_edit_nonce' ) ); ?>" />
	</div>
</fieldset>
