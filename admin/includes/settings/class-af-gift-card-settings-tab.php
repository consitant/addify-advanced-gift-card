<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'Af_Gift_Card_Setting_Tab', false ) ) {
	return new Af_Gift_Card_Setting_Tab();
}

class Af_Gift_Card_Setting_Tab extends WC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'afgc_settings_tab';
		$this->label = __( 'Gift Card', 'addify_gift_cards' );
		add_action( 'woocommerce_admin_field_image_upload', array( $this, 'afgc_render_image_upload_field' ));
		parent::__construct();

		add_action( 'woocommerce_admin_field_wpeditor', array( $this, 'afgc_remind_me_mail' ) );

		add_filter( 'woocommerce_admin_settings_sanitize_option', array( $this, 'afgc_remind_me_mail_sanitization' ), 10, 3 );
	}

	public function get_own_sections() {

		return array(
			''                                => __( 'General', 'addify_gift_cards' ),
			'afgc_virtual_gift_card_setting'  => __( 'Virtual gift card', 'addify_gift_cards' ),
			'afgc_physical_gift_card_setting' => __( 'Physical gift card', 'addify_gift_cards' ),
			'afgc_pdf_setting'                => __( 'PDF', 'addify_gift_cards' ),
		);
	}

	public function afgc_remind_me_mail( $value ) {
		$option_value = get_option( $value['id'], $value['default'] );

		echo '<tr valign="top">';
		echo '<th scope="row" class="titledesc">';
		if ( ! empty( $value['title'] ) ) {
			echo '<label for="' . esc_attr( $value['id'] ) . '">' . esc_html( $value['title'] ) . '</label>';
		}
		echo '</th><td class="forminp forminp-textarea">';

		wp_editor(
			$option_value,
			$value['id'],
			array(
				'textarea_name' => $value['id'],
				'textarea_rows' => 8,
				'media_buttons' => true,
			)
		);

		if ( ! empty( $value['desc'] ) ) {
			echo '<p class="description">' . esc_html( $value['desc'] ) . '</p>';
		}

		echo '</td></tr>';
	}

	public function afgc_remind_me_mail_sanitization( $value, $option, $raw_value ) {
		if ( 'wpeditor' === $option['type'] ) {
			return wp_kses_post( $raw_value );
		}
		return $value;
	}

	/**
	 * Get settings for the detault section.
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {
		$settings =
			array(

				array(
					'title' => __( 'General', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_general_setting',
				),
				array(
					'title'         => __( 'Customize Gift Card Style', 'addify_gift_cards' ),
					'desc'          => __( 'Select this option to override the default theme and apply custom styling to gift cards.', 'addify_gift_cards' ),
					'id'            => 'afgc_customize_theme',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'   => __( 'Background', 'addify_gift_cards' ),
					'desc'    => __( 'Select background color.', 'addify_gift_cards' ),
					'id'      => 'afgc_pick_theme_color',
					'default' => '#15726b',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'title'   => __( 'Border', 'addify_gift_cards' ),
					'desc'    => __( 'Select border color.', 'addify_gift_cards' ),
					'id'      => 'afgc_pick_border_color',
					'default' => '#15726b',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'title'   => __( 'Text', 'addify_gift_cards' ),
					'desc'    => __( 'Select text color.', 'addify_gift_cards' ),
					'id'      => 'afgc_pick_text_color',
					'default' => '#fff',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'title'   => __( 'Background on hover', 'addify_gift_cards' ),
					'desc'    => __( 'Select background color on hover.', 'addify_gift_cards' ),
					'id'      => 'afgc_hover_theme_color',
					'default' => '#15726b',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'title'   => __( 'Border on hover', 'addify_gift_cards' ),
					'desc'    => __( 'Select border color on hover.', 'addify_gift_cards' ),
					'id'      => 'afgc_hover_border_color',
					'default' => '#15726b',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'title'   => __( 'Text on hover', 'addify_gift_cards' ),
					'desc'    => __( 'Select text color on hover.', 'addify_gift_cards' ),
					'id'      => 'afgc_hover_text_color',
					'default' => '#fff',
					'type'    => 'color',
					'css'     => 'width:80px;',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_general_setting',
				),

				array(
					'title' => __( 'Coupon Reminder Email', 'addify_gift_cards' ),
					'type'  => 'title',
					'id'    => 'afgc_reminder_email_settings',
				),
				array(
					'title'   => __( 'Reminder Email Message', 'addify_gift_cards' ),
					'desc'    => __( 'Add content to send reminder email before coupon expire.Use {customer_name}, {coupon_code}, {coupon_amount}, {coupon_remaining}, {coupon_expiry_date}, {coupon_products}.', 'addify_gift_cards' ),
					'id'      => 'afgc_gift_reminder_message',
					'type'    => 'wpeditor', // <-- custom field type
					'default' => __( 'Dear {customer_name}, you have received a gift card!', 'addify_gift_cards' ),
				),
				array(
					'type' => 'sectionend',
					'id'   => 'afgc_reminder_email_settings',
				),

				array(
					'title' => __( 'Coupon', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_coupon_setting',
				),

				array(
					'title'       => __( 'Coupon prefix', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a prefix for coupon.', 'addify_gift_cards' ),
					'id'          => 'afgc_coupon_prefix',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Add prefix here.', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_coupon_prefix',
					'desc_tip'    => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_coupon_setting',
				),

				array(
					'title' => __( 'Shortcodes', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_shortcodes_setting',
				),

				array(
					'title'       => __( 'Gift cards', 'addify_gift_cards' ),
					'desc'        => __( 'Display all gift cards using shortcode.', 'addify_gift_cards' ),
					'id'          => 'afgc_shortcodes',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( '[afgc-gift-cards]', 'addify_gift_cards' ),
					'default'     => '[afgc-gift-cards]',
					'autoload'    => false,
					'class'       => 'afgc_shortcodes',
					'desc_tip'    => false,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_shortcodes_setting',
				),
			);

		$settings = apply_filters( 'woocommerce_products_general_settings', $settings );

		return apply_filters( 'woocommerce_product_settings', $settings );
	}


	/**
	 * Get settings for Virtual Gift Card Section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afgc_virtual_gift_card_setting_section() {

		$settings =
			array(

				array(
					'title' => __( 'Gift Card Gallery', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_card_gallery',
				),

				array(
					'title'         => __( 'Enable gift cards gallery', 'addify_gift_cards' ),
					'desc'          => __( 'Enable gift cards gallery.', 'addify_gift_cards' ),
					'id'            => 'afgc_enable_card_gallery',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Title', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a title for this section.', 'addify_gift_cards' ),
					'id'          => 'afgc_card_gallery_section_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Gift Card Gallery', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_card_gallery_section_title',
					'desc_tip'    => 'If empty then default title is "Gift Card Gallery". ',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_card_gallery',
				),

				array(
					'title' => __( 'Delivery Section', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_gift_card_delivery_setting_virtual',
				),
				array(
					'title'       => __( 'Title', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a title for this section.', 'addify_gift_cards' ),
					'id'          => 'afgc_delivery_section_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Delivery', 'addify_gift_cards' ),
					'default'     => 'Delivery',
					'autoload'    => false,
					'class'       => 'afgc_delivery_section_title',
					'desc_tip'    => 'If empty then default title is "Delivery". ',
				),

				array(
					'title'       => __( '"Email" checkbox label', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a label for email checkbox.', 'addify_gift_cards' ),
					'id'          => 'afgc_email_checkbox_lable',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Email', 'addify_gift_cards' ),
					'default'     => 'Email',
					'autoload'    => false,
					'class'       => 'afgc_email_checkbox_lable',
					'desc_tip'    => 'If empty then default title is "Email". ',
				),

				array(
					'title'    => __( 'Required field', 'addify_gift_cards' ),
					'id'       => 'afgc_email_checkbox_is_required',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
					'desc'     => __( 'Check to make the email checkbox mandatory.', 'addify_gift_cards' ),
				),

				array(
					'title'       => __( '"Print At Home" checkbox label', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a label for print at home checkbox.', 'addify_gift_cards' ),
					'id'          => 'afgc_print_home_checkbox_lable',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Print at home', 'addify_gift_cards' ),
					'default'     => 'Print At Home',
					'autoload'    => false,
					'class'       => 'afgc_print_home_checkbox_lable',
					'desc_tip'    => 'If empty then default title is "Print at home". ',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_gift_card_delivery_setting_virtual',
				),

				array(
					'title' => __( 'Recipient Section', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_gift_card_recipient_setting_virtual',
				),

				array(
					'title'       => __( 'Title', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a title for recipient info section.', 'addify_gift_cards' ),
					'id'          => 'afgc_recipient_info_section_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Recipient Info', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_recipient_info_section_title',
					'desc_tip'    => 'If empty then default title is "Recipient Info". ',
				),

				array(
					'title'         => __( 'Enable another recipient', 'addify_gift_cards' ),
					'desc'          => __( 'Check to enable another recipient option.', 'addify_gift_cards' ),
					'id'            => 'afgc_another_recp',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Text for another recipient option.', 'addify_gift_cards' ),
					'desc'        => __( 'Enter text for the another recipient option.', 'addify_gift_cards' ),
					'id'          => 'afgc_another_recp_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Add another recipient', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_another_recp_title',
					'desc_tip'    => 'If empty then default title is "Add another recipient". ',
				),

				array(
					'title'    => __( 'Required field', 'addify_gift_cards' ),
					'id'       => 'afgc_name_input_is_required',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
					'desc'     => __( 'Check to make the full name field mandatory.', 'addify_gift_cards' ),
				),
				array(
					'title'    => __( 'Required field', 'addify_gift_cards' ),
					'id'       => 'afgc_email_input_is_required',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
					'desc'     => __( 'Check to make the email field mandatory.', 'addify_gift_cards' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_gift_card_recipient_setting_virtual',
				),

				array(
					'title' => __( 'Sender Section', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_gift_card_sender_setting_virtual',
				),

				array(
					'title'         => __( 'Enable sender section', 'addify_gift_cards' ),
					'desc'          => __( 'Check to enable sender section.', 'addify_gift_cards' ),
					'id'            => 'afgc_sender_name',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Title', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a title for sender section.', 'addify_gift_cards' ),
					'id'          => 'afgc_sender_info_section_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Sender Info', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_sender_info_section_title',
					'desc_tip'    => 'If empty then default title is "Sender Info". ',
				),

				array(
					'title'    => __( 'Required field', 'addify_gift_cards' ),
					'id'       => 'afgc_sender_message_is_required',
					'type'     => 'checkbox',
					'default'  => 'no',
					'autoload' => false,
					'desc'     => __( 'Check to make the message field mandatory.', 'addify_gift_cards' ),
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_gift_card_sender_setting_virtual',
				),

			);

		return apply_filters( 'afgc_virtual_gift_card_setting_settings', $settings );
	}


	/**
	 * Get settings for Physical Gift Card Section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afgc_physical_gift_card_setting_section() {

		$settings =
			array(

				array(
					'title' => __( 'Physical Gift Card', 'addify_gift_cards' ),
					'type'  => 'title',
					'css'   => 'margin-top:45px;',
					'desc'  => '',
					'id'    => 'afgc_recipient_delivery_physical',
				),

				array(
					'title'         => __( 'Ask sender & recipient name', 'addify_gift_cards' ),
					'desc'          => __( 'Ask sender & recipient name.', 'addify_gift_cards' ),
					'id'            => 'afgc_sender_recipent_name',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Title', 'addify_gift_cards' ),
					'desc'        => __( 'Enter a title for section.', 'addify_gift_cards' ),
					'id'          => 'afgc_physical_section_title',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Delivery Info', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_physical_section_title',
					'desc_tip'    => 'If empty then default title is "Delivery Info". ',
				),

				array(
					'title'         => __( 'Allow customers to add a message to the gift card', 'addify_gift_cards' ),
					'desc'          => __( 'Allow customers to add a message to the gift card.', 'addify_gift_cards' ),
					'id'            => 'afgc_printed_message',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_recipient_delivery_physical',
				),

			);

		return apply_filters( 'afgc_physical_gift_card_setting_settings', $settings );
	}

	/**
	 * Get settings for the Gift Card PDF section.
	 *
	 * @return array
	 */
	protected function get_settings_for_afgc_pdf_setting_section() {

		$settings =
			array(
				array(
					'title' => __( 'Gift Card PDF', 'addify_gift_cards' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'afgc_pdf_setting',
				),

				array(
					'title'         => __( 'Enable store name', 'addify_gift_cards' ),
					'desc'          => '',
					'id'            => 'afgc_enable_store_name_pdf',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Store name', 'addify_gift_cards' ),
					'desc'        => __( 'Enter store name.', 'addify_gift_cards' ),
					'id'          => 'afgc_store_name_pdf',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Enter store name', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_store_name_pdf',
					'desc_tip'    => false,
				),

				array(
					'title'         => __( 'Enable store logo', 'addify_gift_cards' ),
					'desc'          => '',
					'id'            => 'afgc_enable_store_logo_pdf',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'   => __( 'Store logo', 'addify_gift_cards' ),
					'desc'    => __( 'Upload a logo to display on the gift card.', 'addify_gift_cards' ),
					'id'      => 'afgc_store_logo',
					'type'    => 'image_upload',
					'default' => '', 
					'css'     => 'min-width:300px;',
				),

				array(
					'title'         => __( 'Enable gift card image', 'addify_gift_cards' ),
					'desc'          => 'Enable gift card image for pdf.',
					'id'            => 'afgc_enable_gift_card_image',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Enable gift card name', 'addify_gift_cards' ),
					'desc'          => 'Enable gift card name for pdf.',
					'id'            => 'afgc_enable_gift_card_name',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Enable price', 'addify_gift_cards' ),
					'desc'          => __( 'Enable gift card price for pdf.', 'addify_gift_cards' ),
					'id'            => 'afgc_enable_gift_card_price',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Coupon code', 'addify_gift_cards' ),
					'desc'          => __( 'Enable coupon code for pdf.', 'addify_gift_cards' ),
					'id'            => 'afgc_enable_gift_card_code',
					'default'       => 'yes',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'         => __( 'Message', 'addify_gift_cards' ),
					'desc'          => __( 'Enable gift card message for pdf.', 'addify_gift_cards' ),
					'id'            => 'afgc_enable_gift_card_message',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Gift card message', 'addify_gift_cards' ),
					'id'          => 'afgc_gift_card_message',
					'css'         => 'width:400px; height: 75px;',
					'placeholder' => __( 'Write message with the instruction', 'addify_gift_cards' ),
					'type'        => 'textarea',
					'autoload'    => false,
					'desc_tip'    => false,
				),

				array(
					'title'         => __( 'Enable disclaimer', 'addify_gift_cards' ),
					'desc'          => 'Enable disclaimer text on PDF.',
					'id'            => 'afgc_enable_disclaimer',
					'default'       => 'no',
					'type'          => 'checkbox',
					'checkboxgroup' => 'start',
				),

				array(
					'title'       => __( 'Disclaimer', 'addify_gift_cards' ),
					'desc'        => __( 'Disclaimer text for PDF.', 'addify_gift_cards' ),
					'id'          => 'afgc_disclaimer_text',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Add text here.', 'addify_gift_cards' ),
					'default'     => '',
					'autoload'    => false,
					'class'       => 'afgc_disclaimer_text',
					'desc_tip'    => false,
				),
				array(
					'title'    => __( 'Select Date Format', 'addify_gift_cards' ),
					'desc'     => __( 'Choose the date format you want to use.', 'addify_gift_cards' ),
					'id'       => 'afgc_date_format',
					'default'  => 'd/m/Y',
					'type'     => 'select',
					'options'  => array(
						'd/m/Y'  => __( '13/10/2024', 'addify_gift_cards' ),
						'Y-m-d'  => __( '2024-11-07', 'addify_gift_cards' ),
						'm/d/Y'  => __( '11/07/2024', 'addify_gift_cards' ),
						'F j, Y' => __( 'March 10, 2024', 'addify_gift_cards' ),
					),
					'css'      => 'min-width:300px;',
					'desc_tip' => true,
				),
				array(
					'title'       => __( 'PDF file name', 'addify_gift_cards' ),
					'desc'        => __( 'Add PDF download document name.', 'addify_gift_cards' ),
					'id'          => 'afgc_pdf_file_name',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Add name here.', 'addify_gift_cards' ),
					'class'       => 'afgc_pdf_file_name',
					'desc_tip'    => true,
				),
				array(
					'title'       => __( 'PDF email prefix', 'addify_gift_cards' ),
					'desc'        => __( 'Add email document prefix name.', 'addify_gift_cards' ),
					'id'          => 'afgc_pdf_email_prefix_name',
					'type'        => 'text',
					'css'         => '',
					'placeholder' => __( 'Add prefix name here.', 'addify_gift_cards' ),
					'class'       => 'afgc_pdf_email_prefix_name',
					'desc_tip'    => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'afgc_pdf_setting',
				),

			);

		return apply_filters( 'afgc_pdf_setting_settings', $settings );
	}
	public function afgc_render_image_upload_field( $value ) {
		$option_value = get_option( $value['id'], $value['default'] );
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( $value['type'] ); ?>">
				<input type="hidden" name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" value="<?php echo esc_attr( $option_value ); ?>" />
				<img src="<?php echo esc_attr( $option_value ); ?>" style="width: 84px; margin-right: 11px;" class="afgc-pdf-logo">
				<button type="button" class="button afgc_upload_button" data-target="<?php echo esc_attr( $value['id'] ); ?>">
					<?php echo esc_html( 'Upload Image', 'addify_gift_cards' ); ?>
				</button>
				<span class="description"><?php echo esc_html( $value['desc'] ); ?></span>
			</td>
		</tr>
		<?php
	}
}

Af_Gift_Card_Setting_Tab::init();


