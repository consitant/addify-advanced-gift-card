<?php

	defined( 'ABSPATH' ) || exit;

	/*
	 * @hooked WC_Emails::email_header() Output the email header.
	*/
	do_action( 'woocommerce_email_header', $email_heading, $email );

if ( $additional_content ) {

	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );

}


$curren_item_id = $curren_item_id;
$afgc_order     = wc_get_order( $order_id );
if (!empty($afgc_order)) {
	foreach ( $afgc_order->get_items() as $item_id => $item ) {

		if ( (int) $item_id !== (int) $curren_item_id ) {
			continue;
		}

		$product_name = $item->get_name();

		$product = $item->get_product();

		$product_id = $item->get_product_id();

		$is_afgc_virtual = get_post_meta( $product_id, 'afgc_virtual', true );

		$product_image = $product->get_image();

		$afgc_coupon_code = $item->get_meta( 'afgc_cp' );

		$coupon_info = new WC_Coupon( $afgc_coupon_code );

		$afgc_coupon_amount = $coupon_info->get_amount();

		$coupon_exp = $coupon_info->get_date_expires();

		$afgc_to_from_info = (array) $item->get_meta( 'afgc_gift_card_infos' );

		if ( isset( $afgc_to_from_info['afgc_selected_img'] ) ) {

			$src = $afgc_to_from_info['afgc_selected_img'];

		} else {

			$src = '';

		}

		// Sender Message.
		$afgc_sender_message = isset( $afgc_to_from_info['afgc_sender_message'] ) ? $afgc_to_from_info['afgc_sender_message'] : '';
		$afgc_gift_type      = isset( $afgc_to_from_info['afgc_gift_type'] ) ? $afgc_to_from_info['afgc_gift_type'] : '';
		$afgc_gift_products  = in_array( 'afgc_gift_products', array_keys( $afgc_to_from_info ), true ) ? $afgc_to_from_info['afgc_gift_products'] : '';

		// Sender Name.
		$afgc_sender_name = isset( $afgc_to_from_info['afgc_sender_name'] ) ? $afgc_to_from_info['afgc_sender_name'] : '';

		// Recipient Name.
		$afgc_recipient_name = isset( $afgc_to_from_info['afgc_recipient_name1'] ) ? $afgc_to_from_info['afgc_recipient_name1'] : '';

		if ( 'yes' == $is_afgc_virtual ) {

			// Recipient Email.
			$afgc_recipient_email = isset( $afgc_to_from_info['afgc_recipient_email1'] ) ? $afgc_to_from_info['afgc_recipient_email1'] : '';
		}
	}


	?>

	<div style="margin-bottom: 40px;">

		<table class="afgc-gift-card-recp-email-table td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">

			<thead></thead>

			<tbody>

				<tr>
					<td colspan="2" class="td" scope="col">

						<?php if ( ! empty( $src ) ) { ?>

							<img src="<?php echo esc_url( $src ); ?>">

						<?php } else { ?>

							<?php echo wp_kses_post( $product->get_image() ); ?>

						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="td" scope="col">
						<strong><?php esc_html_e( 'Name', 'addify_gift_cards' ); ?></strong></td>
					<td class="td" scope="col"> 
						<?php echo esc_attr( $product_name ); ?>
					</td>
				</tr>

				<tr>

					<td class="td" scope="col">
						<strong><?php esc_html_e( 'Coupon Code', 'addify_gift_cards' ); ?></strong></td>

					<td class="td" scope="col"> 
						<?php
						$checkout_url = wc_get_page_permalink('cart');
						$coupin_url   = $checkout_url . '?coupon_code=' . $referral_coupon_code;
						?>
						<a href="<?php echo esc_url($coupin_url); ?>"><?php echo esc_attr( $referral_coupon_code ); ?></a>
					</td>

				</tr>
				<?php
				if ( 'product-gift' == $afgc_gift_type && !empty($afgc_gift_products) ) {
					$product         = wc_get_product($afgc_gift_products);
					$product_name    = $product->get_name();
					$product_url     = get_permalink($afgc_gift_products);
					$add_to_cart_url = add_query_arg( 'add-to-cart', $afgc_gift_products, $product_url );
					?>
				<tr>

					<td class="td" scope="col">
						<strong><?php esc_html_e( 'Product Gift', 'addify_gift_cards' ); ?></strong></td>

					<td class="td" scope="col"> 
						<a href="<?php echo esc_url($add_to_cart_url); ?>"><?php echo esc_attr( $product_name ); ?></a>
					</td>

				</tr>
				<?php if (!empty($coupon_exp)) { ?>
				<tr>
					<td class="td" scope="col">
						<strong><?php esc_html_e( 'Expiry Date', 'addify_gift_cards' ); ?></strong></td>
					<td class="td" scope="col"> 
						<?php
						$afgc_date_format_option = !empty(get_option('afgc_date_format')) ? get_option('afgc_date_format', 'M-d-Y'): 'M-d-Y';

						echo esc_attr( date_format( $coupon_exp, $afgc_date_format_option ) );
						?>
					</td>
				</tr>
				<?php } ?>
				<?php } ?>
	<?php if ( ! empty( $afgc_coupon_amount ) ) { ?>
				<tr>

					<td class="td" scope="col">
						<strong><?php esc_html_e( 'Coupon Amount', 'addify_gift_cards' ); ?></strong></td>

					<td class="td" scope="col"> 
						<?php

						// $afgc_coupon_price_html=
						echo wp_kses_post(wc_price( esc_attr( $afgc_coupon_amount ) ));

						// echo esc_attr($afgc_coupon_price_html);
						?>
					</td>

				</tr>
	<?php } ?>
				<tr>

					<td class="td" scope="col" style="vertical-align: top !important;">
						<strong><?php echo esc_html__( 'From', 'addify_gift_cards' ); ?></strong>
						<p><?php echo esc_attr( $afgc_sender_name ); ?></p>
					</td>

					<td class="td" scope="col" style="vertical-align: top !important; ">
						<strong><?php echo esc_html__( 'To', 'addify_gift_cards' ); ?></strong>

						<?php
						if ( 'yes' == $is_afgc_virtual ) {

							for ($i = 1; $i <= $afgc_to_from_info['total_recipient_user_Select']; $i++) {
								if ( isset( $afgc_to_from_info[ 'afgc_recipient_email' . $i ] ) ) {
									$recipient_matching_name    = $afgc_to_from_info[ "afgc_recipient_name$i" ];
									$afgc_recipient_email_match = $afgc_to_from_info[ "afgc_recipient_email$i" ];
									?>
									<p style="margin-top: 4px;">
									<span style="display: block; margin-bottom: 3px;"><?php echo esc_attr( $recipient_matching_name ); ?></span>
									<?php echo esc_attr( $afgc_recipient_email_match ); ?></p>
									<?php
								}
							}
						}
						?>
					</td>

				</tr>

				<tr>
					<td colspan="2" class="td" scope="col">
						<strong><?php echo esc_html__( 'Message', 'addify_gift_cards' ); ?></strong>
						<p><?php echo esc_attr( $afgc_sender_message ); ?></p>
					</td>
				</tr>

			</tbody>

		</table>

	</div>

		<style>
			.afgc-gift-card-recp-email-table{
				width: 100%;
			}

			.afgc-gift-card-recp-email-table td,
			.afgc-gift-card-recp-email-table th{
				padding: 15px;
				
			}

			.afgc-gift-card-recp-email-table td img{
				width: 40%;
				display: block;
				margin: 20px auto;
			}
		</style>

	<?php
}
/*
* @hooked WC_Emails::email_footer() Output the email footer.
*/
do_action( 'woocommerce_email_footer', $email );
