<?php
/**
 * OneID Settings.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare ( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin\Admin;

use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdCallbackHandler;
use DigitalIdentityNet\OneId\WordPress\Plugin\OneIdSettingsManager;
use OneId_Vendor\phpseclib\Crypt\AES;
use OneId_Vendor\phpseclib\Crypt\Random;

class OneIdSettings {
	const MENU_POSITION = 80;
	const OPTION_GROUP_NAME = ONEID_PREFIX . '_settings';

	/**
	 * OneIdSettings Initialization.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', [ __CLASS__, 'add_settings_menu_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'add_register_setting' ] );
		add_action( 'admin_notices', [ __CLASS__, 'display_notices' ] );

		// Encrypt client ID secrets on save.
		// Note that decryption filter is setup in OneIdApp as the setting is also retrieved in the front-end, whereas code here is admin only.
		add_filter( 'pre_update_option_' . ONEID_PREFIX . '_sandbox_client_secret', [ __CLASS__, 'encrypt_option_on_save' ] );
		add_filter( 'pre_update_option_' . ONEID_PREFIX . '_production_client_secret', [ __CLASS__, 'encrypt_option_on_save' ] );
	}

	/**
	 * Create OneId menu page.
	 *
	 * @return void
	 */
	public static function add_settings_menu_page() {
		add_menu_page(
			__( 'OneID', 'oneid' ),
			__( 'OneID', 'oneid' ),
			'manage_options',
			ONEID_PREFIX . '-settings',
			[ __CLASS__, 'add_menu_page_content' ],
			'dashicons-one-id-icon',
			self::MENU_POSITION
		);
	}

	/**
	 * Render settings page.
	 *
	 * @return void
	 */
	public static function add_menu_page_content() {
		echo '<div class="wrap">';
		echo '<div class="oneid-settings-header">';
		echo '<h1 class="wp-heading-inline">' . esc_html__( 'OneID', 'oneid' ) . '</h1>';
		echo '<img src="' . esc_url( ONEID_PLUGIN_URL . 'assets/img/logo.png' ) . '" alt="OneID logo" />';
		echo '</div>';
		echo '<hr class="wp-header-end" />';
		echo '<form method="post" action="options.php">';

		settings_fields( ONEID_PREFIX . '_settings' );
		do_settings_sections( 'oneid' );
		submit_button();

		echo '</form></div>';
	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public static function add_register_setting() {
		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_enable_oneid'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_environment'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_sandbox_client_id',
			'sanitize_text_field'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_sandbox_client_secret',
			'sanitize_text_field'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_production_client_id',
			'sanitize_text_field'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_production_client_secret',
			'sanitize_text_field'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_redirect_uri',
			'sanitize_text_field'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_enable_age_verification'
		);

		register_setting(
			self::OPTION_GROUP_NAME,
			ONEID_PREFIX . '_enable_skip_age_verification'
		);

		add_settings_section(
			self::OPTION_GROUP_NAME . '_section_id',
			'',
			'',
			'oneid'
		);

		// Enable OneID.
		add_settings_field(
			ONEID_PREFIX . '_enable_oneid',
			__( 'Enable OneID', 'oneid' ),
			[ __CLASS__, 'oneid_enable_oneid_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_enable_oneid',
				'class' => 'oneid-class',
			]
		);

		// Environment.
		add_settings_field(
			ONEID_PREFIX . '_environment',
			__( 'Select environment', 'oneid' ),
			[ __CLASS__, 'oneid_environment_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_environment',
				'class' => 'oneid-class',
			]
		);

		// Sandbox Client ID.
		add_settings_field(
			ONEID_PREFIX . '_sandbox_client_id',
			__( 'Sandbox Client ID', 'oneid' ),
			[ __CLASS__, 'oneid_sandbox_client_id_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_sandbox_client_id',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-sandbox-client-id',
			]
		);

		// Sandbox Client Secret.
		add_settings_field(
			ONEID_PREFIX . '_sandbox_client_secret',
			__( 'Sandbox Client Secret', 'oneid' ),
			[ __CLASS__, 'oneid_sandbox_client_secret_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_sandbox_client_secret',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-sandbox-client-secret ',
			]
		);

		// Production Client ID.
		add_settings_field(
			ONEID_PREFIX . '_production_client_id',
			__( 'Production Client ID', 'oneid' ),
			[ __CLASS__, 'oneid_production_client_id_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_production_client_id',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-production-client-id',
			]
		);

		// Production Client Secret.
		add_settings_field(
			ONEID_PREFIX . '_production_client_secret',
			__( 'Production Client Secret', 'oneid' ),
			[ __CLASS__, 'oneid_production_client_secret_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_production_client_secret',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-production-client-secret',
			]
		);

		// Redirect URI.
		add_settings_field(
			ONEID_PREFIX . '_redirect_uri',
			__( 'Redirect URL', 'oneid' ),
			[ __CLASS__, 'oneid_redirect_uri_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_redirect_uri',
				'class' => ONEID_PREFIX . '-class',
			]
		);

		// Enable Age Verification.
		add_settings_field(
			ONEID_PREFIX . '_enable_age_verification',
			__( 'Enable Age Verification', 'oneid' ),
			[ __CLASS__, 'oneid_enable_age_verification_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_enable_age_verification',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-checkbox-group ' . ONEID_PREFIX . '-field',
			]
		);

		// Allow users to skip age verification.
		add_settings_field(
			ONEID_PREFIX . '_enable_skip_age_verification',
			__( 'Allow Customers to Skip Age Verification', 'oneid' ),
			[ __CLASS__, 'oneid_skip_age_verification_field_html' ],
			'oneid',
			self::OPTION_GROUP_NAME . '_section_id',
			[
				'label_for' => ONEID_PREFIX . '_enable_skip_age_verification',
				'class' => ONEID_PREFIX . '-class ' . ONEID_PREFIX . '-checkbox-group ' . ONEID_PREFIX . '-field',
			]
		);
	}

	/**
	 * Render "Enable OneId" field.
	 *
	 * @return void
	 */
	public static function oneid_enable_oneid_field_html() {
		?>
		<input type="checkbox" name="<?php echo esc_attr( ONEID_PREFIX . '_enable_oneid' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_enable_oneid' ); ?>" value="1" <?php checked( true, OneIdSettingsManager::is_enabled(), true ); ?> />
		<?php
	}

	/**
	 * Render "Environment" field.
	 *
	 * @return void
	 */
	public static function oneid_environment_field_html() {
		$environment_option = OneIdSettingsManager::is_using_production_environment() ? 'production' : 'sandbox';

		?>
		<input type="radio" name="<?php echo esc_attr( ONEID_PREFIX . '_environment' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_environment_sandbox' ); ?>" value="sandbox" <?php checked( 'sandbox', $environment_option, true ); ?> />
		<label for="<?php echo esc_attr( ONEID_PREFIX . '_environment_sandbox' ); ?>"><?php echo esc_html( __( 'Sandbox', 'oneid' ) ); ?></label>
		&nbsp;
		<input type="radio" name="<?php echo esc_attr( ONEID_PREFIX . '_environment' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_environment_production' ); ?>" value="production" <?php checked( 'production', $environment_option, true ); ?> />
		<label for="<?php echo esc_attr( ONEID_PREFIX . '_environment_production' ); ?>"><?php echo esc_html( __( 'Production', 'oneid' ) ); ?></label>

		<div class="oneid_vendor_message oneid_sandbox_vendor_message hidden"><?php echo wp_kses_post( self::sandbox_vendor_message_description() ); ?></div>
		<div class="oneid_vendor_message oneid_production_vendor_message hidden"><?php echo wp_kses_post( self::production_vendor_message_description() ); ?></div>

		<?php
	}

	/**
	 * Render "Sandbox Client Id" field.
	 *
	 * @return void
	 */
	public static function oneid_sandbox_client_id_field_html() {
		?>
		<input type="text" name="<?php echo esc_attr( ONEID_PREFIX . '_sandbox_client_id' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_sandbox_client_id' ); ?>" value="<?php echo esc_attr( OneIdSettingsManager::get_sandbox_client_id() ); ?>" class="regular-text <?php echo esc_attr( ONEID_PREFIX . '-key-event-sandbox' ); ?>" />
		<?php
	}

	/**
	 * Render "Sandbox Client Secret" field.
	 *
	 * @return void
	 */
	public static function oneid_sandbox_client_secret_field_html() {
		?>
		<input type="password" name="<?php echo esc_attr( ONEID_PREFIX . '_sandbox_client_secret' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_sandbox_client_secret' ); ?>" value="<?php echo esc_attr( OneIdSettingsManager::get_sandbox_client_secret() ); ?>" class="regular-text <?php echo esc_attr( ONEID_PREFIX . '-key-event-sandbox' ); ?>" />
		<?php
	}

	/**
	 * Render "Production Client Id" field.
	 *
	 * @return void
	 */
	public static function oneid_production_client_id_field_html() {
		?>
		<input type="text" name="<?php echo esc_attr( ONEID_PREFIX . '_production_client_id' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_production_client_id' ); ?>" value="<?php echo esc_attr( OneIdSettingsManager::get_production_client_id() ); ?>" class="regular-text <?php echo esc_attr( ONEID_PREFIX . '-key-event-prod' ); ?>" />
		<?php
	}

	/**
	 * Render "Production Client Secret" field.
	 *
	 * @return void
	 */
	public static function oneid_production_client_secret_field_html() {
		?>
		<input type="password" name="<?php echo esc_attr( ONEID_PREFIX . '_production_client_secret' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_production_client_secret' ); ?>" value="<?php echo esc_attr( OneIdSettingsManager::get_production_client_secret() ); ?>" class="regular-text <?php echo esc_attr( ONEID_PREFIX . '-key-event-prod' ); ?>" />
		<?php
	}

	/**
	 * Render "Redirect URI" field.
	 *
	 * @return void
	 */
	public static function oneid_redirect_uri_field_html() {
		?>
		<code><?php echo esc_url( get_site_url() . '/' . OneIdCallbackHandler::CALLBACK_URL ); ?></code>
		<?php
	}

	/**
	 * Render "Enable Age Verification" field.
	 *
	 * @return void
	 */
	public static function oneid_enable_age_verification_field_html() {
		?>
		<input type="checkbox" name="<?php echo esc_attr( ONEID_PREFIX . '_enable_age_verification' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_enable_age_verification' ); ?>" value="1" <?php checked( true, OneIdSettingsManager::is_age_verification_enabled(), true ); ?> />
		<?php
	}

	/**
	 * Render "Allow Customers to Skip Age Verification" field.
	 *
	 * @return void
	 */
	public static function oneid_skip_age_verification_field_html() {
		?>
		<input type="checkbox" name="<?php echo esc_attr( ONEID_PREFIX . '_enable_skip_age_verification' ); ?>" id="<?php echo esc_attr( ONEID_PREFIX . '_enable_skip_age_verification' ); ?>" value="1" <?php checked( true, OneIdSettingsManager::is_age_verification_skip_enabled(), true ); ?> />
		<?php
	}

	/**
	 * Render production vendor message text.
	 *
	 * @return string
	 */
	public static function production_vendor_message_description(): string {

		$vendor_message = __( 'In order to configure your site’s integration with the OneID Production environment, you must <a href="mailto:askus@oneid.uk?subject=WooCommerce%20OneID%20Production%20Access%20Sign%20Up%20Request">contact us</a> to register in order to receive your Production Client ID and Secret.', 'oneid' );

		return $vendor_message;
	}

	/**
	 * Render sandbox vendor message text.
	 *
	 * @return $vendor_message
	 */
	public static function sandbox_vendor_message_description(): string {

        $vendor_message = __( 'In order to configure your site’s integration with the OneID Sandbox environment, you must <a href="mailto:askus@oneid.uk?subject=WooCommerce%20OneID%20Sandbox%20Access%20Sign%20Up%20Request">contact us</a> to register in order to receive your Sandbox Client ID and Secret.', 'oneid' );

		return $vendor_message;
	}

	/**
	 * Display notices to ensure both success and error notices are shown.
	 *
	 * @return void
	 */
	public static function display_notices() {
		settings_errors();
	}

	/**
	 * Called on an option being updated that we need to encrypt.
	 *
	 * @see https://developer.wordpress.org/reference/hooks/pre_update_option_option/
	 *
	 * @param string $new_value The value of the option we need to encrypt.
	 *
	 * @return string
	 */
	public static function encrypt_option_on_save( string $new_value ): string {
		// If no value has been entered, do not try to encrypt it.
		// When encrypting an empty string it can result in a very long string.
		// Each subsequent save of this empty string seems to result in a larger string, continually growing in size.
		if ( empty( $new_value ) ) {
			return $new_value;
		}

		$cipher = new AES( AES::MODE_CTR );
		$iv = Random::string( $cipher->getBlockLength() );

		$cipher->setIV( $iv );
		$cipher->setKey( AUTH_SALT );

		$ciphertext = $cipher->encrypt( $new_value );

		// Prefix the encrypted value with the IV so we can use it for decryption.
		return ONEID_PREFIX . '_' . bin2hex( $iv ) . '_' . bin2hex( $ciphertext );
	}
}
