<?php

/**
 * Plugin Name:   Advanced Gift Card
 * Plugin URI:    http://www.woocommerce.com/product/advanced-gift-card/
 * Description:   Allow your customers to purchase gift cards for their loved ones for multiple occasions like birthday, wedding, anniversaries, etc.
 * Version:       1.7.1
 * Author:        Addify
 * Developed By:  Addify
 * Author URI:    https://woocommerce.com/vendor/addify/
 * Support:       https://woocommerce.com/vendor/addify/
 * License:      GNU General Public License v3.0
 * License URI:  http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path:   /languages
 * Text Domain:  addify_gift_cards
 * Requires Plugins: woocommerce
 * WC requires at least: 4.0
 * WC tested up to: 10.*.*
 * Requires at least: 6.5
 * Tested up to: 6.*.*
 * Requires PHP: 7.4
 * Woo: 18734001731719:baa66361bf0f435421ed623bb24d3973

 **/

if ( ! defined( 'ABSPATH' ) ) {
	die;
}


if ( ! class_exists( 'Addify_Gift_Card' ) ) {

	class Addify_Gift_Card {

		public function __construct() {

			$this->afgc_global_constents_vars();

			// Gift Card Post Type Taxonomies.
			add_action( 'init', array( $this, 'afgc_gift_card_checking_woocommerce_is_enable_or_not' ) );

			// HOPS compatibility.
			add_action( 'before_woocommerce_init', array( $this, 'af_gift_HOPS_Compatibility' ) );

			add_action( 'plugins_loaded', array( $this, 'af_agc_init' ) );

				// Include Gift Card Custom Product Type Class.
			add_action( 'plugins_loaded', array( $this, 'afgc_register_gift_card_type' ), 100 );

				// Add Gift Card Custom Product Type.
			add_filter( 'product_type_selector', array( $this, 'afgc_add_gift_card_type_cb' ) );

				// Add Gift Card Tab for Custom Product Type.
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'afgc_add_gift_card_tab_cb' ) );

				// Add Product Type for Gift Card Custom Product Type Virtual / Physical.
			add_filter( 'product_type_options', array( $this, 'afgc_gift_card_type_opt_cb' ) );

			add_filter('cron_schedules', array( $this, 'afgc_devlivery_cron_schedules' ) );

			// cronjob
			add_action('afgc_check_last_20_days_orders', array( $this, 'adf_check_last_orders_for_delivery' ) );
			add_action( 'init', array( $this, 'af_remove_add_new_button_for_gift_card' ) );
			// unschedule the cron job.
			register_activation_hook(__FILE__, array( $this, 'afgc_cron_schedule_cb' ));
			register_deactivation_hook(__FILE__, array( $this, 'afgc_cron_unschedule_cb' ));
		}

		public function af_agc_init() {

			// Check the installation of WooCommerce module if it is not a multi site.
			if ( ! is_multisite() && ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
				add_action( 'admin_notices', array( $this, 'af_agc_check_wocommerce' ) );
			}
		}


		public function af_agc_check_wocommerce() {
			// Deactivate the plugin.
			deactivate_plugins( __FILE__ );
			?>
			<div id="message" class="error">
				<p>
					<strong>
						<?php esc_html_e( 'Advanced Gift Card plugin is inactive. WooCommerce plugin must be active in order to activate it.', 'addify_gift_cards' ); ?>
					</strong>
				</p>
			</div>
			<?php
		}

		public function af_gift_HOPS_Compatibility() {

			if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}


		public function afgc_global_constents_vars() {

			if ( ! defined( 'AFGC_URL' ) ) {
				define( 'AFGC_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'AFGC_BASENAME' ) ) {
				define( 'AFGC_BASENAME', plugin_basename( __FILE__ ) );
			}

			if ( ! defined( 'AFGC_PLUGIN_DIR' ) ) {
				define( 'AFGC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'AFGC_PLUGIN_URL' ) ) {
				define( 'AFGC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			if ( ! defined( 'AFGC_PLUGIN_VERSION' ) ) {
				define( 'AFGC_PLUGIN_VERSION', '1.7.1' );
			}
		}

		// Include Theme CSS File.
		public function afgc_include_theme_css_file_cb() {

			include_once AFGC_PLUGIN_DIR . '/admin/includes/settings/afgc-theme-skin.php';
		}

		// On Plugin Activation Save Gift Card Settings.
		public function afgc_register_settings_cb() {

			// Enable Store Logo on PDF.
			if ( empty( get_option( 'afgc_enable_store_logo_pdf' ) ) ) {
				update_option( 'afgc_enable_store_logo_pdf', 'yes', true );
			}
			// Enable Gift Card Image on PDF.
			if ( empty( get_option( 'afgc_enable_gift_card_image' ) ) ) {
				update_option( 'afgc_enable_gift_card_image', 'yes', true );
			}

			// Enable Gift Card Name on PDF.
			if ( empty( get_option( 'afgc_enable_gift_card_name' ) ) ) {
				update_option( 'afgc_enable_gift_card_name', 'yes', true );
			}

			// Enable Gift Card Price on PDF.
			if ( empty( get_option( 'afgc_enable_gift_card_price' ) ) ) {
				update_option( 'afgc_enable_gift_card_price', 'yes', true );
			}

			// Enable Coupon Code on PDF.
			if ( empty( get_option( 'afgc_enable_gift_card_code' ) ) ) {
				update_option( 'afgc_enable_gift_card_code', 'yes', true );
			}

			// Hide Gift Card Mesage on PDF.
			if ( empty( get_option( 'afgc_enable_gift_card_message' ) ) ) {
				update_option( 'afgc_enable_gift_card_message', 'no', true );
			}

			// Hide Gift Card Disclaimer on PDF.
			if ( empty( get_option( 'afgc_enable_disclaimer' ) ) ) {
				update_option( 'afgc_enable_disclaimer', 'no', true );
			}
		}

		// Register Taxonomies callback.
		public function afgc_gift_card_checking_woocommerce_is_enable_or_not() {

			if ( defined( 'WC_PLUGIN_FILE' ) ) {

				// Register Post Type.
				$this->afgc_agf_register_post_type_and_taxonomies();

				include_once dirname( WC_PLUGIN_FILE ) . '/includes/emails/class-wc-email.php';
				include_once AFGC_PLUGIN_DIR . '/front/includes/templates/class-af-gift-card-coupon-code.php';

				include_once AFGC_PLUGIN_DIR . '/front/includes/emails/class-af-gift-card-mail.php';

				// On Plugin Activation Save Gift Card Settings.
				register_activation_hook( __FILE__, array( $this, 'afgc_register_settings_cb' ) );

				// Add Gift Card Logs columns .
				add_filter( 'manage_afgc_gift_card_log_posts_columns', array( $this, 'afgc_gift_card_logs_columns_cb' ) );

				// Add Values to Gift Card Logs columns.
				add_action( 'manage_afgc_gift_card_log_posts_custom_column', array( $this, 'afgc_fill_gift_card_logs_columns_cb' ), 10, 2 );

				// Save Gift Card Term.
				add_action( 'created_term', array( $this, 'afgc_save_category_fields_callback' ) );

				// Edit Gift Card Term.
				add_action( 'edit_term', array( $this, 'afgc_edit_category_fields_callback' ) );

				// Add Gift Gallery Thumbnail.
				add_action( 'afgc_gallery_cat_add_form_fields', array( $this, 'afgc_add_category_gallery_cb' ) );

				// Edit Gift Gallery Thumbnail.
				add_action( 'afgc_gallery_cat_edit_form_fields', array( $this, 'afgc_edit_category_gallery_cb' ), 100 );

				// Delete Gift Gallery Thumbnail.
				add_action( 'wp_ajax_afgc_delete_update_gift_gallery_images', array( $this, 'afgc_delete_update_gift_gallery_images' ) );

				add_action( 'save_post_product', array( $this, 'afgc_save_gift_card_type_opt_cb' ), 10, 3 );

				add_filter( 'woocommerce_email_classes', array( $this, 'afgc_coupon_code_file_cb' ), 100, 1 );

				// Showing Gift card detail to user on edit order.
				add_action( 'woocommerce_order_item_meta_start', array( $this, 'afgc_gift_card_detail_on_edit_cb' ), 10, 3 );

				// Showing Gift card Thumbnail to user on edit order.
				add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'afgc_gift_card_thumb_on_edit_cb' ), 10, 3 );

				// Add Fields in Gift Card Tab Custom Product Type.
				add_action( 'woocommerce_product_data_panels', array( $this, 'afgc_gift_card_options_product_tab_content' ) );

				// Save Gift Card Product Type Metabox.
				add_action( 'woocommerce_process_product_meta', array( $this, 'afgc_save_gift_card_options_field' ), 100 );

				// Include Modal & Gift Card Product Type Templates.
				add_action( 'woocommerce_single_product_summary', array( $this, 'afgc_gift_card_product_temp_cb' ), 20 );

				// Create Gift Card Log and check Gift Card in order on order hold.
				add_action( 'woocommerce_order_status_completed', array( $this, 'afgc_check_gift_card_in_order_cb' ), 100, 1 );

				// Send Coupon Code In Email.
				add_action( 'woocommerce_order_status_completed', array( $this, 'afgc_coupon_code_email_cb' ), 100, 1 );

				// Match Gift Card in order.
				add_action( 'woocommerce_order_status_completed', array( $this, 'afgc_match_gift_card_in_order_cb' ), 10, 1 );

				// Receient Email Body Classes.
				add_filter( 'woocommerce_email_classes', array( $this, 'afgc_recipent_email_body_cb' ), 90, 1 );

				// Include Theme CSS File.
				add_action( 'wp_head', array( $this, 'afgc_include_theme_css_file_cb' ) );

				// Upload Custom Gift Card Image Ajax Callback.
				add_action( 'wp_ajax_afgc_cuctom_card_image_cb', array( $this, 'afgc_cuctom_card_image_cb' ) );
				add_action( 'wp_ajax_nopriv_afgc_cuctom_card_image_cb', array( $this, 'afgc_cuctom_card_image_cb' ) );

				// Price Html for Gift Product On Shop Page.
				add_filter( 'woocommerce_get_price_html', array( $this, 'afgc_product_price_shop_page_cb' ), 10, 2 );

				// Display Gift Card Product on Shop page  Depended on user Role.
				add_filter( 'woocommerce_product_is_visible', array( $this, 'afgc_gift_card_for_user_roles' ), 10, 2 );

				// Redirect Product to 404 for unselected User Role.
				add_action( 'template_redirect', array( $this, 'afgc_redirect_404_cb' ) );
				add_action( 'wp_loaded', array( $this, 'afgc_apply_coupon_from_url' ) );
				add_action( 'wp_ajax_product_search', array( $this, 'afgc_product_search' ) );
				add_action( 'wp_ajax_nopriv_product_search', array( $this, 'afgc_product_search' ) );
				if ( is_admin() ) {

					include_once AFGC_PLUGIN_DIR . 'admin/class-af-gift-card-admin.php';

				} else {

					include_once AFGC_PLUGIN_DIR . 'front/class-af-gift-card-front.php';

				}

				// Load Elementor integration.
				include_once AFGC_PLUGIN_DIR . 'elementor/class-afgc-elementor-integration.php';
			}
		}

		// Gift Card Log Post Type Callback.
		public function afgc_agf_register_post_type_and_taxonomies() {
			$labels = array(
				'name'                => esc_html__( 'Gift Cards Logs', 'addify_gift_cards' ),
				'singular_name'       => esc_html__( 'Gift Cards Log', 'addify_gift_cards' ),
				'add_new'             => esc_html__( 'Add New Gift Card', 'addify_gift_cards' ),
				'add_new_item'        => esc_html__( 'Add New Gift Card', 'addify_gift_cards' ),
				'edit_item'           => esc_html__( 'Edit Gift Card', 'addify_gift_cards' ),
				'new_item'            => esc_html__( 'New Gift Card', 'addify_gift_cards' ),
				'view_item'           => esc_html__( 'View Gift Card', 'addify_gift_cards' ),
				'search_items'        => esc_html__( 'Search Gift Card', 'addify_gift_cards' ),
				'exclude_from_search' => true,
				'not_found'           => esc_html__( 'No Gift Card Found', 'addify_gift_cards' ),
				'not_found_in_trash'  => esc_html__( 'No Gift Card field found in Trash', 'addify_gift_cards' ),
				'parent_item_colon'   => '',
				'all_items'           => esc_html__( 'Gift Cards', 'addify_gift_cards' ),
				'menu_name'           => esc_html__( 'Gift Cards', 'addify_gift_cards' ),
			);

			$args = array(
				'labels'             => $labels,
				'menu_icon'          => plugin_dir_url( __FILE__ ) . 'front/assets/images/small_logo_grey.png',
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_in_menu'       => 'woocommerce',
				'query_var'          => true,
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => 30,
				'rewrite'            => array(
					'slug'       => 'afgc_gift_card_log',
					'with_front' => false,
				),
				'supports'           => array( 'title' ),
			);

			register_post_type( 'afgc_gift_card_log', $args );

			register_taxonomy(
				'afgc_gallery_cat',
				'product',
				array(
					'hierarchical'          => true,
					'update_count_callback' => '_wc_term_recount',
					'label'                 => __( 'Gift Card Image Gallery', 'addify_gift_cards' ),
					'labels'                => array(
						'name'                  => __( 'Gift Card Gallery', 'addify_gift_cards' ),
						'singular_name'         => __( 'Gift Card Gallery', 'addify_gift_cards' ),
						'menu_name'             => _x( 'Gift Card Gallery', 'Admin menu name', 'addify_gift_cards' ),
						'search_items'          => __( 'Search Gift Card Gallery', 'addify_gift_cards' ),
						'all_items'             => __( 'All Gallery', 'addify_gift_cards' ),
						'parent_item'           => __( 'Parent Gallery', 'addify_gift_cards' ),
						'parent_item_colon'     => __( 'Parent Gallery:', 'addify_gift_cards' ),
						'edit_item'             => __( 'Edit Gallery', 'addify_gift_cards' ),
						'update_item'           => __( 'Update Gallery', 'addify_gift_cards' ),
						'add_new_item'          => __( 'Add new Gallery', 'addify_gift_cards' ),
						'new_item_name'         => __( 'New Gallery name', 'addify_gift_cards' ),
						'not_found'             => __( 'No Gallery found', 'addify_gift_cards' ),
						'item_link'             => __( 'Gallery Link', 'addify_gift_cards' ),
						'item_link_description' => __( 'A link to a Gift Card Gallery category.', 'addify_gift_cards' ),
					),
					'show_in_rest'          => true,
					'show_ui'               => true,
					'show_in_menu'          => true,
					'query_var'             => true,
					'capabilities'          => array(
						'manage_terms' => 'manage_product_terms',
						'edit_terms'   => 'edit_product_terms',
						'delete_terms' => 'delete_product_terms',
						'assign_terms' => 'assign_product_terms',
						'create_posts' => 'do_not_allow',
					),
					'rewrite'               => array(
						'slug'         => 'afgc_gallery_cat',
						'with_front'   => true,
						'hierarchical' => true,
					),
				)
			);
		}

		// Showing Gift card detail to user on edit order.
		public function afgc_gift_card_detail_on_edit_cb( $item_id, $item, $order ) {

			$order_id = $order->get_id();

			$order = wc_get_order( $order_id );

			$meta_data = $item->get_meta_data();

			foreach ( $meta_data as $meta ) {

				$data = $meta->get_data();
				if ( is_array( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {

					$array_of_card_info = $data['value'];
					$total_field        = 0;
					if ( ! empty( $array_of_card_info['total_recipient_user_Select'] ) ) {
						$total_field = $array_of_card_info['total_recipient_user_Select'];
					}
					?>

					<ul style="list-style: none; margin:0;">

						<?php if ( ! empty( $array_of_card_info['afgc_sender_name'] ) ) { ?>

							<li><label><?php echo esc_html__( 'Sender Name :', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_sender_name'] ); ?></li>	

						<?php } elseif ( isset($array_of_card_info['afgc_phy_gift_sender_name'])) { ?>
							<li><label><?php echo esc_html__( 'Sender Name :', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_sender_name'] ); ?></li>	
						<?php
						}

						if ( ! empty( $array_of_card_info['afgc_sender_message'] ) ) {
							?>

							<li><label><?php echo esc_html__( 'Sender Message : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_sender_message'] ); ?></li>
							<?php
						} elseif ( isset($array_of_card_info['afgc_phy_gift_sender_message'])) {
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
						} elseif (! empty( $array_of_card_info['afgc_phy_gift_delivery_date'] )) {
							?>
							<li><label><?php echo esc_html__( 'Delivery Date : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $array_of_card_info['afgc_phy_gift_delivery_date'] ); ?></li>
							<?php
						}

						if ( ! empty( $array_of_card_info['afgc_gift_products'] ) ) {
							$product      = wc_get_product($array_of_card_info['afgc_gift_products']);
							$product_name = $product->get_name();
							?>

							<li><label><?php echo esc_html__( 'Product Gift : ', 'addify_gift_cards' ); ?></label><?php echo esc_attr( $product_name ); ?></li>

							<?php
						}
						?>

					</ul>

					<?php

				}
			}
		}

		// Showing Gift card Thumbnail to user on edit order.
		public function afgc_gift_card_thumb_on_edit_cb( $product_image, $item_id, $item ) {

			if ( empty( $item ) ) {
				return $product_image;
			}

			$product_id      = $item->get_product_id();
			$meta_data       = $item->get_meta_data();
			$is_afgc_virtual = get_post_meta( $product_id, 'afgc_virtual', true );

			if ( 'yes' === $is_afgc_virtual && ! empty( $meta_data ) ) {
				foreach ( $meta_data as $meta ) {
					$data = $meta->get_data();

					if ( ! empty( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {
						$card_info = $data['value'];

						if ( ! empty( $card_info['afgc_selected_img'] ) ) {
							$src   = esc_url( $card_info['afgc_selected_img'] );
							$class = 'wc-order-item-thumbnail';

							return sprintf(
								'<img src="%s" class="%s" />',
								$src,
								esc_attr( $class )
							);
						}
					}
				}
			}

			// Always return fallback image
			return $product_image;
		}

		// Add Gift Gallery Thumbnail.
		public function afgc_add_category_gallery_cb( $term ) {
			?>

			<div class="form-field term-thumbnail-wrap">

				<label><?php esc_html_e( 'Images in this category', 'addify_gift_cards' ); ?></label>

				<div name="product_cat_thumbnail" id="product_cat_thumbnail">
					<div class="afgc-gift-card-cat-gallery"><ul></ul></div>
				</div>

				<div>

					<input type="hidden" id="product_cat_thumbnail_id" name="product_cat_thumbnail_id[]" />
					<button type="button" class="upload_image_button button"><?php echo esc_html_e( 'Add images', 'addify_gift_cards' ); ?></button>
				</div>

				<script>

					// Only show the "remove image" button when needed
					if ( ! jQuery( '#product_cat_thumbnail_id' ).val() ) {

						jQuery( '.remove_image_button' ).hide();

					}

					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						if ( file_frame ) {

							console.log(file_frame);

							file_frame.open();

							return;
							
						}

						file_frame = wp.media.frames.downloadable_file = wp.media({

							title: '<?php esc_html_e( 'Choose an image', 'addify_gift_cards' ); ?>',

							button: {

								text: '<?php esc_html_e( 'Use image', 'addify_gift_cards' ); ?>'

							},

							multiple: "toggle"

						});

						file_frame.on( 'select', function() {

							var attachments = file_frame.state().get('selection').map( 

								function( attachment ) {

									attachment.toJSON();

									return attachment;

								});

							var i;

							for ( i = 0; i < attachments.length; ++i ) {

								jQuery( 'div.afgc-gift-card-cat-gallery ul' ).append( '<li class="addli"><span>x</span><input type="hidden" value="' +  attachments[i].attributes.id + '" name="product_cat_thumbnail_id[]" ><img src="' +  attachments[i].attributes.url + '" ></li>' );
							}

						});

						file_frame.open();
					});

				</script>

				<div class="clear"></div>
			</div>
			<?php
		}


		// Edit Gift Gallery Thumbnail.
		public function afgc_edit_category_gallery_cb( $term ) {

			$thumbnail_ids = (array) get_term_meta( $term->term_id, 'product_cat_thumbnail_id', true );

			?>
			
			<div class="form-field term-thumbnail-wrap">
				<label><?php esc_html_e( 'Images in this category', 'addify_gift_cards' ); ?></label>

				<div id="product_cat_thumbnail" name="product_cat_thumbnail">
					
					<div class="afgc-gift-card-cat-gallery">

						<ul>

							<?php
							foreach ( array_filter( $thumbnail_ids ) as $index => $thumbnail_id ) {

								if ( ! empty( wp_get_attachment_thumb_url( $thumbnail_id ) ) ) {
									?>

									<li class="editli">

										<span class="remove_image_button" data-term_id="<?php echo esc_attr( $term->term_id ); ?>" data-thumbnail_id="<?php echo esc_attr( $index ); ?>" ><?php echo esc_html__( 'X', 'addify_gift_cards' ); ?></span>

										<input type="hidden" name="product_cat_thumbnail_id[]" value="<?php echo esc_attr( $thumbnail_id ); ?>">

										<img src="<?php echo esc_url( wp_get_attachment_image_url( $thumbnail_id, 'full' ) ); ?>">

									</li>

									<?php

								}
							}
							?>

						</ul>

					</div>

				</div>

				<button type="button" class="upload_image_button button"><?php echo esc_html__( 'Add image', 'addify_gift_cards' ); ?></button>

				<script>
					
					// Upload Images In Gallery.
					var file_frame;

					jQuery( document ).on( 'click', '.upload_image_button', function( event ) {

						event.preventDefault();

						if ( file_frame ) {

							file_frame.open();

							return;
						}

						file_frame = wp.media.frames.downloadable_file = wp.media({

							title: '<?php esc_html_e( 'Choose an image', 'addify_gift_cards' ); ?>',

							button: {

								text: '<?php esc_html_e( 'Use image', 'addify_gift_cards' ); ?>'

							},
							multiple: "toggle"
						});

						file_frame.on( 'select', function() {

							var attachments = file_frame.state().get('selection').map( 

								function( attachment ) {

									attachment.toJSON();

									return attachment;

								});

							var i;

							for (i = 0; i < attachments.length; ++i) {

								jQuery( 'div.afgc-gift-card-cat-gallery ul' ).append( '<li class="upali"><span class="remove_image_button">x</span><input type="hidden" value="' +  attachments[i].attributes.id + '" name="product_cat_thumbnail_id[]" ><img src="' +  attachments[i].attributes.url + '" ></li>' );

							}

						});

						file_frame.open();
					});
					
				</script>
				<div class="clear"></div>
			</div>
			<?php
		}


		// Delete Gift Gallery Thumbnail.
		public function afgc_delete_update_gift_gallery_images() {

			$nonce = isset( $_POST['afgc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afgc_nonce'] ) ) : 0;

			if ( ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {
				wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
			}

			if ( isset( $_POST['term_id'] ) && isset( $_POST['thumbnail_id'] ) ) {

				$term_id = sanitize_text_field( wp_unslash( $_POST['term_id'] ) );

				$thumbnail_id = sanitize_meta( '', wp_unslash( $_POST['thumbnail_id'] ), '' );

				$thumbnail_ids = (array) get_term_meta( $term_id, 'product_cat_thumbnail_id', true );

				unset( $thumbnail_ids[ $thumbnail_id ] );

				update_term_meta( $term_id, 'product_cat_thumbnail_id', $thumbnail_ids );

			}
		}


		// Save Gift Gallery Term.
		public function afgc_save_category_fields_callback( $term_id, $tt_id = '', $taxonomy = '' ) {

			$nonce = isset( $_POST['_wpnonce_add-tag'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce_add-tag'] ) ) : 0;
			$term  = get_term( $term_id );

			if ( ! is_wp_error( $term ) && 'afgc_gallery_cat' == $term->taxonomy ) {

				if ( ! wp_verify_nonce( $nonce, 'add-tag' ) ) {
					wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
				}

				if ( isset( $_POST['product_cat_thumbnail_id'] ) ) {

					update_term_meta( $term_id, 'product_cat_thumbnail_id', sanitize_meta( '', wp_unslash( $_POST['product_cat_thumbnail_id'] ), '' ) );

				}
			}
		}


		// Edit Gift Gallery Term.
		public function afgc_edit_category_fields_callback( $term_id, $tt_id = '', $taxonomy = '' ) {

			$term = get_term( $term_id );

			if ( ! is_wp_error( $term ) && 'afgc_gallery_cat' == $term->taxonomy && ! is_ajax() ) {

				$nonce = isset( $_POST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ) : 0;

				if ( ! wp_verify_nonce( $nonce, 'update-tag_' . $term_id ) ) {
					wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
				}

				if ( isset( $_POST['product_cat_thumbnail_id'] ) ) {

					update_term_meta( $term_id, 'product_cat_thumbnail_id', sanitize_meta( '', wp_unslash( $_POST['product_cat_thumbnail_id'] ), '' ) );

				}
			}
		}

		public function afgc_gift_card_logs_columns_cb( $columns ) {

			global $post_id;

			$afgc_coupon_code = get_post_meta( $post_id, '_coupon_code', true );

			unset(
				$columns['wpseo-score'],
				$columns['wpseo-title'],
				$columns['wpseo-metadesc'],
				$columns['wpseo-focuskw']
			);
			return array(
				'cb'                    => '<input type="checkbox" />',
				'coupon_code'           => __( 'Gift Card Code', 'addify_gift_cards' ),
				'afgc_used_amount'      => __( 'Used Amount', 'addify_gift_cards' ),
				'afgc_remaining_amount' => __( 'Remaining Amount', 'addify_gift_cards' ),
				'afgc_total_amount'     => __( 'Total Amount', 'addify_gift_cards' ),
				'afgc_download_card'    => __( 'Download', 'addify_gift_cards' ),
			);
		}

		public function afgc_fill_gift_card_logs_columns_cb( $column, $post_id ) {

			$afgc_coupon_code = get_post_meta( $post_id, '_coupon_code', true );

			$afgc_coupon_rem_amount = get_post_meta( $post_id, '_afgc_coupon_rem_amount', true );

			$afgc_gift_card_status = get_post_meta( $post_id, '_gift_card_status', true );

			$coupon = new WC_Coupon( $afgc_coupon_code );

			$coupon_id = $coupon->get_id();

			$coupon_amount = $coupon->get_amount();

			$coupon_total_amount = floatval( get_post_meta( $coupon->get_id(), 'coupon_total_amount', true ) );

			switch ( $column ) {

				case 'coupon_code':
					?>

				<strong><a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>"><?php echo esc_attr( $afgc_coupon_code ); ?></a></strong>
				
				<?php
					break;

				case 'afgc_used_amount':
					if ( 'purchased' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( '0' ) );

					} elseif ( 'pending' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( '0' ) );

					} elseif ( 'partial delivered' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( (int) $coupon_total_amount - (int) $afgc_coupon_rem_amount ) );

					} elseif ( 'accomplish' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( $coupon_total_amount ) );

					}

					break;

				case 'afgc_remaining_amount':
					if ( 'purchased' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( $coupon_total_amount ) );

					} elseif ( 'pending' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( $coupon_amount ) );

					} elseif ( 'partial delivered' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( $afgc_coupon_rem_amount ) );

					} elseif ( 'accomplish' == $afgc_gift_card_status ) {

						echo wp_kses_post( wc_price( '0' ) );

					}

					break;

				case 'afgc_total_amount':
				echo wp_kses_post( wc_price( $coupon_total_amount ) );

					break;

				case 'afgc_download_card':
					?>

				<div class="afgc-download-col">

					<a href="?download_gf_id=<?php echo intval( $post_id ); ?>"><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/download-icon.png' ) ); ?>"></a>

				</div>

				<?php

					break;

			}
		}

		// Include Gift Card Custom Product Type Class.
		public function afgc_register_gift_card_type() {

			include_once AFGC_PLUGIN_DIR . '/admin/includes/meta-boxes/afgc-product-gift-card.php';
		}

		// Add Gift Card Custom Product Type.
		public function afgc_add_gift_card_type_cb( $type ) {

			$type['gift_card'] = __( 'Gift Card', 'addify_gift_cards' );

			return $type;
		}

		// Add Gift Card Tab for Custom Product Type.
		public function afgc_add_gift_card_tab_cb( $tabs ) {
			$new_tab['gift_card'] = array(
				'label'  => __( 'Gift Card', 'addify_gift_cards' ),
				'target' => 'gift_card_options',
				'class'  => ( 'show_if_gift_card' ),
			);

			return array_merge( $new_tab, $tabs );
		}

		// Add Product Type for Gift Card Custom Product Type Virtual / Physical.
		public function afgc_gift_card_type_opt_cb( $afgc_types ) {
			global $post;
			$afgc_types['afgc_virtual'] = array(
				'id'            => 'afgc_virtual',
				'wrapper_class' => 'show_if_gift_card',
				'label'         => __( 'Virtual', 'addify_gift_cards' ),
				'description'   => __( 'Virtual products are intangible and are not shipped.', 'addify_gift_cards' ),
				'default'       => get_post_meta( $post->ID, 'afgc_virtual', true ),
			);

			return $afgc_types;
		}


		public function afgc_save_gift_card_type_opt_cb( $post_id, $product, $update ) {
			if (isset( $_POST['afgc_nonce'] )) {
				$nonce = isset( $_POST['afgc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afgc_nonce'] ) ) : 0;

				if ( ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {
					wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
				}
			}

			$is_afgc_virtual = isset( $_POST['afgc_virtual'] ) ? 'yes' : 'no';

			update_post_meta( $post_id, 'afgc_virtual', $is_afgc_virtual );
		}

		// Add Fields in Gift Card Tab Custom Product Type.
		public function afgc_gift_card_options_product_tab_content() {

			global $post, $product, $afgc_purchace_amount, $wp_roles;

			if ( isset( $_GET['post'] ) && ! empty( $_GET['post'] ) ) {
				$product_id = absint( $_GET['post'] );
				$product    = wc_get_product( $product_id );
			} else {
				$product_id = '';
			}

			$product = wc_get_product( $product_id );

			$afgc_selected_products = (array) get_post_meta( $product_id, 'afgc_select_product', true );

			$afgc_selected_cat = (array) get_post_meta( $product_id, 'afgc_select_categories', true );

			$afgc_user_roles = (array) get_post_meta( $product_id, 'afgc_user_roles', true );

			$afgc_special_discount_amount = get_post_meta( $product_id, 'afgc_special_discount_amount', true );

			$afgc_allow_overide_custom_amount = get_post_meta( $product_id, 'afgc_allow_overide_custom_amount', true );

			$afgc_allow_custom_img = get_post_meta( $product_id, 'afgc_allow_custom_img', true );
			$afgc_product_as_gift  = get_post_meta( $product_id, 'afgc_product_as_gift', true );

			$afgc_enable_custom_image_btn = get_post_meta( $product_id, 'afgc_enable_custom_image_btn', true );

			$afgc_special_discount_type = get_post_meta( $product_id, 'afgc_special_discount_type', true );

			$afgc_expiration_date = get_post_meta( $product_id, 'afgc_expiration_date', true );
			if ( empty( $afgc_update_price_from_pro_level ) ) {

				$afgc_update_price_from_pro_level = get_post_meta( $product_id, 'afgc_gift_card_amnt', true );

			} else {

				$afgc_purchace_amount = $product->get_price( 'edit' );

			}
			?>

			<div id='gift_card_options' class='panel woocommerce_options_panel'>

				<div class='options_group'>

					<?php wp_nonce_field( 'addify_agf_nonce', 'afgc_nonce' ); ?>

					<h3><?php echo esc_html__( 'Gift Card Options', 'addify_gift_cards' ); ?></h3>

					<?php
					woocommerce_wp_text_input(
						array(
							'id'          => 'afgc_gift_card_amnt',
							'type'        => 'text',
							'value'       => $afgc_purchace_amount ? esc_attr( $afgc_purchace_amount ) : $afgc_update_price_from_pro_level,
							'label'       => __( 'Gift card value', 'addify_gift_cards' ),
							'desc_tip'    => true,
							'description' => __( 'Please enter the gift card amount. To add multiple amounts options, separate each value with a comma.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_checkbox(
						array(
							'id'            => 'afgc_allow_custom_img',
							'wrapper_class' => 'afgc-allow-custom-img',
							'label'         => __( 'Allow custom Image', 'addify_gift_cards' ),
							'description'   => __( 'Enable this checkbox to let customers upload custom image for gift card.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => 'afgc_enable_custom_image_btn',
							'type'        => 'text',
							'value'       => $afgc_enable_custom_image_btn ? esc_attr( $afgc_enable_custom_image_btn ) : '',
							'label'       => __( 'Enter a button label', 'addify_gift_cards' ),
							'desc_tip'    => true,
							'description' => __( 'Enter a label for custom upload image button.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_checkbox(
						array(
							'id'            => 'afgc_product_as_gift',
							'wrapper_class' => 'afgc-product-as-gift-card',
							'label'         => __( 'Product as Gift', 'addify_gift_cards' ),
							'description'   => __( 'Enable this checkbox to allow send product as gift.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_checkbox(
						array(
							'id'            => 'afgc_allow_overide_custom_amount',
							'wrapper_class' => 'show_if_gift_card',
							'value'         => $afgc_enable_custom_image_btn ? esc_attr( $afgc_enable_custom_image_btn ) : 'no',
							'label'         => __( 'Allow custom amount', 'addify_gift_cards' ),
							'description'   => __( 'Enable this checkbox to let customers enter custom gift card amount.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'            => 'afgc_allow_overide_min_custom_amount',
							'wrapper_class' => '',
							'label'         => __( 'Minimum custom amount', 'addify_gift_cards' ),
							'desc_tip'      => true,
							'description'   => __( 'Set an optional minimum custom amount for this gift card.', 'addify_gift_cards' ),
							'type'          => 'number',
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'            => 'afgc_allow_overide_max_custom_amount',
							'wrapper_class' => '',
							'label'         => __( 'Maximum custom amount', 'addify_gift_cards' ),
							'desc_tip'      => true,
							'description'   => __( 'Set an optional maximum custom amount for this gift card.', 'addify_gift_cards' ),
							'type'          => 'number',
						)
					);

					woocommerce_wp_select(
						array(
							'id'            => 'afgc_special_discount_type',
							'value'         => $afgc_special_discount_type ? esc_attr( $afgc_special_discount_type ) : '',
							'wrapper_class' => 'stock_status_field hide_if_variable hide_if_external hide_if_grouped',
							'label'         => __( 'Discount type', 'addify_gift_cards' ),
							'options'       => array(
								'none'       => __( 'None', 'addify_gift_cards' ),
								'fixed'      => __( 'Fixed', 'addify_gift_cards' ),
								'percentage' => __( 'Percentage', 'addify_gift_cards' ),
							),
							'desc_tip'      => true,
							'description'   => __( 'Discount type', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => 'afgc_special_discount_amount',
							'type'        => 'text',
							'value'       => $afgc_special_discount_amount ? esc_attr( $afgc_special_discount_amount ) : '',
							'label'       => __( 'Discount Amount', 'addify_gift_cards' ),
							'desc_tip'    => true,
							'description' => __( 'Please enter a value with one monetary decimal point without thousand separators and currency symbols.', 'addify_gift_cards' ),
						)
					);

					woocommerce_wp_text_input(
						array(
							'id'          => 'afgc_expiration_date',
							'type'        => 'text',
							'value'       => $afgc_expiration_date,
							'label'       => __( 'Expiration days', 'addify_gift_cards' ),
							'desc_tip'    => true,
							'description' => __( 'Days when this gift card will expire.', 'addify_gift_cards' ),
						)
					);

					?>
					<p class="form-field">

						<label><?php echo esc_html__( 'User Role Restriction', 'addify_gift_cards' ); ?></label>

						<select class="afgc-user-role-search" multiple="multiple" style="width: 80%;" name="afgc_user_roles[]" id="afgc_user_roles" data-placeholder="<?php esc_attr_e( 'Select Roles', 'addify_gift_cards' ); ?>">

							<?php

							$afgc_get_user_roles = $wp_roles->get_names();

							$afgc_get_user_roles['guest'] = 'Guest';

							foreach ( $afgc_get_user_roles as $key => $afgc_get_user_role ) {
								?>

								<option value="<?php echo esc_attr( $key ); ?>"

									<?php echo in_array( (string) $key, (array) $afgc_user_roles, true ) ? esc_attr( 'selected' ) : ''; ?> />
									
									<?php echo esc_attr( $afgc_get_user_role ); ?>

								</option>

							<?php } ?>

						</select>

						<?php echo wp_kses_post( wc_help_tip( __( 'Leave empty for all.', 'addify_gift_cards' ) ) ); ?>

					</p>

					<h3><?php echo esc_html__( 'Gift Card Discount Applicable To:', 'addify_gift_cards' ); ?></h3>

					<p class="form-field">

						<label><?php echo esc_html__( 'Products', 'addify_gift_cards' ); ?></label>

						<select class="afgc_select_product" multiple="multiple" style="width: 80%;" name="afgc_select_product[]" id="afgc_select_product" data-placeholder="<?php esc_attr_e( 'Select product', 'addify_gift_cards' ); ?>" data-action="woocommerce_json_search_products_and_variations">
							<?php

							foreach ( $afgc_selected_products as $afgc_selected_product ) {

								$product = wc_get_product( $afgc_selected_product );

								if ( is_object( $product ) ) {

									echo '<option selected value="' . esc_attr( $product->get_id() ) . '">' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';

								}
							}

							?>
						</select>
						<?php echo wp_kses_post( wc_help_tip( __( 'Select product for this gift card.', 'addify_gift_cards' ) ) ); ?>
					</p>


					<p class="form-field">

						<label><?php echo esc_html__( 'Categories', 'addify_gift_cards' ); ?></label>

						<select class="afgc-category-search" multiple="multiple" style="width: 80%;" name="afgc_select_categories[]" id="afgc_select_categories" data-placeholder="<?php esc_attr_e( 'Select Categories', 'addify_gift_cards' ); ?>">

							<?php

							foreach ( (array) $afgc_selected_cat as $afgc_cat_id ) {

								if ( $afgc_cat_id ) {

									$term_name = get_term( $afgc_cat_id )->name;

									?>

									<option value="<?php echo esc_attr( $afgc_cat_id ); ?>" selected><?php echo esc_attr( $term_name ); ?></option>
									
									<?php

								}
							}

							?>
						</select>
						<?php echo wp_kses_post( wc_help_tip( __( 'Select categories for this gift card.', 'addify_gift_cards' ) ) ); ?>
					</p>

					<p class="form-field">
						<label></label>
						<span><?php echo esc_html__( 'Leave empty to let customers use gift card discount on any product(s) from entire catalog.', 'addify_gift_cards' ); ?></span>
					</p>

				</div>

			</div>

			<?php
		}


		// Save Gift Card Product Type Metabox.
		public function afgc_save_gift_card_options_field( $post_id ) {

			global $post;

			// For custom post type:
			$exclude_statuses = array(
				'auto-draft',
				'trash',
			);

			$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';
			$nonce  = isset( $_POST['afgc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['afgc_nonce'] ) ) : 0;

			if ( ! in_array( get_post_status( $post_id ), $exclude_statuses ) && ! is_ajax() && 'untrash' != $action ) {

				if ( ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {
					wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
				}
			}

			// Gift Card Amount.
			if ( isset( $_POST['afgc_gift_card_amnt'] ) ) {
				$gift_card_amnt = sanitize_text_field( wp_unslash( $_POST['afgc_gift_card_amnt'] ) );
				if ( update_post_meta( $post_id, 'afgc_gift_card_amnt', $gift_card_amnt ) && ! empty( $gift_card_amnt ) ) {
					$gift_card_amnt      = explode(',', $gift_card_amnt);
					$gift_card_amnt_main = $gift_card_amnt[0];
					// Success
					update_post_meta( $post_id, '_regular_price', $gift_card_amnt_main );
				}
			}

			// Gift Card Special Discount Type.
			if ( isset( $_POST['afgc_special_discount_type'] ) ) {

				update_post_meta( $post_id, 'afgc_special_discount_type', sanitize_meta( '', wp_unslash( $_POST['afgc_special_discount_type'] ), '' ) );

			}

			// Gift Card Special Discount Amount.
			if ( isset( $_POST['afgc_special_discount_amount'] ) ) {

				update_post_meta( $post_id, 'afgc_special_discount_amount', sanitize_meta( '', wp_unslash( $_POST['afgc_special_discount_amount'] ), '' ) );

			}

			// Allow custom amount.
			if ( isset( $_POST['afgc_allow_overide_custom_amount'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_allow_overide_custom_amount', sanitize_text_field( wp_unslash( $_POST['afgc_allow_overide_custom_amount'] ) ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_allow_overide_custom_amount', '' );

			}

			// Allow gift card custom image.
			if ( isset( $_POST['afgc_allow_custom_img'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_allow_custom_img', sanitize_text_field( wp_unslash( $_POST['afgc_allow_custom_img'] ) ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_allow_custom_img', '' );

			}
			if ( isset( $_POST['afgc_product_as_gift'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_product_as_gift', sanitize_text_field( wp_unslash( $_POST['afgc_product_as_gift'] ) ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_product_as_gift', '' );

			}
			
			// Custom Button Label.
			if ( isset( $_POST['afgc_enable_custom_image_btn'] ) ) {

				update_post_meta( $post_id, 'afgc_enable_custom_image_btn', sanitize_meta( '', wp_unslash( $_POST['afgc_enable_custom_image_btn'] ), '' ) );

			}

			// Minimum custom amount.
			if ( isset( $_POST['afgc_allow_overide_min_custom_amount'] ) ) {

				update_post_meta( $post_id, 'afgc_allow_overide_min_custom_amount', sanitize_meta( '', wp_unslash( $_POST['afgc_allow_overide_min_custom_amount'] ), '' ) );

			}

			// Maximum custom amount.
			if ( isset( $_POST['afgc_allow_overide_max_custom_amount'] ) ) {

				update_post_meta( $post_id, 'afgc_allow_overide_max_custom_amount', sanitize_meta( '', wp_unslash( $_POST['afgc_allow_overide_max_custom_amount'] ), '' ) );

			}

			if ( isset( $_POST['afgc_override_expiration_settings'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_override_expiration_settings', sanitize_text_field( wp_unslash( $_POST['afgc_override_expiration_settings'] ) ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_override_expiration_settings', '' );

			}

			// Gift Card Product Select 2.
			if ( isset( $_POST['afgc_select_product'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_select_product', sanitize_meta( '', wp_unslash( $_POST['afgc_select_product'] ), '' ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_select_product', '' );

			}

			// Gift Card Product categories Select 2.
			if ( isset( $_POST['afgc_select_categories'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_select_categories', sanitize_meta( '', wp_unslash( $_POST['afgc_select_categories'] ), '' ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_select_categories', '' );

			}

			// Gift Card User Roles Select 2.
			if ( isset( $_POST['afgc_user_roles'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_user_roles', sanitize_meta( '', wp_unslash( $_POST['afgc_user_roles'] ), '' ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_user_roles', '' );

			}

			// Gift Card Image Gallery.
			if ( isset( $_POST['afgc_img_gallery_categories'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_img_gallery_categories', sanitize_meta( '', wp_unslash( $_POST['afgc_img_gallery_categories'] ), '' ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_img_gallery_categories', '' );

			}

			// Expiration Date.
			if ( isset( $_POST['afgc_expiration_date'] ) ) {

				update_post_meta( intval( $post_id ), 'afgc_expiration_date', sanitize_meta( '', wp_unslash( $_POST['afgc_expiration_date'] ), '' ) );

			} else {

				update_post_meta( intval( $post_id ), 'afgc_expiration_date', '' );

			}
		}

		// Include Modal & Gift Card Product Type Templates.
		public function afgc_gift_card_product_temp_cb() {

			global $product;

			if ( $product && 'gift_card' == $product->get_type() ) {
				$current_theme = wp_get_theme();
				?>

				<style>
					.flex-control-thumbs,
					.product_meta,
					.woocommerce-product-details__short-description{
						display: none;
					}
					<?php if ( 'Woodmart' == $current_theme->Name) { ?>
						.product-images-inner .afgc-main-form-preview-container{
							width: calc(100% - 30px);
							margin-left: 15px;
							bottom: -209px;
						}
					<?php
					}
					if ( 'Divi' == $current_theme->Name) {
						?>
						.select2-container.select2-container--default.select2-container--open{
							max-width: 412px;
						}
						.afgc_preview_popup{
								z-index: 999999!important;
						}
					<?php
					} 
					if ( 'Avada' == $current_theme->Name) {
						?>
						.select2-container--default .select2-selection--single{
							border-radius: 2px;
						}
						.afgc_preview_btn{
							padding: 9px 20px;
							line-height: 14px;
							font-size: 12px;
						}
					<?php } ?>
				</style>

				<?php

				include AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php';

				$template_path = plugin_dir_path( __FILE__ ) . 'admin/includes/templates/';

				wc_get_template(
					'afgc-gift-card-product-temp.php',
					'',
					'',
					trailingslashit( $template_path )
				);
			}
		}

		public function afgc_coupon_code_file_cb( $emails ) {

			if ( ! class_exists( 'WC_Email', false ) ) {

				$emails['af_gift_card_mail'] = new Af_Gift_Card_Mail();

			}

			return $emails;
		}

		// Create Gift Card Log and check gift card in order.
		public function afgc_check_gift_card_in_order_cb( $order_id ) {

			$order                    = wc_get_order( $order_id );
			$afgc_gift_card_log_title = '';

			foreach ( $order->get_items() as $item_id => $item ) {

				$orderdata = $item->get_data();

				$product_id = $orderdata['product_id'];
				$product    = wc_get_product( $product_id );

				if ( empty($product) ) {
					return;
				}
				
				if (!empty($product)) {

					$afgc_gift_card_log_title = $product->get_title();
				}


				if ( 'gift_card' != $product->get_type() ) {
					continue;
				}

				$afgc_log_post = array(

					'post_type'   => 'afgc_gift_card_log',

					'post_status' => 'publish',

					'post_title'  => $afgc_gift_card_log_title,

				);

				$post_id = wp_insert_post( $afgc_log_post, true );

				update_post_meta( $post_id, '_item_id', $item->get_id() );

				update_post_meta( $post_id, '_order_id', $order_id );

				update_post_meta( $post_id, '_gift_card_price', $item->get_subtotal() + $item->get_subtotal_tax() );

				update_post_meta( $post_id, '_gift_card_status', 'purchased' );

				if ( $order->get_user_id() ) {

					update_post_meta( $post_id, '_purchase_by', $order->get_user_id() );

				} else {

					$customer_email = $order->get_billing_email();

					update_post_meta( $post_id, '_purchase_by', $customer_email );

				}

				$user_data_with_prd = $orderdata['meta_data'];

				foreach ( $user_data_with_prd as $prd_value ) {

					$get_data_with_prd = $prd_value->get_data();

					if ( 'afgc_gift_card_infos' == $get_data_with_prd['key'] ) {

						$get_prd_whole_data = $get_data_with_prd['value'];

						update_post_meta( $post_id, '_purchase_for', $get_prd_whole_data );

					}
				}

				wc_update_order_item_meta( $item->get_id(), 'afgc_log_id', $post_id );

			}
		}

		// Send Coupon Code In Email.
		// Advanced Gift Card: generated a coupon with the VAT included, as opposed to excluded the VAT.

		// Send Coupon Code In Email.
		public function afgc_coupon_code_email_cb( $order_id ) {

			$referred_coupon = new Af_Gc_Coupon_Code();

			$order = wc_get_order( $order_id );

			foreach ( $order->get_items() as $item_id => $item ) {

				$orderdata = $item->get_data();

				$product_id = $orderdata['product_id'];

				$product = wc_get_product( $product_id );


				if ( empty($product) ) {
					return;
				}

				if ('gift_card' != $product->get_type() ) {
					continue;
				}

				$existing_coupon_code = wc_get_order_item_meta( $item_id, 'afgc_cp', true );

				if ( $existing_coupon_code ) {
					$coupon_info     = new WC_Coupon( $existing_coupon_code );
					$referred_code   = $coupon_info->get_code();
					$referred_amount = $coupon_info->get_amount();
					
				} else {

					$discount_price = (float) get_post_meta( $product->get_id(), 'afgc_special_discount_amount', true );

					$afgc_special_discount_type = get_post_meta( $product->get_id(), 'afgc_special_discount_type', true );

					$adf_item_total = $item->get_total();

					$card_price = $adf_item_total + $item->get_subtotal_tax();

					$afgc_coupoun_amount = $card_price;

					$meta_data = $item->get_meta_data();

					$is_afgc_virtual = get_post_meta( $product_id, 'afgc_virtual', true );

					$user_email = '';

					if ( 'yes' == $is_afgc_virtual ) {

						$get_user_email_arr = array();

						foreach ( $meta_data as $meta ) {

							$data = $meta->get_data();

							if ( ! empty( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {

								$array_of_card_info = $data['value'];

								$total_field = $array_of_card_info['total_recipient_user_Select'];

								$whole_order_data = $data['value'];

								for ( $i = 1; $i <= $total_field; $i++ ) {

									if ( isset( $array_of_card_info[ 'afgc_recipient_email' . $i ] ) ) {

										$get_user_email_arr[] = $array_of_card_info[ 'afgc_recipient_email' . $i ];

									}
								}
							}
						}

						$get_user_email_arr = array_filter( $get_user_email_arr );

						$user_email = count( $get_user_email_arr ) >= 1 ? $get_user_email_arr : array();

						$user_email = implode( ',', $user_email );

					} else {

						foreach ( $meta_data as $meta ) {

							$data = $meta->get_data();

							if ( ! empty( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {

								$array_of_card_info = $data['value'];

								if ( isset( $array_of_card_info['afgc_phy_gift_recipient_email'] ) ) {

									$user_email = $array_of_card_info['afgc_phy_gift_recipient_email'];

								}
							}
						}
					}
					$afgc_gift_products = '';
					$afgc_gift_type     = '';
					foreach ( $meta_data as $meta ) {
						$data = $meta->get_data();
						if (is_array( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {
							$afgc_gift_card_infos_value = $data['value'];
							$afgc_gift_type             = $afgc_gift_card_infos_value['afgc_gift_type'];
							if ('product-gift'==$afgc_gift_type) {
								$afgc_gift_products = $afgc_gift_card_infos_value['afgc_gift_products'];
							}
						}
					}
					
					$coupon_referred_code = $referred_coupon->afgc_referred_coupon_generate( $product_id, $afgc_coupoun_amount, $user_email, $afgc_gift_type, $afgc_gift_products );

					wc_update_order_item_meta( $item->get_id(), 'afgc_cp', $coupon_referred_code );

					$afgc_log_id = wc_get_order_item_meta( $item->get_id(), 'afgc_log_id', true );

					update_post_meta( $afgc_log_id, '_coupon_code', $coupon_referred_code );

					update_post_meta( $afgc_log_id, '_gift_card_status', 'pending' );

					$coupon_info = new WC_Coupon( $coupon_referred_code );

					$referred_code = $coupon_info->get_code();

					$referred_amount = $coupon_info->get_amount();

					$order = new WC_Order( $order_id );
				}
				$meta_data     = $item->get_meta_data();
				$delivery_date = '';
				foreach ( $meta_data as $meta ) {
					$data = $meta->get_data();
					if ( is_array( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {
						$agf_data_value = $data['value'];
						$delivery_date  = isset($agf_data_value['afgc_delivery_date']) ? $agf_data_value['afgc_delivery_date'] : '';
						break;
					}
				}
				$current_date = gmdate( 'Y-m-d' );
				
				if ( empty($delivery_date) || $current_date == $delivery_date ) {

					$emails['af_gc_mail'] = new Af_Gift_Card_Mail();
					
					WC()->mailer()->emails['af_gc_mail']->trigger( $order_id, $item_id, $referred_code );

				}

				$order = wc_get_order( $order_id );

				$afgc_recip_email = array();

				$whole_order_data = array();

				$meta_data = $item->get_meta_data();

				foreach ( $meta_data as $meta ) {

					$data = $meta->get_data();

					if ( ! empty( $data ) && 'afgc_gift_card_infos' === $data['key'] ) {

						$array_of_card_info = $data['value'];

						$total_field = isset($array_of_card_info['total_recipient_user_Select']) ? $array_of_card_info['total_recipient_user_Select'] : '';

						$whole_order_data = $data['value'];

						for ( $i = 1; $i <= $total_field; $i++ ) {

							if ( isset( $array_of_card_info[ 'afgc_recipient_email' . $i ] ) ) {

								$afgc_recip_email[] = $array_of_card_info[ 'afgc_recipient_email' . $i ];
							}
						}

						foreach ( $afgc_recip_email as $afgc_recip_eml ) {

							$my_user = get_user_by( 'email', $afgc_recip_eml );

							if ( $my_user ) {

								$data_obj = $my_user->data;

								$user_id = $my_user->ID;

								$new_data = array(
									'item_id'  => $item_id,
									'order_id' => $order_id,
								);

								$old_data_of_user = (array) get_user_meta( $user_id, 'gift_card_send_to_user', true );

								foreach ( $old_data_of_user as $value ) {

									if ( ! $value ) {

										continue;
									}

									if ( (int) $value['item_id'] == (int) $item_id && (int) $value['order_id'] == (int) $order_id ) {

										continue 2;

									}
								}

								$old_data_of_user[] = $new_data;

								update_user_meta( $user_id, 'gift_card_send_to_user', $old_data_of_user );

							}
						}
					}
				}
			
			$afgc_prefix_name =!empty(get_option('afgc_pdf_email_prefix_name')) ? get_option('afgc_pdf_email_prefix_name') : 'afgc';

				$gift_card_id = wc_get_order_item_meta( $item->get_id(), 'afgc_log_id', true );
				$path         = AFGC_PLUGIN_DIR . 'front/assets/pdf/' . $afgc_prefix_name . '-' . $gift_card_id . '.pdf';

				if ( file_exists($path) ) {
					wp_delete_file($path);
				}

			}
		}

		// Match Gift Card in order.
		public function afgc_match_gift_card_in_order_cb( $order_id ) {

			$order = wc_get_order( $order_id );

			foreach ( $order->get_coupons() as $afgc_coupons ) {

				$afgc_coupon_code = $afgc_coupons->get_code();

				$afgc_coupons_discount = $afgc_coupons->get_discount();

				$coupon = new WC_Coupon( $afgc_coupon_code );

				$coupon_id = $coupon->get_id();

				$coupon_type = get_post_meta( $coupon_id, 'coupon_type', true );

				$coupon_total_amount = get_post_meta( $coupon_id, 'coupon_total_amount', true );

				if ( 'afgc_coupon' == $coupon_type ) {

					$args = array(
						'post_type'   => 'afgc_gift_card_log',
						'post_status' => 'publish',
						'meta_query'  => array(
							array(
								'key'   => '_coupon_code',
								'value' => $afgc_coupon_code,
							),
						),

					);
					$afgc_query = new WP_Query( $args );

					$coupon_total_amount = get_post_meta( $coupon_id, 'coupon_total_amount', true );

					if ( $afgc_query->have_posts() ) {

						while ( $afgc_query->have_posts() ) {

							$afgc_query->the_post();

							$afgc_item_id = get_post_meta( get_the_ID(), '_item_id', true );

							$item = new WC_Order_Item_Product( $afgc_item_id );

							if ( $afgc_coupons_discount == $coupon->get_amount() ) {

								update_post_meta( get_the_ID(), '_gift_card_status', 'accomplish' );

								update_post_meta( get_the_ID(), '_afgc_coupon_rem_amount', 0 );

								$coupon->set_amount( 0 );

								$afgc_expire_coupon_date = gmdate( 'Y-m-d' );

								$afgc_cpn_exp_dt = $coupon->set_date_expires( $afgc_expire_coupon_date );

								$coupon->save();

							} else {

								$existing_remaining_amount = get_post_meta( get_the_ID(), '_afgc_coupon_rem_amount', true );

								if ( empty( $existing_remaining_amount ) ) {

									$existing_remaining_amount = get_post_meta( get_the_ID(), '_gift_card_price', true );
								}

								update_post_meta( get_the_ID(), '_gift_card_status', 'partial delivered' );

								$afgc_coupon_remaining_amount = $existing_remaining_amount - $afgc_coupons_discount;

								update_post_meta( get_the_ID(), '_afgc_coupon_rem_amount', $afgc_coupon_remaining_amount );

								$coupon->set_amount( $afgc_coupon_remaining_amount );

								$coupon->save();

							}
						}
					}

					wp_reset_postdata();

				}
			}
		}

		public static function afgc_is_date_between( $starts ) {

			if ( '' == $starts ) {

				return 1;

			}

			$today_date = gmdate( 'Y-m-d' );

			$today_date = gmdate( 'Y-m-d', strtotime( $today_date ) );

			$start_date = gmdate( 'Y-m-d', strtotime( $starts ) );

			if ( $start_date == $today_date ) {

				return 1;

			}

			if ( ( $today_date >= $start_date ) ) {

				return 1;

			} else {

				return 0;

			}
		}


		public function afgc_recipent_email_body_cb( $emails ) {

			include_once AFGC_PLUGIN_DIR . '/front/includes/emails/class-af-gift-card-mail.php';

			$emails['af_gc_mail'] = new Af_Gift_Card_Mail();

			return $emails;
		}

		public function afgc_cuctom_card_image_cb() {

			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : 0;
			if ( ! wp_verify_nonce( $nonce, 'addify_agf_nonce' ) ) {
				wp_die( esc_html__( 'Failed Ajax security check!', 'addify_gift_cards' ) );
			}

			if ( isset( $_FILES['file'] ) && ! empty( $_FILES['file']['name'] ) && empty( $_FILES['file']['error'] ) ) {
				
				$file_name = sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) );

				if ( ! empty( $_FILES['file']['type'] ) ) {

					$file_type = sanitize_text_field( wp_unslash( $_FILES['file']['type'] ) );

				}

				include_once ABSPATH . 'wp-admin/includes/image.php';

				include_once ABSPATH . 'wp-admin/includes/file.php';

				include_once ABSPATH . 'wp-admin/includes/media.php';

				$uploaddir = wp_upload_dir();

				$file = sanitize_text_field( wp_unslash( $_FILES['file']['name'] ) );

				$uploadimgfile = $uploaddir['path'] . '/' . basename( $file );

				if ( isset( $_FILES['file']['tmp_name'] ) ) {

					$tempname = sanitize_text_field( $_FILES['file']['tmp_name'] );

				}

				copy( $tempname, $uploadimgfile );

				$filename = basename( $uploadimgfile );

				$wp_filetype = wp_check_filetype( basename( $filename ), null );

				$attachment = array(

					'post_mime_type' => $wp_filetype['type'],

					'post_status'    => 'inherit',

				);

				$attach_id = wp_insert_attachment( $attachment, $uploadimgfile );

				$get_image = wp_get_attachment_url( $attach_id );

				$img_address = $get_image;

				$img_address_old = array(

					'prd_pg_img_links' => $img_address,

					'attach_id'        => $attach_id,

				);

				wp_send_json( $img_address_old );

			}
		}


		// Price Html for Gift Product On Shop Page.
		public function afgc_product_price_shop_page_cb( $price_html, $product ) {
			if ( $product && 'gift_card' == $product->get_type() ) {

				$regular_price = (float) $product->get_regular_price();

				$afgc_gift_card_amnt              = get_post_meta( $product->get_id(), 'afgc_gift_card_amnt', true );
				$afgc_allow_overide_custom_amount = get_post_meta( $product->get_id(), 'afgc_allow_overide_custom_amount', true );
				if (!empty($afgc_gift_card_amnt) ) {
					$afgc_get_cust_amnts = explode( ',', $afgc_gift_card_amnt );
					$min_price           = min( $afgc_get_cust_amnts );
					$max_price           = max( $afgc_get_cust_amnts );
					if ( count($afgc_get_cust_amnts) > 1 ) {
						$price_range = wc_price($min_price) . ' - ' . wc_price($max_price);
						return $price_range;
					} else {

						$afgc_gift_card_amnt_wc = wc_price($afgc_gift_card_amnt);
						return $afgc_gift_card_amnt_wc;
					}
				} else {
					$afgc_get_cust_amnts = explode( ',', $afgc_gift_card_amnt );
					$afgc_gift_card_amnt = wc_price($afgc_get_cust_amnts[0]);
					return $afgc_gift_card_amnt;
				}
			}
			return $price_html;
		}


		// Display Gift Card Product on Shop page  Depended on user Role.
		public function afgc_gift_card_for_user_roles( $visible, $product_id ) {

			$product = wc_get_product( $product_id );
			if (empty($product)) {
				return;
			}
			if ( 'gift_card' != $product->get_type() ) {

				return $visible;

			}

			$afgc_current_user_roles = get_post_meta( $product_id, 'afgc_user_roles', true );

			$get_current_user = wp_get_current_user();

			if ( is_user_logged_in() ) {

				$user_role = current( $get_current_user->roles );

			} else {

				$user_role = 'guest';

			}

			if ( empty( $afgc_current_user_roles ) ) {

				return $visible;

			}

			if ( ! in_array( $user_role, $afgc_current_user_roles ) ) {

				return false;

			}

			return $visible;
		}
		public function afgc_apply_coupon_from_url() {

			if ( isset( $_GET['coupon_code'] )) {

				$coupon_code = sanitize_text_field( $_GET['coupon_code'] );

				if ( ! WC()->cart->has_discount( $coupon_code ) ) {
					WC()->cart->add_discount( $coupon_code );
					WC()->session->set( 'coupon_applied', true );
					$current_url = remove_query_arg( 'coupon_code' );
					wp_redirect( $current_url );
					exit;
				}
			}
		}

		// Redirect Product to 404 for unselected User Role.
		public function afgc_redirect_404_cb() {

			global $wp_query;

			if ( is_singular( 'product' ) ) {

				$post_object = $wp_query->get_queried_object();

				$product_id = $post_object->ID;

				$afgc_current_user_roles = get_post_meta( $product_id, 'afgc_user_roles', true );

				$get_current_user = wp_get_current_user();
				if ( is_user_logged_in() ) {
					$user_role = current( $get_current_user->roles );
				} else {
					$user_role = 'guest';
				}

				if ( empty( $afgc_current_user_roles ) ) {
					return;
				}

				if ( ! in_array( $user_role, (array) $afgc_current_user_roles ) ) {
					$wp_query->set_404();
				}
			}
		}
		public function afgc_product_search() {
			$search_query = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
			$products     = wc_get_products(
				array(
					'status' => 'publish',
					'limit'  => -1,
					's'      => $search_query,
				)
			);

			$results = array();
			foreach ( $products as $product ) {
				if ( $product && 'simple' === $product->get_type() ) {
					$results[] = array(
						'id'    => $product->get_id(),
						'text'  => $product->get_name(),
						'price' => $product->get_price(),
					);
				}
			}

			wp_send_json( $results );
		}

		public function afgc_order_cron_schedule_cb() {
			if (!wp_next_scheduled('afgc_check_last_20_days_orders')) {
				if ('yes' !== get_post_meta( $order_id, '_afgc_scheduled_cron', true )) {
					wp_schedule_event(time(), 'daily', 'afgc_check_last_20_days_orders');
					update_post_meta ( $order_id , '_afgc_scheduled_cron' , 'yes' );
				}
			}
		}

		public function afgc_devlivery_cron_schedules( $schedules ) {
			$schedules['adf_mail_cron'] = array(
				'interval' => 60 * 60 * 24,
				'display'  => __('Every Day', 'addify_gift_cards'),
			);
			return $schedules;
		}


		public function adf_check_last_orders_for_delivery() {
			$args   = array(
				'status' => array( 'completed' ),
				'limit'  => -1,
			);
			$orders = wc_get_orders($args);

			foreach ($orders as $order) {
				if (!empty($order)) {
					$order_id = $order->get_id();
				}
				$af_item_metas      = array();
				$af_gift_card_found = false;
				foreach ($order->get_items() as $item_id => $item) {
					$product = $item->get_product();
					if ($product && $product->is_type('gift_card')) {
						$af_item_metas[]      = $item->get_meta_data();
						$af_gift_card_found   = true;
						$existing_coupon_code = wc_get_order_item_meta( $item_id, 'afgc_cp', true );
					}
				}

				if ($af_gift_card_found) {
					foreach ($af_item_metas as $single_af_item_meta) {
					
						foreach ($single_af_item_meta as $meta_value) {

							$meta_data = $meta_value->get_data();

							if (isset($meta_data['value']['afgc_delivery_date'])) {
								$delivery_date      = $meta_data['value']['afgc_delivery_date'];
								$delivery_timestamp = strtotime($delivery_date);
								$current_date       = current_time('Y-m-d');
								if ($delivery_timestamp && gmdate('Y-m-d', $delivery_timestamp) === $current_date) {
									$this->afgc_coupon_code_email_cb( $order_id );
								}
							}
							error_log('dsfffffffffffff');
							if (!empty($existing_coupon_code) && 'afgc_gift_card_infos' == $meta_data['key']  ) {
								$coupon     = new WC_Coupon( $existing_coupon_code );
								$expiry_obj = $coupon->get_date_expires();

								if ( $expiry_obj ) {
									$expiry_date    = $expiry_obj->date_i18n( 'Y-m-d' ); 
									$one_day_before = date_i18n( 'Y-m-d', strtotime( '-1 day', $expiry_obj->getOffsetTimestamp() ) );
									$current_date   = current_time( 'Y-m-d' );
									$remaining      = $coupon->get_amount();

									$reciepts_user_name = isset($meta_data['value']['afgc_recipient_name1']) ? $meta_data['value']['afgc_recipient_name1']:'';
									$reciepts_email     = isset($meta_data['value']['afgc_recipient_email1']) ? $meta_data['value']['afgc_recipient_email1']: '';
									$total_amount       = isset($meta_data['value']['afgc_price_of_gift_card']) ? $meta_data['value']['afgc_price_of_gift_card']: '';
									
									if ( $one_day_before === $current_date && floatval( $remaining ) > 0 && !empty($reciepts_email) ) {
										$this->afgc_coupon_expiry_reminder_email_cb( $existing_coupon_code, $expiry_date, $remaining, $reciepts_email, $reciepts_user_name, $total_amount );
									}
								}
							}
						}
					}
				}
			}
		}
		public function afgc_cron_schedule_cb() {
			if (!wp_next_scheduled('afgc_check_last_20_days_orders')) {
				wp_schedule_event(time(), 'adf_mail_cron', 'afgc_check_last_20_days_orders');
			}
		}

		public function afgc_cron_unschedule_cb() {
			$timestamp = wp_next_scheduled('afgc_check_last_20_days_orders');
			if ($timestamp) {
				wp_unschedule_event($timestamp, 'afgc_check_last_20_days_orders');
			}
		}
		public function afgc_coupon_expiry_reminder_email_cb( $existing_coupon_code, $expiry_date, $remaining, $reciepts_email, $customer_name, $total_amount ) {
			$subject         = __( 'Your coupon is about to expire!', 'addify_gift_cards' );
			$message         = sprintf(
				"Hi %s,\n\nWe noticed you haven’t used your coupon yet — and it’s about to expire soon! \n\nHere are the details of your coupon:\n\nCoupon Code: %s\nRemaining Balance: %s\nExpiry Date: %s\n\nMake sure to use it before it’s gone!",
				$customer_name,
				$existing_coupon_code,
				wc_price( $remaining ),
				$expiry_date
			);
			$placeholders    = array(
				'{customer_name}'      => $customer_name,
				'{coupon_code}'        => $existing_coupon_code,
				'{coupon_amount}'      => wc_price( $total_amount ),
				'{coupon_remaining}'   => wc_price( $remaining ),
				'{coupon_expiry_date}' => $expiry_date,
				'{coupon_products}'    => ! empty( $product_names ) ? implode( ', ', $product_names ) : __( 'All Products', 'addify_gift_cards' ),
			);
			$template        = get_option('afgc_gift_reminder_message');
			$message         = strtr( $template, $placeholders );
			$subject         = __( 'Your coupon is about to expire!', 'addify_gift_cards' );
			$mailer          = WC()->mailer();
			$wrapped_message = $mailer->wrap_message( $subject, nl2br( $message ) );
			$headers         = array( 'Content-Type: text/html; charset=UTF-8' );
			$mailer->send( $reciepts_email, $subject, $wrapped_message, $headers );
		}
		public function af_remove_add_new_button_for_gift_card() {
			global $wp_post_types;
			if ( isset( $wp_post_types['afgc_gift_card_log'] ) ) {
				$wp_post_types['afgc_gift_card_log']->cap->create_posts = 'do_not_allow';
			}
		}
	}

	new Addify_Gift_Card();

}

