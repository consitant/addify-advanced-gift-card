<?php
// reference the Dompdf namespace
use Dompdf\Dompdf;

class Af_Gift_Card_Mail extends WC_Email {

	public $afgc_log_id;

	public $afgc_order_id = 0;

	public $afgc_get_current_item = 0;

	protected $gift_card_info = array();

	// Send PDF into Email.
	public function afgc_pdf_for_email_cb( $gift_card_id ) {

		require_once AFGC_PLUGIN_DIR . 'vendor/autoload.php';

		ob_start();

		include_once AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-pdf-template.php';

		$temp = ob_get_clean();

		$dompdf = new Dompdf();

		$options = $dompdf->getOptions();

		$options->set( array( 'isRemoteEnabled' => true ) );

		$dompdf->setOptions( $options );

		$dompdf->loadHtml( $temp );

		$dompdf->setPaper( 'A4', 'landscape' );

		$dompdf->render();

		$output = $dompdf->output();

		$afgc_pdf_email_prefix_name =get_option('afgc_pdf_email_prefix_name');

		if (!empty($afgc_pdf_email_prefix_name)) {
			$afgc_prefix_name = $afgc_pdf_email_prefix_name;
		} else {
			$afgc_prefix_name = 'afgc';
		}
		$path = AFGC_PLUGIN_DIR . 'front/assets/pdf/' . $afgc_prefix_name . '-' . $gift_card_id . '.pdf';

		file_put_contents( $path, $output );

		return $path;
	}


	public function __construct() {

		$this->id             = 'afgc_recipent_email_id';
		$this->title          = __( 'Addify Gift Card Recipient Email', 'addify_gift_cards' );
		$this->customer_email = true;
		$this->description    = __( 'This will help to Recipient someone through email', 'addify_gift_cards' );
		$this->template_base  = AFGC_PLUGIN_DIR;
		$this->template_html  = '/front/includes/emails/html/afgc-coupon-code-html.php';
		$this->template_plain = '/front/includes/emails/plain/afgc-coupon-code-plain.php';
		$this->placeholders   = array(
			'{referral_enable}'             => '',
			'{referral_recipent}'           => '',
			'{referral_subject}'            => '',
			'{referral_heading}'            => '',
			'{referral_additional_content}' => '',
			'{referral_coupon_code}'        => '',
		);

		$this->afgc_log_id = array();

		// Call to the  parent constructor.
		parent::__construct();
		add_action( 'afgc_receipent_email_notification', array( $this, 'trigger' ), 10, 2 );
	}

	public function get_default_subject() {
		return __( 'Gift Card Email:', 'addify_gift_cards' );
	}

	public function get_default_heading() {
		return __( 'Gift Card Email:', 'addify_gift_cards' );
	}

	public function trigger( $order_id, $current_item_id, $code ) {
		
		$this->afgc_order_id         = $order_id;
		$this->afgc_get_current_item =  $current_item_id;

		$this->setup_locale();

		$order = new WC_Order( $order_id );

		// $afgc_recip_email = array();

		foreach ( $order->get_items() as $item_id => $item ) {

			if ( (int) $item_id !== (int) $current_item_id ) {
				continue;
			}

			$afgc_log_id = wc_get_order_item_meta( $item->get_id(), 'afgc_log_id', true );

			if ( ! empty( $afgc_log_id ) ) {

				$this->afgc_log_id[] = $afgc_log_id;

			}

			$product_id = $item->get_product_id();

			$is_afgc_virtual = get_post_meta( $product_id, 'afgc_virtual', true );

			$meta_data      = $item->get_meta_data();
			$gift_card_info = array();


			foreach ( $meta_data as $meta ) {
				$data = $meta->get_data();

				if ( 'afgc_gift_card_infos' === $data['key'] ) {
					$gift_card_info = $data['value'];
				}

				if ( 'afgc_cp' === $data['key'] ) {
					$coupon_code                                  = $data['value'];
					$this->placeholders['{referral_coupon_code}'] = $coupon_code;
				}
			}

			$this->gift_card_info = $gift_card_info;

			if ( 'yes' == $is_afgc_virtual ) {

				if ( ! empty( $gift_card_info ) && isset( $gift_card_info['total_recipient_user_Select'] ) ) {

					$total_field      = $gift_card_info['total_recipient_user_Select'];
					$afgc_recip_email = array();
					for ( $i = 1; $i <= $total_field; $i++ ) {

						if ( isset( $gift_card_info[ 'afgc_recipient_email' . $i ] ) ) {

							$afgc_recip_email[] = $gift_card_info[ 'afgc_recipient_email' . $i ];
						}
					}

					$afgc_recip_email = implode( ',', $afgc_recip_email );

					if ( $this->is_enabled() ) {

						$this->send( $afgc_recip_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

					}
				}
			}
		}

		$this->restore_locale();
	}

	public function get_attachments() {

		$files = array();

		if ( empty( $this->afgc_log_id ) ) {

			return apply_filters( 'woocommerce_email_attachments', $files, $this->id, $this->object, $this );

		}
		if ( isset( $this->gift_card_info['afgc_pdf_tab'] ) 
		&& 'yes' === $this->gift_card_info['afgc_pdf_tab'] ) {
			foreach ( $this->afgc_log_id as $log_id ) {

				$files = $this->afgc_pdf_for_email_cb( $log_id );
			}
		}

		return apply_filters( 'woocommerce_email_attachments', $files, $this->id, $this->object, $this );
	}


	public function get_content_html() {

		$order_id = $this->afgc_order_id;

		$afgc_get_current_item_id = $this->afgc_get_current_item;

		$log_id = $this->afgc_log_id;

		return wc_get_template_html(
			$this->template_html,
			array(
				'member'               => $this->object,
				'email_heading'        => $this->get_heading(),
				'additional_content'   => $this->get_additional_content(),
				'sent_to_admin'        => false,
				'plain_text'           => false,
				'email'                => $this,
				'referral_coupon_code' => $this->placeholders['{referral_coupon_code}'],
				'order_id'             => $order_id,
				'log_id'               => $log_id,
				'curren_item_id'       =>$afgc_get_current_item_id,
			),
			$this->template_base,
			$this->template_base
		);
	}

	public function get_content_plain() {
		return wc_get_template_html(
			$this->template_html,
			array(
				'member'             => $this->object,
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => false,
				'email'              => $this,
			),
			$this->template_base,
			$this->template_base
		);
	}
}
