<?php

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

// reference the Dompdf namespace
use Dompdf\Dompdf;

class Af_Gift_Card_Admin {

	// Constructor.
	public function __construct() {

		// Include Gift Card PDF.
		add_action( 'wp_loaded', array( $this, 'afgc_include_pdf_code_cb' ) );

		// Scripts Enqueue.
		add_action( 'admin_enqueue_scripts', array( $this, 'afgc_admin_scripts' ) );

		// Add submenu Dashboard to Woocommerce Menu.
		add_action( 'admin_menu', array( $this, 'afgc_add_dashboard_menu_item_cb' ) );

		// Include Gift Card Dashboard Files.
		add_action( 'admin_init', array( $this, 'afgc_add_dashboard_tabs_cb' ), 10 );

		// Product Select 2.
		add_action( 'wp_ajax_afgc_select_products', array( $this, 'afgc_select_products' ) );

		add_action( 'wp_ajax_nopriv_afgc_select_products', array( $this, 'afgc_select_products' ) );

		// Product Categories Select 2.
		add_action( 'wp_ajax_afgc_category_search', array( $this, 'afgc_category_search' ) );

		add_action( 'wp_ajax_nopriv_afgc_category_search', array( $this, 'afgc_category_search' ) );

		// Add meta box to Gift Card Log.
		add_action( 'add_meta_boxes', array( $this, 'afgc_add_metabox_gift_card_log_cb' ) );

		// Include Setting Files For Gift Card.
		add_action( 'woocommerce_get_settings_pages', array( $this, 'afgc_add_setting_page' ) );

		add_action( 'woocommerce_after_order_itemmeta', array( $this, 'afgc_prd_files_with_line_item' ), 10, 3 );

		// Conditionally remove duplicate submenu link.
		add_action( 'admin_menu', array( $this, 'afgc_remove_submenu_link_cb' ), 100 );

		// Render tabs of Gift Card.
		add_action( 'all_admin_notices', array( $this, 'afgc_render_tabs_cb' ), 5 );

		// Export CSV
		add_action( 'manage_posts_extra_tablenav', array( $this, 'agfc_export_csv_button_nav' ) );
		add_action( 'admin_init', array( $this, 'agfc_export_coupon_handle' ) );

		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'agfc_this_product_as_gift_pro_level' ));
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'agfc_add_gift_option_to_variations' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'agfc_save_variation_gift_settings' ), 10, 2 );
		add_action( 'woocommerce_process_product_meta', array( $this, 'agfc_this_product_as_gift_pro_level_save' ));
		add_action( 'pre_get_posts', array( $this, 'afgc_admin_search_coupon_code' ) );
	}

	public function agfc_this_product_as_gift_pro_level() {
		global $product;

		$product_id = get_the_ID();
		if ( empty( $product ) ) {
			$product = wc_get_product( $product_id );
		}

		if ( ! $product || ! in_array( $product->get_type(), array( 'simple' ), true ) ) {
			return;
		}

		$virtual_gift_card = get_post_meta( $product_id, '_agfc_selected_gift_card', true );

		woocommerce_wp_checkbox( array(
			'id'          => '_agfc_current_product_gift_checkbox',
			'label'       => __( 'Enable Product Gift', 'addify_gift_cards' ),
			'description' => __( 'Enable to add this product as a gift.', 'addify_gift_cards' ),
		) );
		
		woocommerce_wp_select( array(
			'id'          => '_agfc_selected_gift_card',
			'label'       => __( 'Select Gift Card Product', 'addify_gift_cards' ),
			'options'     => $this->afgc_get_gift_card_options(),
			'desc_tip'    => true,
			'description' => __( 'Choose which gift card to redirect this gift card.', 'addify_gift_cards' ),
			'value'       => $virtual_gift_card,
		) );

		wp_nonce_field( 'agf_exclude_product_nonce_action', 'agf_product_as_gift_nonce' );
	}

	public function agfc_add_gift_option_to_variations( $loop, $variation_data, $variation ) {

		woocommerce_wp_checkbox( array(
			'id'          => "_agfc_variation_gift_checkbox_{$variation->ID}",
			'name'        => "_agfc_variation_gift_checkbox[{$variation->ID}]",
			'label'       => __( 'Enable Product Gift', 'addify_gift_cards' ),
			'description' => __( 'Enable to add this variation as a gift.', 'addify_gift_cards' ),
			'desc_tip'    => true,
			'value'       => get_post_meta( $variation->ID, '_agfc_variation_gift_checkbox', true ),
			'class'       => 'afgc-gift-field afgc-variation-check',
		) );
	  
		woocommerce_wp_select( array(
			'id'          => "_agfc_variation_gift_card_{$variation->ID}",
			'name'        => "_agfc_variation_gift_card[{$variation->ID}]",
			'label'       => __( 'Select Gift Card Product', 'addify_gift_cards' ),
			'options'     => $this->afgc_get_gift_card_options(),
			'value'       => get_post_meta( $variation->ID, '_agfc_variation_gift_card', true ),
			'description' => __( 'Choose which gift card to redirect this variation gift.', 'addify_gift_cards' ),
			'desc_tip'    => true,
			'class'       => 'afgc-gift-field afgc-variation-select',
		) );

		wp_nonce_field( 'agfc_save_variation_gift_settings', 'agfc_variation_nonce' );
	}

	public function afgc_get_gift_card_options() {
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'gift_card' ),
				),
			),
		);

		$giftcard_query = new WP_Query( $args );
		$options        = array( '' => __( '-- Select a Gift Card --', 'addify_gift_cards' ) );

		if ( $giftcard_query->have_posts() ) {
			while ( $giftcard_query->have_posts() ) {
				$giftcard_query->the_post();
				$virtual_gift_card = get_post_meta( get_the_ID(), 'afgc_virtual', true );
				if ( 'yes' !== $virtual_gift_card ) {
					continue;
				}
				$options[ get_the_ID() ] = get_the_title();
			}
		}
		wp_reset_postdata();
		return $options;
	}

	public function agfc_this_product_as_gift_pro_level_save( $post_id ) {
		if ( ! isset( $_POST['agf_product_as_gift_nonce'] ) || 
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['agf_product_as_gift_nonce'] ) ), 'agf_exclude_product_nonce_action' ) ) {
			return;
		}
		$value = isset( $_POST['_agfc_current_product_gift_checkbox'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_agfc_current_product_gift_checkbox', $value );

		if ( isset( $_POST['_agfc_selected_gift_card'] ) ) {
			update_post_meta(
				$post_id,
				'_agfc_selected_gift_card',
				absint( $_POST['_agfc_selected_gift_card'] )
			);
		}
	}

	public function agfc_save_variation_gift_settings( $variation_id, $i ) {
		if ( ! isset( $_POST['agfc_variation_nonce'] ) || 
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['agfc_variation_nonce'] ) ), 'agfc_save_variation_gift_settings' ) ) {
			return;
		}
		if ( isset( $_POST['_agfc_variation_gift_checkbox'][ $variation_id ] ) ) {
			update_post_meta( $variation_id, '_agfc_variation_gift_checkbox', 'yes' );
		} else {
			update_post_meta( $variation_id, '_agfc_variation_gift_checkbox', 'no' );
		}

		if ( isset( $_POST['_agfc_variation_gift_card'][ $variation_id ] ) ) {
			update_post_meta( $variation_id, '_agfc_variation_gift_card', sanitize_text_field( $_POST['_agfc_variation_gift_card'][ $variation_id ] ) );
		}
	}

	// Scripts Enqueue.
	public function afgc_admin_scripts() {

		$screen = get_current_screen();

		if ( 'edit-afgc_gallery_cat' == $screen->id || 'edit-afgc_gift_card_log' == $screen->id || 'woocommerce_page_afgc_dashboard ' == $screen->id || 'woocommerce_page_wc-settings' == $screen->id || 'product' == $screen->id || 'woocommerce_page_afgc_dashboard' == $screen->base ) {

			wp_enqueue_style( 'afgc-admin', plugins_url( '/assets/css/afgc-admin.css', __FILE__ ), false, '1.0.0' );

			wp_enqueue_script( 'jquery' );

			wp_enqueue_media();

			wp_enqueue_script( 'afgc-admin', plugins_url( '/assets/js/afgc-admin.js', __FILE__ ), array(), '1.0.0', false );

			if ( defined( 'WC_PLUGIN_FILE' ) ) {

				wp_enqueue_style( 'wc-select2', plugins_url( 'assets/css/select2.css', WC_PLUGIN_FILE ), array(), '5.7.2' );
				wp_enqueue_script( 'wc-select2', plugins_url( 'assets/js/select2/select2.min.js', WC_PLUGIN_FILE ), array( 'jquery' ), '4.0.3', true );
			}
		}

		$addify_gift_card_ajax_data = array(

			'admin_url' => admin_url( 'admin-ajax.php' ),

			'nonce'     => wp_create_nonce( 'addify_agf_nonce' ),

		);

		wp_localize_script( 'afgc-admin', 'k_php_var', $addify_gift_card_ajax_data );
	}


	public function afgc_include_pdf_code_cb() {

		if ( isset( $_GET['download_gf_id'] ) ) {

			require_once AFGC_PLUGIN_DIR . 'vendor/autoload.php';

			ob_start();

			include_once AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-pdf-template.php';

			$temp = ob_get_clean();

			$dompdf = new Dompdf();

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
			$dompdf->stream($afgc_pdf_file_name . '.pdf');

		}
	}

	public function afgc_admin_search_coupon_code( $query ) {
		global $pagenow;

		if (
			is_admin()
			&& 'edit.php' === $pagenow
			&& isset( $_GET['post_type'] )
			&& 'afgc_gift_card_log' === $_GET['post_type']
			&& ! empty( $_GET['s'] )
		) {
			$search_term = sanitize_text_field( $_GET['s'] );
			$query->set( 's', '' );

			$meta_query = array(
				array(
					'key'     => '_coupon_code',
					'value'   => $search_term,
					'compare' => 'LIKE',
				),
			);

			$query->set( 'meta_query', $meta_query );
		}
	}

	// Add submenu Dashboard to Woocommerce Menu.
	public function afgc_add_dashboard_menu_item_cb() {

		add_submenu_page(
			'woocommerce',
			__( 'Gift Cards', 'addify_gift_cards' ),
			__( 'Gift Cards', 'addify_gift_cards' ),
			'manage_options',
			'afgc_dashboard',
			array( $this, 'afgc_gift_card_tabs_cb' ),
			10
		);
	}


	// Gift Card Sub Menu callback.
	public function afgc_gift_card_tabs_cb() {

		?>

		<div class="afgc-tabs-section">

			<form method="post" action="options.php" class="afgc_options_form">    

				<?php

				if ( isset( $_GET['page'] ) && 'afgc_dashboard' === $_GET['page'] ) {

					settings_fields( 'afgc_dashboard_fields' );

					do_settings_sections( 'afgc_dashboard_section' );

				}

				?>

			</form> 

		</div>
		
		<?php
	}


	// Include Gift Card Dashboard Files.
	public function afgc_add_dashboard_tabs_cb() {

		include_once AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-dashboard-temp.php';
	}


	// Conditionally remove duplicate submenu link.
	public function afgc_remove_submenu_link_cb() {

		global $pagenow, $typenow;

		if ( ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'afgc_dashboard' === sanitize_text_field( wp_unslash( $_GET['page'] ) ) ) ) {

			remove_submenu_page( 'woocommerce', 'edit.php?post_type=afgc_gift_card_log' );

		} elseif ( ( 'edit.php' === $pagenow && 'afgc_gift_card_log' === $typenow ) || ( 'post.php' === $pagenow && isset( $_GET['post'] ) && 'afgc_gift_card_log' === get_post_type( sanitize_text_field( wp_unslash( $_GET['post'] ) ) ) ) ) {

			remove_submenu_page( 'woocommerce', 'afgc_dashboard' );

		} else {

			remove_submenu_page( 'woocommerce', 'edit.php?post_type=afgc_gift_card_log' );

		}
	}


	// Render tabs of Gift Card.
	public function afgc_render_tabs_cb() {

		global $post, $typenow;

		$screen = get_current_screen();

		if ( $screen && in_array( $screen->id, $this->get_tab_screen_ids(), true ) ) {

			$tabs = apply_filters(
				'afgc_admin_tabs',
				array(

					'dashboard'         => array(

						'title' => __( 'Dashboard', 'addify_gift_cards' ),

						'url'   => admin_url( 'admin.php?page=afgc_dashboard' ),

					),
					'logs'              => array(

						'title' => __( 'Gift Card Logs', 'addify_gift_cards' ),

						'url'   => admin_url( 'edit.php?post_type=afgc_gift_card_log' ),
					),
					'afgc_card_gallery' => array(

						'title' => __( 'Gift Card Gallery', 'addify_gift_cards' ),

						'url'   => admin_url( 'edit-tags.php?taxonomy=afgc_gallery_cat&post_type=product' ),
					),

				)
			);

			if ( is_array( $tabs ) ) {
				?>

				<div class="wrap woocommerce">

					<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">

						<?php

						$current_tab = $this->get_current_tab();

						foreach ( $tabs as $id => $tab_data ) {

							$class = $id === $current_tab ? array( 'nav-tab', 'nav-tab-active' ) : array( 'nav-tab' );

							printf( '<a href="%1$s" class="%2$s">%3$s</a>', esc_url( $tab_data['url'] ), implode( ' ', array_map( 'sanitize_html_class', $class ) ), esc_html( $tab_data['title'] ) );

						}

						?>

					</h2>

				</div>

				<?php

			}
		}
	}


	// Get Current Tab.
	public function get_current_tab() {

		$screen = get_current_screen();

		$tabs_screens = array(
			'afgc_gift_card_log',
			'edit-afgc_gift_card_log',
			'woocommerce_page_afgc_dashboard',
			'afgc_gallery_cat',
			'edit_afgc_gallery_cat',
		);

		switch ( $screen->id ) {

			case 'afgc_gift_card_log':
			case 'edit-afgc_gift_card_log':
				return 'logs';

			case 'woocommerce_page_afgc_dashboard':
				return 'dashboard';

			case 'afgc_gallery_cat':
			case 'edit-afgc_gallery_cat':
				return 'afgc_card_gallery';

		}
	}


	public function get_tab_screen_ids() {

		$tabs_screens = array(
			'woocommerce_page_afgc_dashboard',
			'afgc_gift_card_log',
			'edit-afgc_gift_card_log',
			'afgc_gallery_cat',
			'edit-afgc_gallery_cat',
		);

		return $tabs_screens;
	}


	public function afgc_add_setting_page( $settings ) {

		$settings[] = include_once AFGC_PLUGIN_DIR . '/admin/includes/settings/class-af-gift-card-settings-tab.php';

		return $settings;
	}


	// Select Product Select 2.
	public function afgc_select_products() {

		if ( isset( $_POST['nonce'] ) && '' != $_POST['nonce'] ) {

			$nonce = sanitize_text_field( wp_unslash( $_POST['nonce'] ) );

		} else {

			$nonce = 0;

		}

		if ( isset( $_POST['q'] ) && '' != $_POST['q'] ) {

			if ( ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {

				die( 'Failed ajax security check!' );

			}

			$pro = sanitize_text_field( wp_unslash( $_POST['q'] ) );

		} else {

			$pro = '';

		}

		$data_array = array();

		$args = array(
			'post_type'   => 'product',

			'post_status' => 'publish',

			'numberposts' => 50,

			's'           => $pro,

		);

		$pros = get_posts( $args );

		if ( ! empty( $pros ) ) {

			foreach ( $pros as $proo ) {

				$title = ( mb_strlen( $proo->post_title ) > 50 ) ? mb_substr( $proo->post_title, 0, 49 ) . '...' : $proo->post_title;

				$data_array[] = array( $proo->ID, $title );

			}
		}

		echo wp_json_encode( $data_array );

		die();
	}



	// Product Categories Select 2.
	public function afgc_category_search() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;

		if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {

			die( 'Failed Ajax security check!' );

		}

		$s = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';

		$args = array(

			'taxonomy'   => 'product_cat',

			'hide_empty' => false,

			'name__like' => $s,

		);

		$categories = get_terms( $args );

		$data_array = array();

		if ( ! empty( $categories ) ) {

			foreach ( $categories as $category ) {

				$title = ( mb_strlen( $category->name ) > 50 ) ? mb_substr( $category->name, 0, 49 ) . '...' : $category->name;

				$data_array[] = array( $category->term_id, $title );

			}
		}

		wp_send_json( $data_array );

		die();
	}



	public function afgc_prd_files_with_line_item( $item_id, $item, $product ) {

		$meta_data = $item->get_meta_data();

		foreach ( $meta_data as $meta ) {

			$data = $meta->get_data();

			if ( ! empty( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {

				$array_of_card_info = $data['value'];

				if ( ! empty( $array_of_card_info['total_recipient_user_Select'] ) ) {
					$total_field = $array_of_card_info['total_recipient_user_Select'];
				} else {
					$total_field = -1;
				}

				?>

				<ul style="list-style: none; margin:5px 0px;">
					<?php if ( ! empty( $array_of_card_info['afgc_sender_name'] ) ) { ?>

						<li><label><?php echo esc_html__( 'Sender Name : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_sender_name'] ); ?></li>	

					<?php } elseif ( isset($array_of_card_info['afgc_phy_gift_sender_name'] ) ) { ?>

						<li><label><?php echo esc_html__( 'Sender Name :', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_sender_name'] ); ?></li>

					<?php
					}
					
					if ( ! empty( $array_of_card_info['afgc_sender_message'] ) ) {
						?>

						<li><label><?php echo esc_html__( 'Sender Message : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_sender_message'] ); ?></li>	

						<?php
					} elseif ( isset($array_of_card_info['afgc_phy_gift_sender_message'] ) ) {
						?>
						<li><label><?php echo esc_html__( 'Sender Message : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_sender_message'] ); ?></li>
						<?php
					}

					for ( $i = 0; $i <= $total_field; $i++ ) {

						if ( ! empty( $array_of_card_info[ 'afgc_recipient_name' . $i ] ) ) {
							?>

							<li><label><?php echo esc_html__( 'Recipient Name : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info[ 'afgc_recipient_name' . $i ] ); ?></li>	

						<?php } ?>

						<?php if ( ! empty( $array_of_card_info[ 'afgc_recipient_email' . $i ] ) ) { ?>

							<li><label><?php echo esc_html__( 'Recipient Email : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info[ 'afgc_recipient_email' . $i ] ); ?></li>		

							<?php
						}
					}

					if ( ! empty( $array_of_card_info['afgc_phy_gift_recipient_name'] ) ) {
						?>
						<li><label><?php echo esc_html__( 'Recipient Name : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_recipient_name'] ); ?></li>

						<?php
					}

					if ( ! empty( $array_of_card_info['afgc_delivery_date'] ) ) {
						?>

						<li><label><?php echo esc_html__( 'Delivery Date : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_delivery_date'] ); ?></li>		

					<?php
					}
					if (! empty( $array_of_card_info['afgc_phy_gift_delivery_date'] )) {
						?>
						<li><label><?php echo esc_html__( 'Delivery Date : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_delivery_date'] ); ?></li>
						<?php
					}
					if ( ! empty( $array_of_card_info['afgc_price_of_gift_card'] ) ) {
						?>

						<li><label><?php echo esc_html__( ' Price : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_price_of_gift_card'] ); ?></li>

					<?php
					}
					
					if ( ! empty( $array_of_card_info['afgc_gift_products'] ) ) {
						$product      = wc_get_product($array_of_card_info['afgc_gift_products']);
						$product_name = $product->get_name();
						?>

						<li><label><?php echo esc_html__( ' Product Gift : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $product_name ); ?></li>

					<?php } ?>


				</ul>

				<?php

			}
		}
	}


	// Custom Meta Box For Gift Card Details.
	public function afgc_add_metabox_gift_card_log_cb() {

		add_meta_box(
			'afgc_gift_card_details',
			esc_html__( 'Gift Card', 'addify_gift_cards' ),
			array( $this, 'afgc_gift_card_details_cb' ),
			'afgc_gift_card_log'
		);
	}

	public function afgc_gift_card_details_cb() {

		include_once AFGC_PLUGIN_DIR . '/admin/includes/meta-boxes/afgc-gift-card-info.php';
	}
	public function agfc_export_csv_button_nav( $which ) {
		global $typenow;

		if ( 'afgc_gift_card_log' === $typenow && 'top' === $which ) {
			echo '<a href="' . esc_url( add_query_arg( array(
				'action'    => 'agf_export_cpt_csv',
				'post_type' => $typenow,
				'_wpnonce'  => wp_create_nonce( 'agf_export_cpt_csv' ),
			), admin_url( 'edit.php' ) ) ) . '" class="button">Export CSV</a>';
		}
	}
	public function agfc_export_coupon_handle() {
		if ( isset( $_GET['action'] ) && 'agf_export_cpt_csv' === $_GET['action'] ) {
			$adf_nonce = isset($_GET['_wpnonce']) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) :'';
			if ( ! wp_verify_nonce( $adf_nonce, 'agf_export_cpt_csv' ) ) {
				wp_die( 'Security check failed' );
			}

			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : 'gift_card';
			$query     = new WP_Query( array(
				'post_type'      => $post_type,
				'posts_per_page' => -1,
				'post_status'    => 'any',
			) );

			if ( ! $query->have_posts() ) {
				wp_die( 'No gift cards found.' );
			}

			header( 'Content-Type: text/csv; charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=gift-cards.csv' );

			$output = fopen( 'php://output', 'w' );

			fputcsv( $output, array( 'Gift Card Name', 'Coupon Code', 'Used Amount', 'Remaining Amount', 'Total Amount', 'Expire' ) );

			foreach ( $query->posts as $post ) {
				$post_id                = $post->ID;
				$afgc_coupon_code       = get_post_meta( $post_id, '_coupon_code', true );
				$afgc_gift_card_status  = get_post_meta( $post_id, '_gift_card_status', true );
				$afgc_coupon_rem_amount = get_post_meta( $post_id, '_afgc_coupon_rem_amount', true );
				$coupon                 = new WC_Coupon( $afgc_coupon_code );
				$coupon_id              = $coupon->get_id();
				$coupon_amount          = $coupon->get_amount();
				$coupon_exp             = $coupon->get_date_expires();
				$coupon_expire          = isset($coupon_exp) ? date_format( $coupon_exp, 'M-d-Y' ) : '--';

				$coupon_total_amount = floatval( get_post_meta( $coupon->get_id(), 'coupon_total_amount', true ) );
				$currency_symbol     = html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' );
				
				if ( 'purchased' == $afgc_gift_card_status ) {

					$used_amount           = 0;
					$afgc_remaining_amount = $coupon_total_amount;

				} elseif ( 'pending' == $afgc_gift_card_status ) {

					$used_amount           = 0;
					$afgc_remaining_amount = $coupon_amount;

				} elseif ( 'partial delivered' == $afgc_gift_card_status ) {

					$used_amount           = (int) $coupon_total_amount - (int) $afgc_coupon_rem_amount;
					$afgc_remaining_amount = $afgc_coupon_rem_amount;

				} elseif ( 'accomplish' == $afgc_gift_card_status ) {

					$used_amount           = $coupon_total_amount;
					$afgc_remaining_amount = 0;

				} else {
					$used_amount           = 0;
					$afgc_remaining_amount = 0;
				}

				fputcsv( $output, array(
					$post->post_title,
					$afgc_coupon_code,
					$currency_symbol . ' ' . wc_format_decimal( $used_amount, wc_get_price_decimals() ),
					$currency_symbol . ' ' . wc_format_decimal( $afgc_remaining_amount, wc_get_price_decimals() ),
					$currency_symbol . ' ' . wc_format_decimal( $coupon_total_amount ? $coupon_total_amount : 0, wc_get_price_decimals() ),
					$coupon_expire,
				) );
			}

			fclose( $output );
			exit;
		}
	}
}

new Af_Gift_Card_Admin();