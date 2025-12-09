<?php

/**
 *  Gift Card Dashboard.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_settings_section(
	'afgc-dashboard-sec',                            // ID used to identify this section and with which to register options.
	'',                                             // Title to be displayed on the administration page.
	'',                                            // Callback used to render the description of the section.
	'afgc_dashboard_section'                      // Page on which to add this section of options.
);

add_settings_field(
	'afgc_dashboard_card_list',                   // ID used to identify the field throughout the theme.
	'',
	'afgc_dashboard_card_list_cb',              // The name of the function responsible for rendering the option interface.
	'afgc_dashboard_section',                  // The page on which this option will be displayed.
	'afgc-dashboard-sec',                     // The name of the section to which this field belongs.
	''
);

register_setting(
	'afgc_dashboard_fields',
	'afgc_dashboard_card_list',
	array(
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_text_field',
	)
);

function afgc_dashboard_card_list_cb( $args = array() ) {

	global $product, $post, $woocommerce;

	$user_id                = get_current_user_id();
	$current_user_email     = get_user_by( 'id', $user_id )->data->user_email;
	$old_data_of_user       = (array) get_user_meta( $user_id, 'gift_card_send_to_user', true );
	$old_data_card_purchase = (array) get_user_meta( get_current_user_id(), 'gift_card_purchase_for_user', true );
	$afgc_used_gift_card    = 0;
	$afgc_debit_amount      = 0;
	$afgc_credit_amount     = 0;
	$afgc_net_amount        = 0;

	$gift_card_posts = get_posts( array( 
		'post_type'   => 'afgc_gift_card_log',
		'post_status' => 'publish',
		'numberposts' => -1,
	) );
	if (!empty($gift_card_posts) && is_array($gift_card_posts)) {
		foreach ( $gift_card_posts as $gift_card ) {
			$coupon_total_amount    = floatval( get_post_meta( $gift_card->ID, '_gift_card_price', true ) );
			$afgc_coupon_rem_amount = floatval( get_post_meta( $gift_card->ID, '_afgc_coupon_rem_amount', true ) );
			$afgc_gift_card_status  = get_post_meta( $gift_card->ID, '_gift_card_status', true );
			if ( 'pending' == $afgc_gift_card_status ) {

				if ( empty( $afgc_coupon_rem_amount ) ) {

					$afgc_coupon_rem_amount = $coupon_total_amount;
				}
			}

			if ( 'accomplish' == $afgc_gift_card_status ) {

				$afgc_debit_amount += $coupon_total_amount; // Used Amount.

			} else {

				$afgc_debit_amount += $coupon_total_amount - $afgc_coupon_rem_amount; // Used Amount.

			}

			$afgc_credit_amount += $afgc_coupon_rem_amount; // Remaining Amount.

			$afgc_net_amount += $coupon_total_amount; // Total Amount.
++$afgc_used_gift_card;
			// $afgc_used_gift_card;
		}
	}
	?>

	<div class="afgc-dashboard-section">
		<div class="afgc-dashboard-card-list">
			<h2><?php echo esc_html__( 'Gift Cards', 'addify_gift_cards' ); ?></h2>
			<table>
				<thead>
					<tr>
						<td class="afgc-coupon-code"><strong><?php echo esc_html__( 'Gift Card Code', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-product-name"><strong><?php echo esc_html__( 'Gift Cards', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-recp-email"><strong><?php echo esc_html__( 'Recipient', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-used-amount"><strong><?php echo esc_html__( 'Used Amount', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-remainig-amount"><strong><?php echo esc_html__( 'Remaining Amount', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-total-amount"><strong><?php echo esc_html__( 'Total Amount', 'addify_gift_cards' ); ?></strong></td>
						<td class="afgc-download-col"><strong><?php echo esc_html__( 'Download', 'addify_gift_cards' ); ?></strong></td>
					</tr>
				</thead>
				<tbody>
					<?php
						global $post;

						$paged = ( isset( $_GET['paged'] ) && ! empty( $_GET['paged'] ) ) ? intval( $_GET['paged'] ) : 1;

						$args = array(
							'post_type'      => 'afgc_gift_card_log',
							'post_status'    => 'publish',
							'posts_per_page' => 8,
							'orderby'        => 'title',
							'order'          => 'ASC',
							'paged'          => $paged,
						);

						$query = new WP_Query( $args );

						if ( $query->have_posts() ) :

							while ( $query->have_posts() ) :

								$query->the_post();

								$afgc_coupon_code = get_post_meta( get_the_ID(), '_coupon_code', true );

								if ( empty( $afgc_coupon_code ) ) {

									continue;

								}

								$afgc_gift_card_status = get_post_meta( get_the_ID(), '_gift_card_status', true );

								$afgc_coupon_rem_amount = floatval( get_post_meta( get_the_ID(), '_afgc_coupon_rem_amount', true ) );

								$coupon = new WC_Coupon( $afgc_coupon_code );

								$coupon_id = $coupon->get_id();

								$coupon_total_amount = floatval( get_post_meta( get_the_ID(), '_gift_card_price', true ) );

								$coupon_amount = $coupon->get_amount();

								$afgc_purchase_for = (array) get_post_meta( intval( $post->ID ), '_purchase_for', true );

								if ( isset( $afgc_purchase_for['afgc_phy_gift_recipient_email'] ) ) {

									$afgc_phy_gift_recipient_email = $afgc_purchase_for['afgc_phy_gift_recipient_email'];

								} else {

									$afgc_phy_gift_recipient_email = '';
								}

								$afgc_order_id = get_post_meta( intval( $post->ID ), '_order_id', true );

								$order = wc_get_order( $afgc_order_id );

								if ( ! $order ) {

									continue;
								}

								$afgc_item_id = get_post_meta( intval( $post->ID ), '_item_id', true );

								$item = new WC_Order_Item_Product( $afgc_item_id );

								$product = $item->get_product();

								$afgc_product_id = $product->get_id();

								$afgc_product_name = $product->get_name();

								if ( $afgc_coupon_code ) {

									?>

								<tr>
									<td class="afgc-coupon-code">
										<strong><a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>"><?php echo esc_attr( $afgc_coupon_code ); ?></a></strong>
									</td>
									<td class="afgc-product-name">
										<strong>
											<a href="<?php echo esc_url( get_edit_post_link( $afgc_product_id ) ); ?>"><?php echo esc_attr( $afgc_product_name ); ?></a>
										</strong>
									</td>
									<td class="afgc-recp-email">
										<?php

										if ( isset( $afgc_purchase_for['total_recipient_user_Select'] ) ) {

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
									<td class="afgc-used-amount">

										<?php
										if ( 'purchased' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( '0' ) );

										} elseif ( 'pending' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( '0' ) );

										} elseif ( 'partial delivered' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( (int) $coupon_total_amount - (int) $coupon_amount ) );

										} elseif ( 'accomplish' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( $coupon_total_amount ) );

										}
										?>

									</td>

									<td class="afgc-remainig-amount">

										<?php
										if ( 'purchased' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( $coupon_total_amount ) );

										} elseif ( 'pending' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( $coupon_amount ) );

										} elseif ( 'partial delivered' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( $afgc_coupon_rem_amount ) );

										} elseif ( 'accomplish' == $afgc_gift_card_status ) {

											echo wp_kses_post( wc_price( '0' ) );

										}
										?>

									</td>

									<td class="afgc-total-amount">
										<p> <?php echo wp_kses_post( wc_price( $coupon_total_amount ) ); ?> </p>
									</td>

									<td class="afgc-download-col">
										<a href="?download_gf_id=<?php echo intval( $post->ID ); ?>"><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/download-icon.png' ) ); ?>"></a>
									</td>
								</tr>

									<?php

								}

					endwhile;

					endif;

						?>
						
				</tbody>
			</table>
				<div class="pagination adf-dashboard-pagination">
		<?php
		$big = 999999999;
		echo wp_kses_post( paginate_links(
			array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $paged ),
				'total'     => $query->max_num_pages,
				'prev_text' => __( '« Previous', 'addify_gift_cards' ),
				'next_text' => __( 'Next »', 'addify_gift_cards' ),
			)
		) );
		?>
	</div>
		</div>
		<div class="afgc-dashboard-nav">
			<h2><?php echo esc_html__( 'Dashboard', 'addify_gift_cards' ); ?></h2>
			<div class="afgc-dashboard-box">
				<div class="afgc_dashboard-item-content">
					<h4><?php echo esc_html__( 'Total Gift Cards', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/gift.png' ) ); ?>"></h4>
					<p><?php echo esc_attr( $afgc_used_gift_card ); ?></p>
				</div>
			</div>
			<div class="afgc-dashboard-box">
				<div class="afgc_dashboard-item-content">
					<h4><?php echo esc_html__( 'Used Amount', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/debit.png' ) ); ?>"></h4>
					<p><?php echo wp_kses_post( wc_price( $afgc_debit_amount ) ); ?> </p>
				</div>
			</div>
			<div class="afgc-dashboard-box">
				<div class="afgc_dashboard-item-content">
					<h4><?php echo esc_html__( 'Remaining Amount', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/credit.png' ) ); ?>"></h4>
					<p><?php echo wp_kses_post( wc_price( $afgc_credit_amount ) ); ?> </p>
				</div>
			</div>
			<div class="afgc-dashboard-box">
				<div class="afgc_dashboard-item-content">
					<h4><?php echo esc_html__( 'Total Amount', 'addify_gift_cards' ); ?><img src="<?php echo esc_url( plugins_url( 'addify-advanced-gift-card/admin/assets/images/net.png' ) ); ?>"></h4>
					<p><?php echo wp_kses_post( wc_price( $afgc_net_amount ) ); ?> </p>
				</div>
			</div>
		</div>
	</div>


	<?php
}
