/**
 * Javascript for OneID Admin settings page.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

document.addEventListener(
	"DOMContentLoaded",
	function () {

		const ONEID_PREFIX = ONEID.PREFIX;
		const ENABLE_ONEID_SELECTOR = ONEID_PREFIX + '_enable_oneid';
		const SANDBOX_VENDOR_MESSAGE_SELECTOR = '.' + ONEID_PREFIX + '_sandbox_vendor_message';
		const PROD_VENDOR_MESSAGE_SELECTOR = '.' + ONEID_PREFIX + '_production_vendor_message';
		const FIELDS_SELECTOR = '.' + ONEID_PREFIX + '-field';
		const ENVIRONMENT_SANDBOX_SELECTOR = ONEID_PREFIX + "_environment_sandbox";
		const ENVIRONMENT_PROD_SELECTOR = ONEID_PREFIX + "_environment_production";
		const PROD_CLIENT_ID_SELECTOR = '.' + ONEID_PREFIX + '-production-client-id';
		const PROD_CLIENT_SECRET_SELECTOR = '.' + ONEID_PREFIX + '-production-client-secret';
		const SANDBOX_CLIENT_ID_SELECTOR = '.' + ONEID_PREFIX + '-sandbox-client-id';
		const SANDBOX_CLIENT_SECRET_SELECTOR = '.' + ONEID_PREFIX + '-sandbox-client-secret';
		const SANDBOX_KEY_SELECTOR = '.' + ONEID_PREFIX + '-key-event-sandbox';
		const PROD_KEY_SELECTOR = '.' + ONEID_PREFIX + '-key-event-prod';
		const ONEID_SSL_NOTICE = '.' + ONEID_PREFIX + '-ssl-notice';
		const AV_SELECTOR = ONEID_PREFIX + '_enable_age_verification';
		const AV_PLUS_SELECTOR = ONEID_PREFIX + '_enable_age_verification_plus';
		const AV_SKIP_SELECTOR = ONEID_PREFIX + '_enable_skip_age_verification';

		// Environment radio buttons.
		const sandbox_environment_radio = document.getElementById( ENVIRONMENT_SANDBOX_SELECTOR );
		const production_environment_radio = document.getElementById( ENVIRONMENT_PROD_SELECTOR );

		// Production client details.
		const production_client_id = document.querySelector( PROD_CLIENT_ID_SELECTOR );

		const production_client_secret = document.querySelector( PROD_CLIENT_SECRET_SELECTOR );

		// Sandbox client details.
		const sandbox_client_id = document.querySelector( SANDBOX_CLIENT_ID_SELECTOR );
		const sandbox_client_secret = document.querySelector( SANDBOX_CLIENT_SECRET_SELECTOR );

		// Vendor message details.
		const sandbox_vendor_message = document.querySelector( SANDBOX_VENDOR_MESSAGE_SELECTOR );
		const production_vendor_message = document.querySelector( PROD_VENDOR_MESSAGE_SELECTOR );

		// Get selector of enable oneid.
		const enable_one_id = document.getElementById( ENABLE_ONEID_SELECTOR );
		const one_id_fields = document.querySelectorAll( FIELDS_SELECTOR + ' input' );

		// Event handler on key press.
		const sandbox_key_events = document.querySelectorAll( SANDBOX_KEY_SELECTOR );
		const production_key_events = document.querySelectorAll( PROD_KEY_SELECTOR );

		// Enable AV / AV+ / AV skip.
		const av = document.getElementById( AV_SELECTOR );
		const av_plus = document.getElementById( AV_PLUS_SELECTOR );
		const av_skip = document.getElementById( AV_SKIP_SELECTOR );

		if ( ! sandbox_environment_radio || ! production_environment_radio || ! enable_one_id) {
			return;
		}

		// Show/hide fields on selected environment changing.
		sandbox_environment_radio.addEventListener( "change", show_or_hide_environment_fields );
		production_environment_radio.addEventListener( "change", show_or_hide_environment_fields );

		// Change other setting on enable/disable oneid.
		enable_one_id.addEventListener( "change", toggle_settings_options );

		// Update AV+ when AV changes.
		av.addEventListener( "change", update_av_plus_skip_status );

		// Show/hide fields on page load.
		pre_flight_check();
		show_or_hide_environment_fields();
		toggle_settings_options();
		key_event_handler( sandbox_key_events );
		key_event_handler( production_key_events );
		update_av_plus_skip_status();

		/**
		 * Perform any pre-flight checks to see if the plugin can be used or not.
		 */
		function pre_flight_check() {
			if ( ! is_ssl_enabled() ) {
				disable_plugin();
			}
		}

		function is_ssl_enabled() {
			return [ null, false ].includes( document.querySelector( ONEID_SSL_NOTICE ) );
		}

		function show_or_hide_environment_fields() {
			if ( sandbox_environment_radio.checked ) {
				on_sandbox_environment_selected();
				return;
			}

			if ( production_environment_radio.checked ) {
				on_production_environment_selected();
				return;
			}
		}

		function on_sandbox_environment_selected() {
			hide_production_client_details();
			show_sandbox_client_details();
			toggle_sandbox_vendor_message();
		}

		function on_production_environment_selected() {
			hide_sandbox_client_details();
			show_production_client_details();
			toggle_production_vendor_message();
		}

		function show_production_client_details() {
			production_client_id.style.display = "table-row";
			production_client_secret.style.display = "table-row";
		}

		function hide_production_client_details() {
			production_client_id.style.display = "none";
			production_client_secret.style.display = "none";
		}

		function show_sandbox_client_details() {
			sandbox_client_id.style.display = "table-row";
			sandbox_client_secret.style.display = "table-row";
		}

		function hide_sandbox_client_details() {
			sandbox_client_id.style.display = "none";
			sandbox_client_secret.style.display = "none";
		}

		function toggle_sandbox_vendor_message() {

			production_vendor_message.style.display = "none";
			sandbox_vendor_message.style.display = "block";

			if ( check_field_if_empty( SANDBOX_CLIENT_ID_SELECTOR ) && check_field_if_empty( SANDBOX_CLIENT_SECRET_SELECTOR ) ) {
				hide_sandbox_vendor_message();
				return;
			}

		}

		function toggle_production_vendor_message() {
			sandbox_vendor_message.style.display = "none";
			production_vendor_message.style.display = "block";

			if ( check_field_if_empty( PROD_CLIENT_ID_SELECTOR ) && check_field_if_empty( PROD_CLIENT_SECRET_SELECTOR ) ) {
				hide_production_vendor_message();
				return;
			}

		}

		function toggle_settings_options() {
			if ( enable_one_id.checked ) {
				enable_settings();
				update_av_plus_skip_status();
				return;
			}

			disable_settings();
			update_av_plus_skip_status();
		}

		function disable_plugin() {
			enable_one_id.checked = false;
			enable_one_id.disabled = true;
		}

		function disable_settings() {
			one_id_fields.forEach(
				function( oneidField ){
					oneidField.disabled = true;
					oneidField.checked = false;
				}
			)
		}

		function enable_settings() {
			one_id_fields.forEach(
				function( oneidField ){
					oneidField.disabled = false;
				}
			)
		}

		function hide_sandbox_vendor_message() {
			sandbox_vendor_message.style.display = "none";
		}

		function hide_production_vendor_message() {
			production_vendor_message.style.display = "none";
		}

		function key_event_handler( eventSelectors ) {
			for ( const eventSelector of eventSelectors ) {
				eventSelector.addEventListener( 'keyup', show_or_hide_environment_fields );
			}
		}

		function check_field_if_empty( fieldSelector ) {

			const getInputFieldValue = document.querySelector( fieldSelector + ' input' ).value;
			if ( getInputFieldValue.trim() === '' ) {
				return false;
			}

			return true;
		}

		function update_av_plus_skip_status() {
			av_plus.disabled = false;
			av_skip.disabled = false;
			if ( ! av.checked ) {
				av_plus.checked = false;
				av_skip.checked = false;

				av_plus.disabled = true;
				av_skip.disabled = true;
			}
		}
	}
);
