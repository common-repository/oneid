<?php
/**
 * Age restriction verification email class.
 *
 * @package DigitalIdentityNet\OneId\WordPress\Plugin
 */

declare( strict_types=1 );

class AgeRestrictionVerificationPendingEmail extends \WC_Email {
	const AGE_VERIFICATION_WC_EMAIL_PATH = __DIR__ . '/../views/emails';

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id             = 'age_verification_pending';
		$this->title          = __( 'Age Verification Pending', 'oneid' );
		$this->description    = __( 'Age Verification emails are sent to chosen recipient(s) when age verification has been skipped (if they were previously processing or on-hold).', 'oneid' );
		$this->template_html  = '/admin-age-verification-pending-order.php';
		$this->template_plain = '/plain/admin-age-verification-pending-order.php';
		$this->template_base  = AgeRestrictionVerificationPendingEmail::AGE_VERIFICATION_WC_EMAIL_PATH;
		$this->placeholders   = array(
			'{order_date}'              => '',
			'{order_number}'            => '',
			'{order_billing_full_name}' => '',
		);

		// Call parent constructor.
		parent::__construct();

		// Other settings.
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
		return __( 'Age Verification Skipped: Order #{order_number}', 'oneid' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
		return __( 'Age Verification Skipped: #{order_number}', 'oneid' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int            $order_id The order ID.
	 * @param WC_Order|false $order Order object.
	 */
	public function trigger( $order_id, $order = false ) {

		$this->setup_locale();

		if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
			$order = wc_get_order( $order_id );
		}

		if ( is_a( $order, 'WC_Order' ) ) {
			$this->object                                    = $order;
			$this->placeholders['{order_date}']              = wc_format_datetime( $this->object->get_date_created() );
			$this->placeholders['{order_number}']            = $this->object->get_order_number();
			$this->placeholders['{order_billing_full_name}'] = $this->object->get_formatted_billing_full_name();
		}

		if ( $this->is_enabled() && $this->get_recipient() ) {
			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		$this->restore_locale();
	}

	/**
	 * Get content html.
	 *
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => false,
				'email'              => $this,
			),
			'', 
			$this->template_base
		);
	}

	/**
	 * Get content plain.
	 *
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_plain,
			array(
				'order'              => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => true,
				'plain_text'         => true,
				'email'              => $this,
			),
			'', 
			$this->template_base
		);
	}

	/**
	 * Default content to show below main email content.
	 *
	 * @since 3.7.0
	 * @return string
	 */
	public function get_default_additional_content() {
		return __( 'Congratulations on the sale.', 'oneid' );
	}

	/**
	 * Initialise settings form fields.
	 */
	public function init_form_fields() {
		/* translators: %s: list of placeholders */
		$placeholder_text  = sprintf( __( 'Available placeholders: %s', 'oneid' ), '<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>' );
		$this->form_fields = array(
			'enabled'            => array(
				'title'   => __( 'Enable/Disable', 'oneid' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification', 'oneid' ),
				'default' => 'yes',
			),
			'recipient'          => array(
				'title'       => __( 'Recipient(s)', 'oneid' ),
				'type'        => 'text',
				/* translators: %s: admin email */
				'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'oneid' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'placeholder' => '',
				'default'     => '',
				'desc_tip'    => true,
			),
			'subject'            => array(
				'title'       => __( 'Subject', 'oneid' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_subject(),
				'default'     => '',
			),
			'heading'            => array(
				'title'       => __( 'Email heading', 'oneid' ),
				'type'        => 'text',
				'desc_tip'    => true,
				'description' => $placeholder_text,
				'placeholder' => $this->get_default_heading(),
				'default'     => '',
			),
			'additional_content' => array(
				'title'       => __( 'Additional content', 'oneid' ),
				'description' => __( 'Text to appear below the main email content.', 'oneid' ) . ' ' . $placeholder_text,
				'css'         => 'width:400px; height: 75px;',
				'placeholder' => __( 'N/A', 'oneid' ),
				'type'        => 'textarea',
				'default'     => $this->get_default_additional_content(),
				'desc_tip'    => true,
			),
			'email_type'         => array(
				'title'       => __( 'Email type', 'oneid' ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to send.', 'oneid' ),
				'default'     => 'html',
				'class'       => 'email_type wc-enhanced-select',
				'options'     => $this->get_email_type_options(),
				'desc_tip'    => true,
			),
		);
	}
}
