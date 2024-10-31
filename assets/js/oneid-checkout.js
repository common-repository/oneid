/**
 * Javascript for OneID Checkout. Handles the saving of the checkout on progression to the OneID auth.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

window.addEventListener( // Needs to be window rather than document to allow the oneid button to load.
	'load',
	function () {
		const one_id_checkout = {
			click_handler: function( event ) {

				// Only add our beforeunload listener if the user has clicked the oneid button. This way regular back button presses or clicks away won't save the checkout.
				window.addEventListener( 'beforeunload', one_id_checkout.exit_page_handler );
			},

			exit_page_handler: function ( event ) {
				const oneid_button_pressed_on_checkout_event = new Event( 'oneid_button_pressed_on_checkout' );
				document.body.dispatchEvent( oneid_button_pressed_on_checkout_event );
			},

			save_checkout: function ( event ) {
				const form = document.querySelector( 'form.checkout' );
				if ( ! form ) {
					return;
				}

				// WooCommerce exposes checkout_url globally, so use that if poss, else fallback to common format.
				const checkout_url = wc_checkout_params.checkout_url || '/?wc-ajax=checkout';

				const request = new XMLHttpRequest();
				request.open( 'POST', checkout_url );
				const form_data = new FormData( form );
				// Need to set the woocommerce_checkout_update_totals field so WooCommerce doesn't try and process the order.
				form_data.append( 'woocommerce_checkout_update_totals', true );
				request.send( form_data );
			},

			init: function ( one_id_button ) {
				if ( one_id_button === null ) {
					return;
				}

				document.body.addEventListener( 'oneid_button_pressed_on_checkout', one_id_checkout.save_checkout );
				one_id_button.addEventListener( 'click', one_id_checkout.click_handler );
			}
		};

		// Use a resize observer as way of catching when the oneid button is present.
		const observer = new ResizeObserver(
			function( entries ) {
				const one_id = document.querySelector( 'one-id' );

				let one_id_shadow_root = null;
				if ( one_id ) {
					one_id_shadow_root = document.querySelector( 'one-id' ).shadowRoot;
				}

				let one_id_button = null;
				if ( one_id_shadow_root ) {
					one_id_button = one_id_shadow_root.querySelector( '.oneid-button' );
				}

				if ( one_id_shadow_root && one_id_button ) {
					one_id_checkout.init( one_id_button );
				}
			}
		);

		observer.observe( document.querySelector( "#oneid-button-wrapper" ) );
	}
);
