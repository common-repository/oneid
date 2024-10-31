/**
 * Javascript & jQuery for OneID Checkout. Allows user to skip OneID verification and prompts for confirmation.
 * Some features require interaction with WC events created in jQuery.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

document.addEventListener(
	'DOMContentLoaded',
	function () {
		const readCookie = function( name ) {
			name += '=';
			let parts = document.cookie.split( /;\s*/ );
			let parts_length = parts.length;
			for ( i = 0; i < parts_length; i++ ) {
				if ( ! parts[i].indexOf( name ) ) {
					return parts[i].replace( name, '' );
				}
			}
		}

		const deleteCookie = function( name ) {
			document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
		}

		const one_id_skip_confirmation = {
			/**
			 * User has indicated they wish to skip age verification with OneID
			 *
			 * @param event event
			 *
			 * Set skip session cookie to 'skip'.
			 * remove OneID button.
			 * remove skip button.
			 * Trigger WC / jQuery update_checkout event to show confirmation message instead of button.
			 * WC / jQuery events are not accessible to vanilla JS - see init method.
			 */

			skip_handler: function( event ) {
				const one_id_skip_button = document.querySelector( '.oneid-skip-button' );

				document.cookie = "oneid-skip=skip;path=/";
				one_id_skip_confirmation.fadeOut( document.querySelector( 'one-id' ) );
				one_id_skip_confirmation.fadeOut( one_id_skip_button );
				jQuery( document.body ).trigger( 'update_checkout' );
			},

			/**
			 * User has confirmed they wish to skip age verification with OneID
			 *
			 * @param event event
			 *
			 * Set skip session cookie to cart_hash.
			 * We can check this for changes in the future (and remove to force re-verification).
			 * remove confirmation button & message.
			 * enable place order button.
			 * Trigger WC / jQuery update_checkout event to show confirmation message instead of button.
			 * WC / jQuery events are not accessible to vanilla JS - see init below.
			 */
			confirm_handler: function( event ) {
				const one_id_skip_confirm_message = document.querySelector( '.oneid-skip-confirm-message' );
				const woocommerce_cart_hash = readCookie( 'woocommerce_cart_hash' );
				const place_order_button = document.querySelector( '#place_order' );

				document.cookie = 'oneid-skip=' + woocommerce_cart_hash + ';path=/';
				one_id_skip_confirmation.fadeOut( one_id_skip_confirm_message );
				place_order_button.disabled = false;
				jQuery( document.body ).trigger( 'update_checkout' );
			},

			cancel_handler: function( event ) {
				// Delete cookie to reset state.
				deleteCookie( 'oneid-skip' );
				location.reload();
			},

			fadeOut: function ( element ) {
				// use CSS transitions to fade out element.
				element.style.transition = 'opacity 1s';
				element.addEventListener( 'transitionend', () => element.remove() );
				element.style.opacity = '0';
			},

			init: function () {
				const one_id = document.querySelector( 'one-id' );
				let one_id_shadow_root = null;
				if ( one_id ) {
					one_id_shadow_root = document.querySelector( 'one-id' ).shadowRoot;
				}

				let one_id_button = null;
				if ( one_id_shadow_root ) {
					one_id_button = one_id_shadow_root.querySelector( '.oneid-button' );
				}

				let one_id_button_is_visible = false;
				if ( one_id_button ) {
					one_id_button_is_visible = jQuery( one_id_button ).is( ':visible' );
				}

				// (re)initialise button vars as they are wiped out by WC ajax calls.
				const one_id_skip_button = document.querySelector( '.oneid-skip-button' );
				const one_id_skip_confirm_button = document.querySelector( '.oneid-skip-confirm-button' );
				const one_id_skip_cancel_button = document.querySelector( '.oneid-skip-cancel-button' );
				const place_order_button = document.querySelector( '#place_order' );
				// update cookie vars in case they have changed.
				const one_id_skip_cookie = readCookie( 'oneid-skip' );
				const woocommerce_cart_hash = readCookie( 'woocommerce_cart_hash' );

				// Don't do anything if place order button is not found.
				if ( ! place_order_button ) {
					return;
				}

				const one_id_skip_cancel_and_confirm_button_is_visible = one_id_skip_cancel_button && one_id_skip_confirm_button;

				// if no skip and confirm buttons exit early.
				// Otherwise disable place order button until skipped/confirmed.
				if ( ! one_id_skip_button && ! one_id_skip_confirm_button ) {
					return;
				} else {
					place_order_button.disabled = true;
				}

				// if the cart has changed since user confirmed skip verification, clear cookie and reload.
				let user_confirmed = one_id_skip_cookie && 'skip' !== one_id_skip_cookie;
				let cart_changed = woocommerce_cart_hash !== one_id_skip_cookie;
				if ( user_confirmed && cart_changed ) {
					deleteCookie( 'oneid-skip' );
					location.reload();
				}

				if (
					'skip' === one_id_skip_cookie &&
					one_id_skip_cancel_and_confirm_button_is_visible
				) {
					/**
					 * User is being asked to confirm, delete cookie so if they don't confirm,
					 * they will be prompted to skip again.
					 */
					deleteCookie( 'oneid-skip' );
				}

				if ( one_id_skip_button ) {
					one_id_skip_button.addEventListener( 'click', one_id_skip_confirmation.skip_handler );
				}

				if ( one_id_skip_cancel_button ) {
					one_id_skip_cancel_button.addEventListener( 'click', one_id_skip_confirmation.cancel_handler );
				}

				if ( one_id_skip_confirm_button ) {
					one_id_skip_confirm_button.addEventListener( 'click', one_id_skip_confirmation.confirm_handler );
				}
			}
		};

		one_id_skip_confirmation.init();

		/**
		 * WC reloads the place order button via ajax after updating the checkout so we need to re-call init.
		 * Event is triggered using jQuery, so jQuery must be used to listen for it as vanilla JS cannot access these events.
		 */
		jQuery( document.body ).on(
			'update_order_review update_checkout updated_checkout',
			function() {
				one_id_skip_confirmation.init();
			}
		);
	}
);
