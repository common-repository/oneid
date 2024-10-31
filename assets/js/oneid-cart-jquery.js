/**
 * Javascript for OneID Cart.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

document.addEventListener(
	'DOMContentLoaded',
	function () {
		const one_id_cart = {

			remove_duplicate_notices: function () {
				const one_id_notices = document.querySelectorAll( '.oneid-notice-message' );
				const one_id_notices_length = one_id_notices.length;

				// Only need to remove duplicates if we have more than one.
				if ( one_id_notices_length < 2 ) {
					return;
				}

				// New notices get prepended, so we want to leave the top one intact.
				for ( let i = 0; i < one_id_notices_length; ++i ) {
					if ( i === 0 ) {
						continue;
					}
					one_id_notices[i].parentNode.removeChild( one_id_notices[i] );
				}
			},

			init: function () {
				// WooCommerce triggers the updated_wc_div event using jQuery, so we have to use jQuery to listen to it.
				if ( ! jQuery ) {
					return;
				}
				jQuery( document.body ).on( 'updated_wc_div', one_id_cart.remove_duplicate_notices );
			}
		};

		one_id_cart.init();
	}
);
