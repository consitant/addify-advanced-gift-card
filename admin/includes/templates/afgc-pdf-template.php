<?php

global $post;

if ( isset( $_GET['download_gf_id'] ) ) {
	$log_id = str_replace( 'http://', '', sanitize_text_field( wp_unslash( $_GET['download_gf_id'] ) ) );
} else {
	$log_id = $gift_card_id;
}
$item_id           = get_post_meta( $log_id, '_item_id', true );
$item              = new WC_Order_Item_Product( $item_id );
$item_data         = $item->get_data();
$items_meta_data[] = $item->get_meta_data();
$product           = $item->get_product();
$afgc_coupon_code  = $item->get_meta( 'afgc_cp' );
$afgc_to_from_info = $item->get_meta( 'afgc_gift_card_infos' );
if ( isset( $afgc_to_from_info['afgc_selected_img'] ) ) {
	$src = $afgc_to_from_info['afgc_selected_img'];
} else {
	$src = '';
}

$coupon                        = new WC_Coupon( $afgc_coupon_code );
$coupon_exp                    = $coupon->get_date_expires();
$coupon_amount                 = $coupon->get_amount();
$afgc_name                     = $product->get_name();
$is_afgc_virtual               = get_post_meta( $product->get_id(), 'afgc_virtual', true );
$afgc_enable_store_name_pdf    = get_option( 'afgc_enable_store_name_pdf' ); // Enable Store Title.
$afgc_store_name_pdf           = get_option( 'afgc_store_name_pdf' ); // Store Title.
$afgc_enable_store_logo_pdf    = get_option( 'afgc_enable_store_logo_pdf' ); // Enable Store Logo.
$afgc_enable_gift_card_image   = get_option( 'afgc_enable_gift_card_image' ); // Enable Gift Card Image.
$afgc_enable_gift_card_name    = get_option( 'afgc_enable_gift_card_name' ); // Enable Gift Card Name.
$afgc_enable_gift_card_price   = get_option( 'afgc_enable_gift_card_price' ); // Enable Gift Card Price.
$afgc_enable_gift_card_code    = get_option( 'afgc_enable_gift_card_code' ); // Enable Coupon Code.
$afgc_enable_gift_card_message = get_option( 'afgc_enable_gift_card_message' ); // Enable Gift Card Message.
$afgc_gift_card_message        = get_option( 'afgc_gift_card_message' ); // Gift Card Message.
$afgc_enable_disclaimer        = get_option( 'afgc_enable_disclaimer' ); // Enable Disclaimer.
$afgc_disclaimer_text          = get_option( 'afgc_disclaimer_text' ); // Disclaimer.
$afgc_gift_type_select         = ( is_array( $afgc_to_from_info ) && array_key_exists( 'afgc_gift_type', $afgc_to_from_info ) ) ? $afgc_to_from_info['afgc_gift_type'] : '';
$afgc_gift_products            = ( is_array( $afgc_to_from_info ) && array_key_exists( 'afgc_gift_products', $afgc_to_from_info ) ) ? $afgc_to_from_info['afgc_gift_products'] : '';
if ( 'yes' == $is_afgc_virtual ) {

	// Sender Message.
	$afgc_sender_message = isset( $afgc_to_from_info['afgc_sender_message'] ) ? $afgc_to_from_info['afgc_sender_message'] : '';

	// Sender Name.
	$afgc_sender_name = isset( $afgc_to_from_info['afgc_sender_name'] ) ? $afgc_to_from_info['afgc_sender_name'] : '';

	// Recipient Name.
	$afgc_recipient_name = isset( $afgc_to_from_info['afgc_recipient_name1'] ) ? $afgc_to_from_info['afgc_recipient_name1'] : '';

	// Recipient Email.
	$afgc_recipient_email = isset( $afgc_to_from_info['afgc_recipient_email1'] ) ? $afgc_to_from_info['afgc_recipient_email1'] : '';
} else {

	// physical Sender Message.
	$afgc_sender_message = isset( $afgc_to_from_info['afgc_phy_gift_sender_message'] ) ? $afgc_to_from_info['afgc_phy_gift_sender_message'] : '';

	// physical Sender Name.
	$afgc_sender_name = isset( $afgc_to_from_info['afgc_phy_gift_sender_name'] ) ? $afgc_to_from_info['afgc_phy_gift_sender_name'] : '';

	// physical Recipient Name.
	$afgc_recipient_name = isset( $afgc_to_from_info['afgc_phy_gift_recipient_name'] ) ? $afgc_to_from_info['afgc_phy_gift_recipient_name'] : '';
}

$afgc_store_logo = get_option( 'afgc_store_logo' );

$matching_name = '';
if (is_user_logged_in()) {
	$logged_in_user = wp_get_current_user();
	if (isset($afgc_to_from_info['total_recipient_user_Select'])) {
		$current_user_email = $logged_in_user->user_email;
		for ($i = 1; $i <= $afgc_to_from_info['total_recipient_user_Select']; $i++) {
			if (
				isset($afgc_to_from_info[ "afgc_recipient_email$i" ]) &&
				$afgc_to_from_info[ "afgc_recipient_email$i" ] === $current_user_email
			) {
				$matching_name = $afgc_to_from_info[ "afgc_recipient_name$i" ];
				break;
			}
		}
	}
	if ($matching_name) {
		$afgc_recipient_name = $matching_name;
	}
}
?>
<style>
.coupon-code-preiew a{
		font-size: 14px;
	line-height: 16px;
	background: none;
	color: #000;
	text-decoration: none;
	padding: 10px 15px;
	border-radius: 2px;
	display: inline-block;
	margin-top: 5px;
	margin-bottom: 8px;
	border: 1px solid lightgray;
}
.afd-preview-product-name-wrap span,
.afd-preview-expire-data-wrap span{
	margin-left: 8px;
}
.coupon-code-preiew{
		margin: 16px 0 20px;
}
.afgc-pdf-band p{
	font-size: 15px;
	margin-bottom: 5px;
	margin-top: 0;
	line-height: 25px;
}
.afd-preview-product-name-wrap, .afd-preview-expire-data-wrap{
		font-size: 14px;
	line-height: 24px;
}
.afgc-pdf-band .afg-received-message {
	text-align: center;
	font-size: 17px;
	margin-bottom: 0;
	margin-top: 0;
	font-family: sans-serif;
	line-height: 27px;
}
.adf-stor-name{
	text-align: center;
	font-size: 17px;
	margin-top: -5px;
	font-family: sans-serif;
	line-height: 27px;
	font-weight: 500;
}
.afgc-pdf-header, .afgc-template-box, body{
	text-align: center;
	font-family: sans-serif!important;
}
.afgc-disclaimer-message p{
	margin: 0;
	color: #000;
	font-size: 14px;
	line-height: 24px;
	text-align: left;
}
.afgc-pdf-band h2{
		font-size: 23px;
	line-height: 33px;
	font-weight: 700;
	color: #000;
}
.afgc-card-image img {
	width: 90%;
	margin: 0 auto;
	height: auto;
}

.afgc-pdf-band img{
	width: auto;
	max-width: 118px;
	margin-bottom: 0;
}
	</style>
	<div class="afgc-template-box">
		<div class="afgc-pdf-header">
			<div class="afgc-pdf-band">
				<span>
					<?php if ( 'yes' == $afgc_enable_store_logo_pdf ) { ?>
						<?php if ( !empty($afgc_store_logo) ) { ?>
							<img src="<?php echo esc_url($afgc_store_logo); ?>">
						<?php } else { ?>
							<img src="<?php echo esc_url( wp_get_attachment_url( get_theme_mod( 'custom_logo' ) ) ); ?>">
						<?php } ?>
					<?php } ?>

				</span>
				<?php if ( 'yes' == $afgc_enable_store_name_pdf ) { ?>
				<p class="adf-stor-name">
					<?php echo esc_html( $afgc_store_name_pdf ); ?>
				</p>
				<?php } ?>
				<h2><?php echo esc_html__('Dear ', 'addify_gift_cards'); ?><span id="afgc_recipient_name_pre"><?php echo esc_attr( $afgc_recipient_name ); ?></span></h2>
				<?php if ('yes' == $afgc_enable_gift_card_price ) { ?>
				<p class="afg-received-message"><?php echo esc_html__('You have received a gift card worth ', 'addify_gift_cards'); ?><span id="afgc_selected_price" style="font-family:DejaVu Sans;"><?php echo esc_html( get_woocommerce_currency_symbol() ) . esc_attr( $coupon_amount ); ?></span> <?php echo esc_html__('from', 'addify_gift_cards'); ?> <span id="afgc_pre_sender_name"><?php echo esc_attr( $afgc_sender_name ); ?></span></p>
				<?php } ?>
				<p class="afg-preview-sender-message"><?php echo esc_html( $afgc_sender_message ); ?></p>
			</div>
			<?php if ( 'yes' == $afgc_enable_gift_card_name ) { ?>
			<div class="afd-preview-product-name-wrap">
				<strong><?php echo esc_html__('Name', 'addify_gift_cards'); ?>:</strong><span class="afd-preview-product-name"><?php echo esc_attr( $afgc_name ); ?></span>
			</div>
			<?php } ?>
			<?php
			if ( 'product-gift' == $afgc_gift_type_select && !empty($afgc_gift_products) ) {
				$product      = wc_get_product($afgc_gift_products);
				$product_name = $product->get_name();
				$product_url  = get_permalink($afgc_gift_products);
				?>
			<div class="afd-preview-product-name-wrap">
				<strong><?php echo esc_html__('Product Gift', 'addify_gift_cards'); ?>:</strong><a href="<?php echo esc_url($product_url); ?>"><span class="afd-preview-product-name"><?php echo esc_attr( $product_name ); ?></span></a>
			</div>
			<?php } ?>
			<?php if ( $coupon_exp ) { ?>
			<div class="afd-preview-expire-data-wrap">
				<strong><?php echo esc_html__('Expiry Date', 'addify_gift_cards'); ?>:</strong><span class="afd-preview-expire-data"><?php $afgc_date_format_option = !empty(get_option('afgc_date_format')) ? get_option('afgc_date_format', 'M-d-Y'): 'M-d-Y'; ?>
				<?php echo esc_attr( date_format( $coupon_exp, $afgc_date_format_option ) ); ?></span>
			</div>
			<?php } ?>
			<?php
			if ( 'yes' == $afgc_enable_gift_card_code ) {
				$checkout_url = wc_get_page_permalink('cart');
				$coupin_url   = $checkout_url . '?coupon_code=' . $afgc_coupon_code;
				?>
			<div class="coupon-code-preiew">
				<a href="<?php echo esc_url($coupin_url); ?>"><?php echo esc_html($afgc_coupon_code); ?></a>
			</div>
			<?php } ?>
			<?php if ( 'yes' == $afgc_enable_gift_card_image ) { ?>
				<div class="afgc-card-image">
					<?php if ( 'yes' == $is_afgc_virtual && ! empty( $src ) ) { ?>
						<img src="<?php echo esc_url( $src ); ?>">
					<?php } else { ?>
						<?php echo wp_kses_post( $product->get_image() ); ?>
					<?php } ?>
				</div>
			<?php
			}
			if ( 'yes' == $afgc_enable_gift_card_message ) {
				?>
				<p style="padding-bottom: 10px;"><?php echo esc_attr( $afgc_gift_card_message ); ?></p>
			<?php
			}
			if ( 'yes' == $afgc_enable_disclaimer ) {
				?>
				<div class="afgc-disclaimer-message">
					<p><strong><?php echo esc_html__('Disclaimer: ', 'addify_gift_cards'); ?></strong><?php echo esc_html($afgc_disclaimer_text); ?></p>
				</div>
			<?php } ?>
		</div>
	</div>

		