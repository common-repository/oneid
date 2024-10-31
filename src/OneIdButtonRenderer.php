<?php
/**
 * OneId Button Renderer, used to render the oneid.js button.
 *
 * @see https://docs.myoneid.co.uk/oneid-button.html
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

namespace DigitalIdentityNet\OneId\WordPress\Plugin;

use DigitalIdentityNet\OneId\WordPress\Plugin\Session\OneIdSessionStorageInterface;

final class OneIdButtonRenderer {
	const DEFAULT_SCOPE = [ 'openid' ];
	const ONEID_BUTTON_SRC = 'https://public-assets.myoneid.co.uk/latest/v2/oneid.js';

	/**
	 * WooCommerce session, required to get customer session id.
	 *
	 * @var OneIdSessionStorageInterface
	 */
	private $session;

	/**
	 * ButtonRenderer constructor.
	 *
	 * @param OneIdSessionStorageInterface $session Session object.
	 */
	public function __construct( OneIdSessionStorageInterface $session ) {
		$this->session = $session;
	}

	/**
	 * Render OneID Button.
	 *
	 * @param array $scope Scope to pass through to the OneID button.
	 *
	 * @return void
	 */
	public function render_button( array $scope = self::DEFAULT_SCOPE ) {
		$client_id = OneIdSettingsManager::get_current_client_id();
		$redirect_uri = home_url() . '/' . OneIdCallbackHandler::CALLBACK_URL;
		$one_id_base_url = esc_url( OneIdSettingsManager::get_current_provider_url() );
		$one_id_button_src = self::ONEID_BUTTON_SRC;
		$scope_as_string = implode( ' ', $scope );
		$environment = OneIdSettingsManager::is_using_sandbox_environment() ? 'sandbox' : 'production';
		$product = OneIdSettingsManager::is_age_verification_plus_enabled() ? 'age_verification_plus' : 'age_verification';

		$script = <<<JS
<div id="oneid-button-wrapper">
	<script>
		window.OneIDBaseURL = "{$one_id_base_url}";
	</script>
	<script
		src="{$one_id_button_src}"
		id="oneid-button"
		data-target="{$redirect_uri}"
		data-scope="{$scope_as_string}"
		data-product="{$product}"
		data-client-id="{$client_id}"
		data-style="default"
		data-environment="{$environment}"
	></script>
</div>
JS;

		echo wp_kses(
			$script,
			[
				'div' => [
					'id' => [],
				],
				'script' => [
					'src' => [],
					'id' => [],
					'data-target' => [],
					'data-scope' => [],
					'data-product' => [],
					'data-client-id' => [],
					'data-style' => [],
					'data-environment' => [],
				],
			]
		);
	}
}
