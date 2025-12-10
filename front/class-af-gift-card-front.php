<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// reference the Dompdf namespace
use Dompdf\Dompdf;

class Af_Gift_Card_Front {

	// Constructor.
	public function __construct() {

		// Include Gift Card PDF.
		add_action( 'wp_loaded', array( $this, 'afgc_include_front_pdf_code_cb' ) );

		// Scripts Enqueue.
		add_action( 'wp_enqueue_scripts', array( $this, 'afgc_front_enqueue_scripts_cb' ) );

		// Gallery Popup.
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'afgc_gift_card_gallery_modal_cb' ) );

		// Add Gift Card Info into Cart Item.
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'afgc_add_gift_card_info_cart_item_data' ), 10, 3 );

		// Add Gift Card Info into Cart.
		add_filter( 'woocommerce_add_cart_item', array( $this, 'afgc_add_gift_card_info_cart' ), 20, 2 );

		// Display Gift Card Info into Cart.
		add_filter( 'woocommerce_get_item_data', array( $this, 'afgc_display_gift_card_info_cart' ), 10, 2 );

		// Replace Product Thumbnai With Custom Card Image.
		add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'afgc_replace_prodcut_thumb_cart_page_cb' ), 10, 2 );
		// Replace Product Thumbnai With Custom Card in cart block.
		add_filter('woocommerce_store_api_cart_item_images', array( $this, 'afgc_replace_prodcut_thumb_cart_block_cb' ), 10, 3);
		// 1- Register new endpoint (URL) for My Account page.
		add_action( 'wp_loaded', array( $this, 'afgc_add_link_my_account_endpoint' ) );
		// 2. Add new query var.
		add_filter( 'query_vars', array( $this, 'afgc_add_link_my_account_vars' ) );

		// 3. Insert the new endpoint into the My Account menu.
		add_filter( 'woocommerce_account_menu_items', array( $this, 'afgc_add_link_my_account' ) );

		// Adding detail into meta.
		add_action( 'woocommerce_checkout_order_created', array( $this, 'afgc_order_created_cb' ), 10, 1 );

		add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'afgc_update_product_data' ), 10, 4 );

		// Add fee with of gift card with product.
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'ka_gw_prd_pg_fee' ), 10, 1 );
		add_filter( 'woocommerce_cart_item_price', array( $this, 'afamad_mini_cart_update' ), 10, 3 );

		// Display Giftcard Products Shortcodes.
		add_shortcode( 'afgc-gift-cards', array( $this, 'afgc_show_gift_cards_cb' ) );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'afgc_add_gift_card_button' ) );
		add_filter( 'woocommerce_available_variation', array( $this, 'afgc_add_gift_button_to_variations' ), 10, 3 );
		add_filter( 'woocommerce_store_api_product_quantity_editable', array( $this, 'afgc_disable_quantity_edit_gift_cards_block' ), 10, 3 );
		add_filter( 'woocommerce_cart_item_quantity', array( $this, 'afgc_disable_quantity_edit_gift_cards' ), 10, 3 );
	}
	public function afgc_disable_quantity_edit_gift_cards( $product_quantity, $cart_item_key, $cart_item ) {
		$product = $cart_item['data'];
		if ( $product && $product->is_type( 'gift_card' ) ) {
			return sprintf( '<span class="fixed-qty">%d</span>', $cart_item['quantity'] );
		}
		return $product_quantity;
	}

	public function afgc_disable_quantity_edit_gift_cards_block( $editable, $product, $cart_item ) {
		if ( $product && $product->get_type() === 'gift_card' ) {
			return false;
		}
		return $editable;
	}
	public function afgc_add_gift_card_button() {
		global $product;
		$get_current_theme  = wp_get_theme();
		$product_id         = $product->get_id();
		$enabl_this_product = get_post_meta( $product_id, '_agfc_current_product_gift_checkbox', true );
		$gift_product_id    = get_post_meta( $product_id, '_agfc_selected_gift_card', true );
		if ( 'yes' === $enabl_this_product && ! empty( $gift_product_id ) ) {
			if ( $gift_product_id ) {
				$url = add_query_arg(
					array(
						'gift_product' => $product_id,
					),
					get_permalink( $gift_product_id )
				);
				if ('Twenty Twenty-Five' == $get_current_theme->name || 'Twenty Twenty-Four' == $get_current_theme->name) {
					echo '<a href="' . esc_url( $url ) . '" class="button alt wp-element-button" style="justify-self: start; margin-top: 10px;">' . esc_html__('Send as a Gift', 'addify_gift_cards') . '</a>';
				} else {
					echo '<a href="' . esc_url( $url ) . '" class="button alt">' . esc_html__('Send as a Gift', 'addify_gift_cards') . '</a>';
				}
			}
		}
	}

	public function afgc_add_gift_button_to_variations( $variation_data, $product, $variation ) {
		$variation_id       = $variation->get_id();
		$get_current_theme  = wp_get_theme();
		$enabl_this_product = get_post_meta( $variation_id, '_agfc_variation_gift_checkbox', true );
		$gift_product_id    = get_post_meta( $variation_id, '_agfc_variation_gift_card', true );
		if ( 'yes' === $enabl_this_product && ! empty( $gift_product_id ) ) {
			$url = add_query_arg(
				array(
					'gift_product' => $variation_id,
				),
				get_permalink( $gift_product_id )
			);
			if ('Twenty Twenty-Five' == $get_current_theme->name || 'Twenty Twenty-Four' == $get_current_theme->name) {
				$variation_data['afgc_gift_button'] = '<a href="' . esc_url( $url ) . '" class="button alt afgc-gift-btn wp-element-button" style="justify-self: start; margin-top: 10px;">' . esc_html__( 'Send as a Gift', 'addify_gift_cards' ) . '</a>';
			} else {
				$variation_data['afgc_gift_button'] = '<a href="' . esc_url( $url ) . '" class="button alt afgc-gift-btn">' . esc_html__( 'Send as a Gift', 'addify_gift_cards' ) . '</a>';
			}
		}
		return $variation_data;
	}

	public function afamad_mini_cart_update( $price, $cart_item, $cart_item_key ) {
		if ( isset( $cart_item['data'] ) && is_object( $cart_item['data'] ) ) {
			$product = $cart_item['data']; // WC_Product object

			// Check if product is gift card
			if ( $product->is_type( 'gift_card' ) 
				&& isset( $cart_item['afgc_gift_card_infos']['afgc_price_of_gift_card'] ) 
			) {
				$cart_item_price = floatval( $cart_item['afgc_gift_card_infos']['afgc_price_of_gift_card'] );
				$price           = wc_price( $cart_item_price );
			}
		}

		return $price;
	}
	// Scripts Enqueue.
	public function afgc_front_enqueue_scripts_cb() {

		wp_enqueue_style( 'afgc-front', plugins_url( '/assets/css/afgc-front.css', __FILE__ ), false, '1.0.0' );
		wp_enqueue_style( 'select2', plugins_url( '/assets/css/select2.css', __FILE__ ), false, '1.0.0' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'afgc-front', plugins_url( '/assets/js/afgc-front.js', __FILE__ ), array(), '1.0.0', false );
		wp_enqueue_script( 'select2', plugins_url( '/assets/js/select2.js', __FILE__ ), array(), '1.0.0', false );
		$afgc_date_format    = get_option('afgc_date_format');
		$afgc_curreny_symbol = get_woocommerce_currency_symbol();

		$addify_gift_card_ajax_data = array(
			'admin_url'           => admin_url( 'admin-ajax.php' ),
			'nonce'               => wp_create_nonce( 'addify_agf_nonce' ),
			'afgc_date_format'    => $afgc_date_format,
			'afgc_curreny_symbol' => $afgc_curreny_symbol,
		);

		wp_localize_script( 'afgc-front', 'k_php_var', $addify_gift_card_ajax_data );
	}

	public function afgc_include_front_pdf_code_cb() {

		if ( isset( $_GET['download_gf_id'] ) ) {

			require_once AFGC_PLUGIN_DIR . 'vendor/autoload.php';
			ob_start();
			include_once AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-pdf-template.php';

			$temp    = ob_get_clean();
			$dompdf  = new Dompdf();
			$options = $dompdf->getOptions();
			$options->set( array( 'isRemoteEnabled' => true ) );
			$dompdf->setOptions( $options );
			$dompdf->loadHtml( $temp );
			$dompdf->setPaper( 'A4', 'portrait' );
			$dompdf->render();
			$afgc_pdf_file_name = get_option('afgc_pdf_file_name');
			if (!empty($afgc_pdf_file_name)) {
				$afgc_pdf_file_name = $afgc_pdf_file_name;
			} else {
				$afgc_pdf_file_name = 'document';
			}
			$dompdf->stream($afgc_pdf_file_name . '.pdf', array( 'Attachment'=>1 ));

		}
	}


	// Gallery Popup.
	public function afgc_gift_card_gallery_modal_cb() {

		include AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php';
	}

	public function afgc_add_gift_card_info_cart_item_data( $cart_item_data, $product_id, $variation_id ) {

		$product = wc_get_product( $product_id );

		if ( ! $product->is_type( 'gift_card' ) ) {

			return;

		}
		$nonce = isset( $_POST['afgc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afgc_nonce'] ) ) : 0;

		if ( ! wp_verify_nonce( $nonce, 'afgc_nonce' ) ) {
			die( 'Failed Ajax security check!' );
		}

		$get_gift_cart_info = array();

		$afgc_by_email = 'no';

		$afgc_by_pdf = 'no';

		$total_receipient = 0;

		if ( ! empty( $_POST['afgc_email_tab'] ) ) {

			$afgc_by_email = sanitize_text_field( wp_unslash( $_POST['afgc_email_tab'] ) );

		}

		if ( ! empty( $_POST['afgc_pdf_tab'] ) ) {

			$afgc_by_pdf = sanitize_text_field( wp_unslash( $_POST['afgc_pdf_tab'] ) );

		}

		$get_gift_cart_info['afgc_email_tab'] = $afgc_by_email;

		$get_gift_cart_info['afgc_pdf_tab'] = $afgc_by_pdf;

		if ( isset( $_POST['total_input_field_admin_select'] ) ) {

			$total_receipient = intval( $_POST['total_input_field_admin_select'] );

			$get_gift_cart_info['total_recipient_user_Select'] = $total_receipient;

		}

		$is_afgc_virtual = get_post_meta( $product->get_id(), 'afgc_virtual', true );

		if ( 'yes' == $is_afgc_virtual ) {
			if ( isset( $_POST['afgc_selected_img'] ) ) {

				$get_gift_cart_info['afgc_selected_img'] = sanitize_text_field( wp_unslash( $_POST['afgc_selected_img'] ) );

			}
		}

		if ( $product->is_type( 'gift_card' ) ) {

			for ( $i = 1; $i <= $total_receipient; $i++ ) {

				if ( isset( $_POST[ 'afgc_recipient_name' . $i ] ) ) {

					$get_gift_cart_info[ 'afgc_recipient_name' . $i ] = sanitize_text_field( wp_unslash( $_POST[ 'afgc_recipient_name' . $i ] ) );

				}

				if ( isset( $_POST[ 'afgc_recipient_email' . $i ] ) ) {

					$get_gift_cart_info[ 'afgc_recipient_email' . $i ] = sanitize_text_field( wp_unslash( $_POST[ 'afgc_recipient_email' . $i ] ) );

				}
			}

			if ( isset( $_POST['afgc_phy_gift_recipient_name'] ) ) {

				$get_gift_cart_info['afgc_phy_gift_recipient_name'] = sanitize_text_field( wp_unslash( $_POST['afgc_phy_gift_recipient_name'] ) );

			}

			if ( isset( $_POST['afgc_phy_gift_recipient_email'] ) ) {

				$get_gift_cart_info['afgc_phy_gift_recipient_email'] = sanitize_text_field( wp_unslash( $_POST['afgc_phy_gift_recipient_email'] ) );

			}
		}

		if ( isset( $_POST['afgc_virtual_custom_amount'] ) ) {

			$get_gift_cart_info['afgc_virtual_custom_amount'] = sanitize_text_field( wp_unslash( $_POST['afgc_virtual_custom_amount'] ) );

		}

		if ( isset( $_POST['afgc_admin_set_price'] ) ) {

			$get_gift_cart_info['afgc_admin_set_price'] = sanitize_text_field( wp_unslash( $_POST['afgc_admin_set_price'] ) );

		} else {
			$get_gift_cart_info['afgc_admin_set_price'] = '';
		}
		if ( isset( $_POST['final_selected_price_of_gift_card'] ) ) {

			$get_gift_cart_info['afgc_price_of_gift_card'] = sanitize_text_field( wp_unslash( $_POST['final_selected_price_of_gift_card'] ) );

		}

		if ( isset( $_POST['afgc_delivery_date'] ) ) {

			$get_gift_cart_info['afgc_delivery_date'] = sanitize_text_field( wp_unslash( $_POST['afgc_delivery_date'] ) );

		}
		if ( isset( $_POST['afgc_gift_products'] ) ) {

			$get_gift_cart_info['afgc_gift_products'] = sanitize_text_field( wp_unslash( $_POST['afgc_gift_products'] ) );
		} else {
			$get_gift_cart_info['afgc_gift_products'] = '';
		}
		if ( isset( $_POST['afgc_gift_type'] ) ) {

			$get_gift_cart_info['afgc_gift_type'] = sanitize_text_field( wp_unslash( $_POST['afgc_gift_type'] ) );
		}
		if ( isset( $_POST['afgc_sender_name'] ) ) {

			$get_gift_cart_info['afgc_sender_name'] = sanitize_text_field( wp_unslash( $_POST['afgc_sender_name'] ) );

		}

		if ( isset( $_POST['afgc_sender_message'] ) ) {

			$get_gift_cart_info['afgc_sender_message'] = sanitize_text_field( wp_unslash( $_POST['afgc_sender_message'] ) );

		}

		if ( isset( $_POST['afgc_phy_gift_sender_name'] ) ) {

			$get_gift_cart_info['afgc_phy_gift_sender_name'] = sanitize_text_field( wp_unslash( $_POST['afgc_phy_gift_sender_name'] ) );

		}

		if ( isset( $_POST['afgc_phy_gift_sender_message'] ) ) {

			$get_gift_cart_info['afgc_phy_gift_sender_message'] = sanitize_text_field( wp_unslash( $_POST['afgc_phy_gift_sender_message'] ) );

		}

		if ( isset( $_POST['afgc_phy_gift_delivery_date'] ) ) {

			$get_gift_cart_info['afgc_phy_gift_delivery_date'] = sanitize_text_field( wp_unslash( $_POST['afgc_phy_gift_delivery_date'] ) );

		}
		$cart_item_data['afgc_gift_card_infos'] = $get_gift_cart_info;

		return $cart_item_data;
	}

	public function afgc_add_gift_card_info_cart( $cart_item_data, $product ) {

		if ( isset( $cart_item_data['afgc_gift_card_infos']['afgc_virtual_custom_amount'] ) ) {

			$afgc_virtual_custom_amount = $cart_item_data['afgc_gift_card_infos']['afgc_virtual_custom_amount'];

			$afgc_gift_card_infos = $cart_item_data['afgc_gift_card_infos']['afgc_admin_set_price'];
			if ( ! empty( $afgc_virtual_custom_amount ) ) {

				$gift_card_price = $cart_item_data['afgc_gift_card_infos']['afgc_virtual_custom_amount'];

			} elseif ( ! empty( $afgc_gift_card_infos ) ) {

				$gift_card_price = $cart_item_data['afgc_gift_card_infos']['afgc_admin_set_price'];

			} else {
				$gift_card_price = '';
			}
			$cart_item_data['data']->set_price( $gift_card_price );

		}

		return $cart_item_data;
	}


	// Replace Product Thumbnai With Custom Card Image.
	public function afgc_replace_prodcut_thumb_cart_page_cb( $item_data, $cart_item ) {
		$is_afgc_virtual = get_post_meta( $cart_item['data']->get_id(), 'afgc_virtual', true );
		$product         = wc_get_product( $cart_item['product_id'] );
		if ( ! $product->is_type( 'gift_card' ) || ! isset( $cart_item['afgc_gift_card_infos'] ) ) {
			return $item_data;
		}
		$get_gift_cart_info = $cart_item['afgc_gift_card_infos'];
		if ( 'yes' == $is_afgc_virtual && isset( $get_gift_cart_info['afgc_selected_img'] ) ) {
			$class          = 'attachment-shop_thumbnail wp-post-image';
			$src            = $get_gift_cart_info['afgc_selected_img'];
			$afgc_card_img  = '<img';
			$afgc_card_img .= ' src="' . $src . '"';
			$afgc_card_img .= ' class="' . $class . '"';
			$afgc_card_img .= ' />';
			return $afgc_card_img;
		} else {
			return $product->get_image();
		}
	}

	public function afgc_replace_prodcut_thumb_cart_block_cb( $product_images, $cart_item, $cart_item_key ) {
		$is_afgc_virtual = get_post_meta( $cart_item['data']->get_id(), 'afgc_virtual', true );
		$product         = wc_get_product( $cart_item['product_id'] );
		if ( ! $product->is_type( 'gift_card' ) || ! isset( $cart_item['afgc_gift_card_infos'] ) ) {
			return $product_images;
		}
		$get_gift_cart_info = $cart_item['afgc_gift_card_infos'];
		if ( isset( $get_gift_cart_info['afgc_selected_img'] ) ) {
			$src   = esc_url( $get_gift_cart_info['afgc_selected_img'] );
			$class = 'attachment-shop_thumbnail wp-post-image';

			return array(
				(object) array(
					'id'        => 0,
					'src'       => $src,
					'thumbnail' => $src,
					'srcset'    => '',
					'sizes'     => '',
					'name'      => 'Gift Card Image',
					'alt'       => 'Gift Card Image',
				),
			);
		}
	}

	public function ka_gw_prd_pg_fee( $cart_object ) {

		foreach ( $cart_object->get_cart() as $hash => $value_cart ) {
			if ( ! is_array( $value_cart ) ) {
				continue;
			}

			$product_id = $value_cart['product_id'];


			$get_main_array_key = array_keys( $value_cart );

			$total_person = 0;

			if ( isset( $value_cart['afgc_gift_card_infos']['total_recipient_user_Select'] ) ) {

				$total_receipient = sanitize_text_field( $value_cart['afgc_gift_card_infos']['total_recipient_user_Select'] );

				for ( $i = 1; $i <= $total_receipient; $i++ ) {

					if ( isset( $value_cart['afgc_gift_card_infos'][ 'afgc_recipient_name' . $i ] ) ) {

						++$total_person;

					}
				}
			}

			if ( in_array( 'afgc_gift_card_infos', $get_main_array_key ) ) {

				$afgc_virtual_custom_amount = isset( $value_cart['afgc_gift_card_infos']['afgc_virtual_custom_amount'] ) ? $value_cart['afgc_gift_card_infos']['afgc_virtual_custom_amount'] : '';

				if ( ! empty( $afgc_virtual_custom_amount ) ) {

					$gift_card_price = isset( $value_cart['afgc_gift_card_infos']['afgc_virtual_custom_amount'] ) ? $value_cart['afgc_gift_card_infos']['afgc_virtual_custom_amount'] : '';

					$cart_object->cart_contents[ $hash ]['data']->set_price( $gift_card_price );

				}
			}
			if ( in_array( 'afgc_gift_card_infos', $get_main_array_key ) ) {

				$afgc_virtual_custom_amount = isset( $value_cart['afgc_gift_card_infos']['afgc_admin_set_price'] ) ? $value_cart['afgc_gift_card_infos']['afgc_admin_set_price'] : '';

				if ( ! empty( $afgc_virtual_custom_amount ) ) {

					$gift_card_price = isset( $value_cart['afgc_gift_card_infos']['afgc_admin_set_price'] ) ? $value_cart['afgc_gift_card_infos']['afgc_admin_set_price'] : '';

					$cart_object->cart_contents[ $hash ]['data']->set_price( $gift_card_price );

				} else {
					$afgc_price_of_gift_card = isset( $value_cart['afgc_gift_card_infos']['afgc_price_of_gift_card'] ) ? $value_cart['afgc_gift_card_infos']['afgc_price_of_gift_card'] : '';
					$cart_object->cart_contents[ $hash ]['data']->set_price( $afgc_price_of_gift_card );
				}
			}
		}

		$total_discount = 0; // Initialize total discount
		if (!empty($cart_object)) {
			foreach ( $cart_object->get_cart() as $cart_item ) {
				$product_id = $cart_item['product_id'];

				if ( ! empty( $product_id ) && isset( $cart_item['line_total'] ) && !empty( $cart_item['line_total'] ) ) {

					$gift_card_price = $cart_item['line_total'];

					$product = wc_get_product( $product_id );

					if ( 'gift_card' === $product->get_type() ) {
						$discount_price             = (float) get_post_meta( $product->get_id(), 'afgc_special_discount_amount', true );
						$afgc_special_discount_type = get_post_meta( $product->get_id(), 'afgc_special_discount_type', true );

						if ( ! empty( $discount_price ) ) {
							if ( 'fixed' === $afgc_special_discount_type ) {
								$afgc_remaining_amount = min($discount_price, $gift_card_price);
							} elseif ( 'percentage' === $afgc_special_discount_type ) {
								$afgc_remaining_amount = round( $gift_card_price * ( $discount_price / 100 ) );
							}

							// Add the discount to the total if valid
							if ( ! empty( $afgc_remaining_amount ) && $afgc_remaining_amount > 0 ) {
								$total_discount += $afgc_remaining_amount;
							}
						}
					}
				}
			}
		}

		if ( $total_discount > 0 ) {
			$cart_object->add_fee( __( 'Gift Card Discount', 'addify_gift_cards' ), -$total_discount );
		}
	}

	public function afgc_display_gift_card_info_cart( $item_data, $cart_item ) {

		global $post;

		$get_cart_item_array_keys = array_keys( $cart_item );

		$is_afgc_virtual = get_post_meta( $cart_item['product_id'], 'afgc_virtual', true );

		$array_of_card_info = isset( $cart_item['afgc_gift_card_infos'] ) ? (array) $cart_item['afgc_gift_card_infos'] : array();

		$array_of_card_info = array_filter( $array_of_card_info );

		if ( 'yes' == $is_afgc_virtual ) {

			if ( isset( $array_of_card_info['afgc_selected_img'] ) ) {

				$afgc_selected_img = $array_of_card_info['afgc_selected_img'];

			}

			if (isset($array_of_card_info['total_recipient_user_Select'])) {
			$total_fileds = $array_of_card_info['total_recipient_user_Select'];
				for ($i = 0; $i <= $total_fileds; $i++) {

					if (isset($array_of_card_info[ 'afgc_recipient_name' . $i ])) {

						$item_data[] = array(
							'key'     => __('Recipient Name', 'addify_gift_cards'),
							'value'   => wc_clean($array_of_card_info[ 'afgc_recipient_name' . $i ]),
							'display' => wc_clean($array_of_card_info[ 'afgc_recipient_name' . $i ]),
						);

					}

					if (isset($array_of_card_info[ 'afgc_recipient_email' . $i ])) {

						$item_data[] = array(
							'key'     => __('Recipient Email', 'addify_gift_cards'),
							'value'   => wc_clean($array_of_card_info[ 'afgc_recipient_email' . $i ]),
							'display' => wc_clean($array_of_card_info[ 'afgc_recipient_email' . $i ]),
						);

					}
				}
			}

			if ( isset( $array_of_card_info['afgc_sender_name'] ) && ! empty( $array_of_card_info['afgc_sender_name'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Sender Name', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_sender_name'] ),
					'display' => wc_clean( $array_of_card_info['afgc_sender_name'] ),
				);

			}

			if ( isset( $array_of_card_info['afgc_sender_message'] ) && ! empty( $array_of_card_info['afgc_sender_message'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Sender Message', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_sender_message'] ),
					'display' => wc_clean( $array_of_card_info['afgc_sender_message'] ),
				);

			}

		} else {

			if ( isset( $array_of_card_info['afgc_phy_gift_recipient_name'] ) && ! empty( $array_of_card_info['afgc_phy_gift_recipient_name'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Recipient Name', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_phy_gift_recipient_name'] ),
					'display' => wc_clean( $array_of_card_info['afgc_phy_gift_recipient_name'] ),
				);

			}

			if ( isset( $array_of_card_info['afgc_phy_gift_recipient_email'] ) && ! empty( $array_of_card_info['afgc_phy_gift_recipient_email'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Recipient Email', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_phy_gift_recipient_email'] ),
					'display' => wc_clean( $array_of_card_info['afgc_phy_gift_recipient_email'] ),
				);

			}

			if ( isset( $array_of_card_info['afgc_phy_gift_sender_name'] ) && ! empty( $array_of_card_info['afgc_phy_gift_sender_name'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Sender Name', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_phy_gift_sender_name'] ),
					'display' => wc_clean( $array_of_card_info['afgc_phy_gift_sender_name'] ),
				);

			}

			if ( isset( $array_of_card_info['afgc_phy_gift_sender_message'] ) && ! empty( $array_of_card_info['afgc_phy_gift_sender_message'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Sender Message', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_phy_gift_sender_message'] ),
					'display' => wc_clean( $array_of_card_info['afgc_phy_gift_sender_message'] ),
				);

			}

			if ( isset( $array_of_card_info['afgc_phy_gift_delivery_date'] ) ) {

				$item_data[] = array(
					'key'     => __( 'Delivery Date', 'addify_gift_cards' ),
					'value'   => wc_clean( $array_of_card_info['afgc_phy_gift_delivery_date'] ),
					'display' => wc_clean( $array_of_card_info['afgc_phy_gift_delivery_date'] ),
				);

			}
		}

		if ( isset( $array_of_card_info['afgc_virtual_custom_amount'] ) && ! empty( $array_of_card_info['afgc_virtual_custom_amount'] ) ) {

			$item_data[] = array(
				'key'     => __( 'Custom Amount', 'addify_gift_cards' ),
				'value'   => wc_clean( get_woocommerce_currency_symbol() . $array_of_card_info['afgc_virtual_custom_amount'] ),
				'display' => wc_clean( get_woocommerce_currency_symbol() . ' ' . $array_of_card_info['afgc_virtual_custom_amount'] ),
			);

		}

		if ( isset( $array_of_card_info['afgc_delivery_date'] ) && ! empty( $array_of_card_info['afgc_delivery_date'] ) ) {

			$item_data[] = array(
				'key'     => __( 'Delivery Date', 'addify_gift_cards' ),
				'value'   => wc_clean( $array_of_card_info['afgc_delivery_date'] ),
				'display' => wc_clean( $array_of_card_info['afgc_delivery_date'] ),
			);

		}
		if ( isset( $array_of_card_info['afgc_gift_products'] ) && ! empty( $array_of_card_info['afgc_gift_products'] ) ) {
			$product_id   = wc_clean($array_of_card_info['afgc_gift_products']);
			$product      = wc_get_product($product_id);
			$product_name = $product->get_name();
			$item_data[]  = array(
				'key'     => __( 'Product Gift', 'addify_gift_cards' ),
				'value'   => $product_id,
				'display' => $product_name,
			);

		}

		return $item_data;
	}

	public function afgc_add_link_my_account_endpoint() {

		add_rewrite_endpoint( 'gift-card', EP_ROOT | EP_PAGES );

		flush_rewrite_rules();
		add_action( 'woocommerce_account_gift-card_endpoint', array( $this, 'afgc_add_link_my_content' ) );
	}

	public function afgc_add_link_my_account_vars( $vars ) {

		$vars[] = 'gift-card';

		return $vars;
	}

	public function afgc_add_link_my_account( $items ) {

		$items['gift-card'] = 'Gift Card';

		if ( isset( $items['customer-logout'] ) ) {
			$logout_item = $items['customer-logout'];
			unset( $items['customer-logout'] );
			$items['customer-logout'] = $logout_item;
		}

		return $items;
	}

	public function afgc_add_link_my_content() {

		global $product, $post, $post_id;

		$user_id = get_current_user_id();

		$current_user_email = get_user_by( 'id', $user_id )->data->user_email;

		$paged = get_query_var( 'gift-card' );

		$segments     = explode('/', trim($paged, '/'));
		$firstSegment = $segments[0] ? $segments[0] : 'afgc-purchased';
		?>
		<div class="afgc-tabs">
			<ul class="afgc-tabs-navs" id="afgc_tabs_nav">
				<li class="<?php echo esc_attr( ( 'afgc-purchased' == $firstSegment ) ? 'active' : ''); ?>"><a href="<?php echo esc_url(wc_get_page_permalink( 'myaccount' ) . '/gift-card/afgc-purchased'); ?>" data-toggle="tab" data-tab-class="afgc-purchased"><?php echo esc_html__( 'Purchased Gifts', 'addify_gift_cards' ); ?></a></li>
				<li class="<?php echo esc_attr( ( 'afgc-received' == $firstSegment ) ? 'active' : ''); ?>"><a href="<?php echo esc_url(wc_get_page_permalink( 'myaccount' ) . '/gift-card/afgc-received'); ?>" data-toggle="tab" data-tab-class="afgc-received"><?php echo esc_html__( 'Received Gifts', 'addify_gift_cards' ); ?></a></li>
			</ul>
			<div class="afgc-tabs-content">
				<div class="tab-content" id="afgc-purchased" style="display: <?php echo ( 'afgc-purchased' == $firstSegment ) ? 'block' : 'none'; ?>">

					<?php

						$my_orders_columns = apply_filters(
							'woocommerce_my_account_my_orders_columns',
							array(
								'afgc_code'          => esc_html__( 'Gift Card', 'addify_gift_cards' ),
								'afgc_coupon_amount' => esc_html__( 'Price', 'addify_gift_cards' ),
								'afgc_coupon_expiry' => esc_html__( 'Expiry', 'addify_gift_cards' ),
								'afgc_receipent'     => esc_html__( 'Receipent Email', 'addify_gift_cards' ),
								'afgc_download_pdf'  => esc_html__( 'Download', 'addify_gift_cards' ),
							)
						);
					?>
					<h4><?php echo esc_html__( 'Purchased Gifts', 'addify_gift_cards' ); ?> </h4>
					<div class="afgc-gift-account-content">

						<table class="shop_table shop_table_responsive my_account_orders">
							<thead>
								<tr>
									<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>

										<th class="<?php echo esc_attr( $column_id ); ?>">

											<span class="nobr"><?php echo esc_html( $column_name ); ?></span>

										</th>

									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php
									global $post;

									$coupon_total_amount = 0;

									$afgc_net_amount = 0;

									global $wp_query;

									$paged          = str_replace('paged=', '', $paged);
									$paged          = str_replace('afgc-purchased/page/', '', $paged);
									$paged_purchase = isset($_GET['purchase_paged']) ? absint($_GET['purchase_paged']) : 1;
									$paged          = $paged_purchase ? $paged_purchase : 1;
									$args           = array(

										'post_type'      => 'afgc_gift_card_log',

										'post_status'    => 'publish',

										'posts_per_page' => -1,

										'orderby'        => 'id',

										'order'          => 'DESC',

									);
									$pre_query      = new WP_Query($args);
									$valid_post_ids = array();
									if ($pre_query->have_posts()) {
										while ($pre_query->have_posts()) {
											$pre_query->the_post();

											$afgc_coupon_code    = get_post_meta(get_the_ID(), '_coupon_code', true);
											$coupon              = new WC_Coupon($afgc_coupon_code);
											$coupon_id           = $coupon->get_id();
											$coupon_total_amount = floatval( get_post_meta( $coupon_id, 'coupon_total_amount', true ) );
											$afgc_purchase_for   = get_post_meta(get_the_ID(), '_purchase_for', true);

											if ( isset( $afgc_purchase_for['afgc_phy_gift_recipient_email'] ) ) {

												$afgc_phy_gift_recipient_email = $afgc_purchase_for['afgc_phy_gift_recipient_email'];

											} else {

												$afgc_phy_gift_recipient_email = '';
											}

											$afgc_order_id = get_post_meta( intval( $post->ID ), '_order_id', true );

											$afgc_item_id = get_post_meta( intval( $post->ID ), '_item_id', true );

											$order = wc_get_order( $afgc_order_id );

											$billing_email = $order->get_billing_email();

											$item = new WC_Order_Item_Product( $afgc_item_id );

											$product = $item->get_product();

											$afgc_product_id = $product->get_id();

											$afgc_product_name = $product->get_name();

											if ( $billing_email == $current_user_email ) {

												$valid_post_ids[] = get_the_ID();
												$afgc_net_amount += $coupon_total_amount;
											}
										}
									}
									wp_reset_postdata();

									$af_purchase_total_posts = count($valid_post_ids);

									$purchase_posts_per_page = 8;
									$offset                  = ( (int) $paged - 1 ) * $purchase_posts_per_page;

									// Query only valid posts for the current page
									$args['posts_per_page'] = $purchase_posts_per_page;
									$args['post__in']       = $valid_post_ids;
									$args['paged']          = $paged;
									$query                  = new WP_Query( $args );

									if ( $query->have_posts() ) :

										while ( $query->have_posts() ) :

											$query->the_post();

											$afgc_coupon_code = get_post_meta( get_the_ID(), '_coupon_code', true );

											$coupon = new WC_Coupon( $afgc_coupon_code );

											$coupon_id = $coupon->get_id();

											$coupon_exp = $coupon->get_date_expires();

											$coupon_total_amount = floatval( get_post_meta( $coupon_id, 'coupon_total_amount', true ) );

											$afgc_purchase_for = get_post_meta( intval( $post->ID ), '_purchase_for', true );

											if ( isset( $afgc_purchase_for['afgc_phy_gift_recipient_email'] ) ) {

												$afgc_phy_gift_recipient_email = $afgc_purchase_for['afgc_phy_gift_recipient_email'];

											} else {

												$afgc_phy_gift_recipient_email = '';
											}

											$afgc_order_id = get_post_meta( intval( $post->ID ), '_order_id', true );

											$afgc_item_id = get_post_meta( intval( $post->ID ), '_item_id', true );

											$order = wc_get_order( $afgc_order_id );

											$billing_email = $order->get_billing_email();

											$item = new WC_Order_Item_Product( $afgc_item_id );

											$product = $item->get_product();

											$afgc_product_id = $product->get_id();

											$afgc_product_name = $product->get_name();

											if ( $billing_email != $current_user_email ) {

												continue;
											}

											if ( $afgc_coupon_code ) {

												// $afgc_net_amount += $coupon_total_amount;

												?>

												<tr class="order">
													<td>
														<strong><?php echo esc_attr( $afgc_coupon_code ); ?></strong>
													</td>
													<td><?php echo wp_kses_post( wc_price( $coupon_total_amount ) ); ?></td>
													<td>
													<?php
													if ( $coupon_exp ) {
														echo esc_attr( date_format( $coupon_exp, 'M-d-Y' ) );
													} else {
														echo esc_html__( ' - - ', 'addify_gift_cards' );}
													?>
													</td>
													<td>
														<?php
														if ( in_array( 'total_recipient_user_Select', array_keys( $afgc_purchase_for ), true ) ) {

															$total_recp = $afgc_purchase_for['total_recipient_user_Select'];

															$get_array_keys = array_keys( $afgc_purchase_for );

															for ( $i = 0; $i <= $total_recp; $i++ ) {

																if ( in_array( 'afgc_recipient_name' . $i, $get_array_keys, true ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_name' . $i ] ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ) ) {

																	?>

																		<p><?php echo esc_attr( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ); ?></p>

																	<?php

																}
															}
														} else {
															?>

															<p><?php echo esc_attr( $afgc_phy_gift_recipient_email ); ?></p>

																												<?php } ?>

													</td>

													<td>
														<div class="afgc-download-col">

															<a href="?download_gf_id=<?php echo intval( get_the_ID() ); ?>"><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/download-icon.png' ) ); ?>"></a>

														</div>
													</td>
												</tr>

												<?php

											}

										endwhile;

									endif;
									?>
							

								<ul class="afgc-coupon-strip">
									<li><h4><?php echo esc_html__( 'Total: ', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/net.png' ) ); ?>"></h4><?php echo wp_kses_post( wc_price( $afgc_net_amount ) ); ?></li>
								</ul>
									
							</tbody>
						</table>
					</div>
						<div class="pagination adf-dashboard-pagination">
						<?php
						$adf_purch_pagination = paginate_links(array(
							'format'    => '?purchase_paged=%#%',
							'current'   => $paged,
							'total'     => ceil($af_purchase_total_posts / $purchase_posts_per_page),
							'prev_text' => __('« Previous', 'addify_gift_cards'),
							'next_text' => __('Next »', 'addify_gift_cards'),
						));

						if (!empty($adf_purch_pagination)) {
							echo wp_kses_post($adf_purch_pagination);
						}

						?>
					</div>
				</div>
				<div class="tab-content" id="afgc-received" style="display: <?php echo ( 'afgc-received' == $firstSegment ) ? 'block' : 'none'; ?>">
					<h4><?php echo esc_html__( 'Received Gifts', 'addify_gift_cards' ); ?></h4>
					<div class="afgc-gift-account-content">

						<?php

							$my_orders_columns = apply_filters(
								'woocommerce_my_account_my_orders_columns',
								array(
									'afgc_coupon_code'     => esc_html__( 'Gift Card ', 'addify_gift_cards' ),
									'afgc_coupon_rec_date' => esc_html__( 'Received', 'addify_gift_cards' ),
									'afgc_coupon_expiry'   => esc_html__( 'Expiry', 'addify_gift_cards' ),
									'afgc_coupon_price'    => esc_html__( 'Price', 'addify_gift_cards' ),
									'afgc_discount_coupon_amount' => esc_html__( 'Used', 'addify_gift_cards' ),
									'afgc_balance_coupon_amount' => esc_html__( 'Balance', 'addify_gift_cards' ),
									'afgc_download_pdf'    => esc_html__( 'Download', 'addify_gift_cards' ),
								)
							);

						?>

						<table class="shop_table shop_table_responsive my_account_orders">
							<thead>
								<tr>
									<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
										<th class="<?php echo esc_attr( $column_id ); ?>"><span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php
									global $post;
									global $wp_query;

									$coupon_total_amount = 0;

									$afgc_net_amount = 0;

									$afgc_total_used_amount = 0;

									$afgc_balance_amount = 0;

									$page = get_query_var( 'gift-card' );
									$page = str_replace('paged=', '', $page);
									$page = str_replace('afgc-received/page/', '', $page);

									$paged_purchase = isset($_GET['received_paged']) ? absint($_GET['received_paged']) : 1;
									$page           = $paged_purchase ? $paged_purchase : 1;

									$args = array(

										'post_type'      => 'afgc_gift_card_log',

										'post_status'    => 'publish',

										'posts_per_page' => -1,

										'orderby'        => 'id',

										'order'          => 'DESC',
										'meta_query'     => array(
											array(
												'key'     => '_order_id',
												'compare' => 'EXISTS',
											),
										),

									);


									$pre_query      = new WP_Query($args);
									$valid_post_ids = array();
									if ($pre_query->have_posts()) {
										while ($pre_query->have_posts()) {
											$pre_query->the_post();
											$afgc_coupon_code = get_post_meta( get_the_ID(), '_coupon_code', true );

											$coupon = new WC_Coupon( $afgc_coupon_code );

											$restricted_emails = $coupon->get_email_restrictions();

											$coupon_id = $coupon->get_id();

											$coupon_exp = $coupon->get_date_expires();

											$coupon_total_amount = floatval( get_post_meta( $coupon_id, 'coupon_total_amount', true ) );

											$afgc_coupon_created_date = $coupon->get_date_created();

											$afgc_balance_coupon_amount = $coupon->get_amount();

											$afgc_discount_coupon_amount = $coupon_total_amount - $afgc_balance_coupon_amount;

											$afgc_purchase_for = get_post_meta( intval( get_the_ID() ), '_purchase_for', true );

											$afgc_order_id = get_post_meta( intval( get_the_ID() ), '_order_id', true );
											$order         = wc_get_order( $afgc_order_id );

											$billing_email = $order->get_billing_email();

											$afgc_item_id = get_post_meta( intval( get_the_ID() ), '_item_id', true );

											$item = new WC_Order_Item_Product( $afgc_item_id );

											$product = $item->get_product();

											$afgc_product_id = $product->get_id();

											$is_afgc_virtual = get_post_meta( $afgc_product_id, 'afgc_virtual', true );

											$afgc_product_name = $product->get_name();

											if ( 'yes' == $is_afgc_virtual ) {

												if ( in_array( 'total_recipient_user_Select', array_keys( $afgc_purchase_for ), true ) ) {
													$total_recp     = $afgc_purchase_for['total_recipient_user_Select'];
													$get_array_keys = array_keys( $afgc_purchase_for );
													if ($total_recp > 1) {

														for ( $i = 0; $i <= $total_recp; $i++ ) {
															if ( in_array( 'afgc_recipient_name' . $i, $get_array_keys, true ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_name' . $i ] ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ) ) {
																$afgc_virtual_gift_recipient_email[] = $afgc_purchase_for[ 'afgc_recipient_email' . $i ];
															}
														}
													} else {
														$afgc_virtual_gift_recipient_email = (array) $afgc_purchase_for[ 'afgc_recipient_email' . $total_recp ];
													}

												}

												if ( in_array( $current_user_email, $afgc_virtual_gift_recipient_email ) ) {

													$valid_post_ids[]        = get_the_ID();
													$afgc_net_amount        += $coupon_total_amount;
													$afgc_total_used_amount += $afgc_discount_coupon_amount;
													$afgc_balance_amount    += $afgc_balance_coupon_amount;
												}
											} else {

												if ( isset( $afgc_purchase_for['afgc_phy_gift_recipient_email'] ) ) {

													$afgc_phy_gift_recipient_email = $afgc_purchase_for['afgc_phy_gift_recipient_email'];

												} else {

													$afgc_phy_gift_recipient_email = '';
												}

												if ( $current_user_email == $afgc_phy_gift_recipient_email ) {

													$valid_post_ids[]        = get_the_ID();
													$afgc_net_amount        += $coupon_total_amount;
													$afgc_total_used_amount += $afgc_discount_coupon_amount;
													$afgc_balance_amount    += $afgc_balance_coupon_amount;
												}
											}
											
										}
									}
									wp_reset_postdata();

									$total_posts    = count($valid_post_ids);
									$posts_per_page = 8;
									$offset         = ( $page - 1 ) * $posts_per_page;

									// Query only valid posts for the current page
									$args['posts_per_page'] = $posts_per_page;
									$args['post__in']       = $valid_post_ids;
									$args['paged']          = $page;

									$query = new WP_Query( $args );

									if ( $query->have_posts() ) :

										while ( $query->have_posts() ) :

											$query->the_post();

											$afgc_coupon_code = get_post_meta( get_the_ID(), '_coupon_code', true );

											$coupon = new WC_Coupon( $afgc_coupon_code );

											$restricted_emails = $coupon->get_email_restrictions();


											$coupon_id = $coupon->get_id();

											$coupon_exp = $coupon->get_date_expires();

											$coupon_total_amount = floatval( get_post_meta( $coupon_id, 'coupon_total_amount', true ) );

											$afgc_coupon_created_date = $coupon->get_date_created();

											$afgc_balance_coupon_amount = $coupon->get_amount();

											$afgc_discount_coupon_amount = $coupon_total_amount - $afgc_balance_coupon_amount;

											$afgc_purchase_for = get_post_meta( intval( $post->ID ), '_purchase_for', true );

											$afgc_order_id = get_post_meta( intval( $post->ID ), '_order_id', true );

											$order = wc_get_order( $afgc_order_id );

											$billing_email = $order->get_billing_email();

											$afgc_item_id = get_post_meta( intval( $post->ID ), '_item_id', true );

											$item = new WC_Order_Item_Product( $afgc_item_id );

											$product = $item->get_product();

											$afgc_product_id = $product->get_id();

											$is_afgc_virtual = get_post_meta( $afgc_product_id, 'afgc_virtual', true );

											$afgc_product_name = $product->get_name();
											if ( 'yes' == $is_afgc_virtual ) {

												if ( in_array( 'total_recipient_user_Select', array_keys( $afgc_purchase_for ), true ) ) {
													$total_recp     = $afgc_purchase_for['total_recipient_user_Select'];
													$get_array_keys = array_keys( $afgc_purchase_for );
													if ($total_recp > 1) {

														for ( $i = 0; $i <= $total_recp; $i++ ) {
															if ( in_array( 'afgc_recipient_name' . $i, $get_array_keys, true ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_name' . $i ] ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ) ) {
																$afgc_virtual_gift_recipient_email[] = $afgc_purchase_for[ 'afgc_recipient_email' . $i ];
															}
														}
													} else {
														$afgc_virtual_gift_recipient_email = (array) $afgc_purchase_for[ 'afgc_recipient_email' . $total_recp ];
													}

												}

	
												if ( ! in_array( $current_user_email, $afgc_virtual_gift_recipient_email ) ) {

													continue;
												}
											} else {

												if ( isset( $afgc_purchase_for['afgc_phy_gift_recipient_email'] ) ) {

													$afgc_phy_gift_recipient_email = $afgc_purchase_for['afgc_phy_gift_recipient_email'];

												} else {

													$afgc_phy_gift_recipient_email = '';
												}

												if ( $current_user_email != $afgc_phy_gift_recipient_email ) {

													continue;

												}
											}

											if ( $afgc_coupon_code ) {

												?>

												<tr class="order">

													<td><strong><?php echo esc_attr( $afgc_coupon_code ); ?></strong></td>
													<td><?php echo $afgc_coupon_created_date ? esc_attr( gmdate( 'd-m-Y', strtotime( $afgc_coupon_created_date ) ) ) : ''; ?></td>
													<td>
													<?php
													if ( $coupon_exp ) {
														echo esc_attr( date_format( $coupon_exp, 'M-d-Y' ) );
													} else {
														echo esc_html__( '- -', 'addify_gift_cards' );}
													?>
													</td>
													<td><?php echo wp_kses_post( wc_price( $coupon_total_amount ) ); ?></td>
													<td><?php echo wp_kses_post( wc_price( $afgc_discount_coupon_amount ) ); ?></td>
													<td><?php echo wp_kses_post( wc_price( $afgc_balance_coupon_amount ) ); ?></td>
													<td>
														<div class="afgc-download-col">

															<a href="?download_gf_id=<?php echo intval( get_the_ID() ); ?>"><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/download-icon.png' ) ); ?>"></a>

														</div>
													</td>
											
												</tr>

												<?php

											}

								endwhile;

								endif;

									?>

									<ul class="afgc-coupon-strip">
										<li><h4><?php echo esc_html__( 'Total: ', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/net.png' ) ); ?>"></h4><?php echo wp_kses_post( wc_price( $afgc_net_amount ) ); ?></li>
										<li><h4><?php echo esc_html__( 'Used: ', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/debit.png' ) ); ?>"></h4><?php echo wp_kses_post( wc_price( $afgc_total_used_amount ) ); ?></li>
										<li><h4><?php echo esc_html__( 'Balance: ', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/credit.png' ) ); ?>"></h4><?php echo wp_kses_post( wc_price( $afgc_balance_amount ) ); ?></li>
									</ul>
							</tbody>
						</table>
						<div class="pagination adf-dashboard-pagination">
							<?php
							$pagination = paginate_links(array(
								'format'    => '?received_paged=%#%',
								'current'   => $page,
								'total'     => ceil($total_posts / $posts_per_page),
								'prev_text' => __('« Previous', 'addify_gift_cards'),
								'next_text' => __('Next »', 'addify_gift_cards'),
							));

							if (!empty($pagination)) {
								echo wp_kses_post($pagination);
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php
	}

	public function afgc_order_created_cb( $order ) {

		foreach ( $order->get_items() as $item_key => $item ) {

			$product = $item->get_product();

			if ( $product->is_type( 'gift_card' ) ) {

				$old_data_card_purchase = (array) get_user_meta( get_current_user_id(), 'gift_card_purchase_for_user', true );

				$old_data_card_purchase[] = $item->get_id();

				update_user_meta( get_current_user_id(), 'gift_card_purchase_for_user', $old_data_card_purchase );

			}
		}
	}

	public function afgc_update_product_data( $item, $cart_item_key, $values, $order ) {

		$order_id = $order->get_id();

		foreach ( WC()->cart->get_cart() as $item_key => $value_check ) {

			$total_files_in_array = 0;

			if ( ! empty( $value_check ) && $item_key === $cart_item_key && array_key_exists( 'afgc_gift_card_infos', $value_check ) ) {

				$get_data_of_files = $value_check['afgc_gift_card_infos'];

				$item->add_meta_data( 'afgc_gift_card_infos', $get_data_of_files );

			}
		}
	}

	// Display Giftcard Products Shortcodes.
	public function afgc_show_gift_cards_cb() {

		ob_start();

		include AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-shortcode-temp.php';

		return ob_get_clean();
	}
}

new Af_Gift_Card_Front();


