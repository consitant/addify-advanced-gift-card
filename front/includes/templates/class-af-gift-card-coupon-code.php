<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Af_Gc_Coupon_Code {

	public function afgc_referred_coupon_generate( $product_id, $afgc_coupoun_amount, $user_email, $afgc_gift_type, $afgc_gift_products ) {

		$afgc_code = wp_rand( 1, 100 );

		$afgc_prefix = get_option( 'afgc_coupon_prefix' );

		$afgc_coupon_code = $afgc_prefix . substr( str_shuffle( md5( $afgc_code ) ), 0, 5 );

		$afgc_selected_product = array();
		if ('product-gift' == $afgc_gift_type) {
			$afgc_selected_product = (array) $afgc_gift_products;
		} else {
			$afgc_selected_product     = (array) get_post_meta( $product_id, 'afgc_select_product', true );
			$afgc_selected_product_cat = (array) get_post_meta( $product_id, 'afgc_select_categories', true );
		}

		$afgc_selected_product_ids_of_cat = $afgc_selected_product_cat;
		$afgc_selected_product_ids        = $afgc_selected_product;

		$afgc_expiration_days = get_post_meta( $product_id, 'afgc_expiration_date', true );

		$afgc_expiration_date = '';

		if ( ! empty( $afgc_expiration_days ) ) {

			$afgc_expiration_date = gmdate( 'Y-m-d', strtotime( '+' . $afgc_expiration_days . 'days' ) );
		}

		$coupon = array(
			'post_title'   => $afgc_coupon_code,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'shop_coupon',
		);

		$new_coupon_id = wp_insert_post( $coupon );

		// $this->coupon_id = $new_coupon_id;

		$coupon      = new WC_Coupon( $new_coupon_id );
		$email_count = 0;
		if (!empty($user_email)) {
			$email_array = explode(',', $user_email);
			$email_count = count( $email_array );
		}

		$coupon->set_props(
			array(
				'code'                        => $afgc_coupon_code,
				'discount_type'               => 'fixed_cart',
				'amount'                      => $afgc_coupoun_amount,
				'date_expires'                => $afgc_expiration_date,
				'individual_use'              => '',
				'product_ids'                 => $afgc_selected_product_ids,
				'excluded_product_ids'        => array(),
				'usage_limit'                 => $email_count ? absint($email_count + 2) : absint( 4 ),
				'usage_limit_per_user'        => absint( '' ),
				'limit_usage_to_x_items'      => absint( '' ),
				'free_shipping'               => true,
				'product_categories'          => $afgc_selected_product_ids_of_cat,
				'excluded_product_categories' => array(),
				'exclude_sale_items'          => false,
				'minimum_amount'              => wc_format_decimal( '' ),
				'maximum_amount'              => wc_format_decimal( '' ),
				'email_restrictions'          => array(), // No email restriction - coupon can be used by anyone
			)
		);

			update_post_meta( $new_coupon_id, 'coupon_type', 'afgc_coupon' );

			update_post_meta( $new_coupon_id, 'coupon_total_amount', $afgc_coupoun_amount );

			$coupon->save();

		return $afgc_coupon_code;
	}
}

if ( class_exists( 'Af_Gc_Coupon_Code' ) ) {
	new Af_Gc_Coupon_Code();
}
