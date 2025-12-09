<?php

	global $post;

	$afgc_item_id = get_post_meta( $post->ID, '_item_id', true );

	$afgc_order_id = get_post_meta( $post->ID, '_order_id', true );

	$afgc_coupon_code = get_post_meta( $post->ID, '_coupon_code', true );

	$afgc_all_orders = wc_get_orders(
		array(
			'limit'  => -1,
			'return' => 'ids',
		)
	);


	$usage_of_coupon = array();

	$sbc_cp_code  = array();
	$redeem_price = 0;

	foreach ( $afgc_all_orders as $afgc_order ) {

		$afgc_getorder = wc_get_order( $afgc_order );

		if ( $afgc_getorder ) {

			$afgc_coupon_info = $afgc_getorder->get_items( 'coupon' );

			foreach ( $afgc_coupon_info as $value ) {

				$sbc_cp_code = $value->get_data()['code'];

				if ( $afgc_coupon_code == $sbc_cp_code ) {

					$usage_of_coupon[] = array(

						'discount' => $value->get_data()['discount'],

						'order_id' => $value->get_order_id(),
						'date'     => $afgc_getorder->get_date_completed()
						? $afgc_getorder->get_date_completed()->getTimestamp()
						: $afgc_getorder->get_date_created()->getTimestamp(),

					);
				}
			}
		}
	}

	$afgc_coupon_rem_amount = get_post_meta( $post->ID, '_afgc_coupon_rem_amount', true );

	$afgc_digital_checkbox = get_post_meta( intval( $post->ID ), 'afgc_digital_checkbox', true );

	$item = new WC_Order_Item_Product( $afgc_item_id );

	$product_id = $item->get_product_id();

	$is_afgc_virtual = get_post_meta( $product_id, 'afgc_virtual', true );

	$coupon = new WC_Coupon( $afgc_coupon_code );

	$coupon_id = $coupon->get_id();

	$coupon_amount = $coupon->get_amount();

	$coupon_exp = $coupon->get_date_expires();
	$coupon_exp = $coupon_exp->date_i18n( 'Y-m-d' );

	$afgc_coupon_expiration_days = get_post_meta( $product_id, 'afgc_expiration_date', true );

	$afgc_order = wc_get_order( $afgc_order_id );

	$afgc_gift_card_status = get_post_meta( $post->ID, '_gift_card_status', true );

	$order_date_created = $afgc_order->get_date_created();

	$orderTotal = $afgc_order->get_item_subtotal( $item, false, true );

	$afgc_purchase_for = get_post_meta( $post->ID, '_purchase_for', true );

	$coupon_total_amount = floatval( get_post_meta( $coupon_id, 'coupon_total_amount', true ) );
	$last_usage_date     = '';
	if ( ! empty( $usage_of_coupon ) ) {
		usort( $usage_of_coupon, function ( $a, $b ) {
			return $b['date'] - $a['date']; // DESC (latest first)
		});

		$last_usage_date = gmdate( 'M-d-Y', $usage_of_coupon[0]['date'] );
	}
	?>
	<style type="text/css">
		.afgc-recp label{
				display: block;
			margin-bottom: 8px;
			font-weight: 600;
		}
		.afgc-recp div, .afgc-recp{
			margin-bottom: 15px;
		}
		.afgc-recp input, .afgc-recp textarea{
			background: no-repeat;
			border-radius: 0;
			max-width: 300px;
			min-width: 300px;
		}
		.afgc-log-table table{
			width: 100%;
			max-width: 500px;
		}
		.afgc-log-table table tr th,
		.afgc-log-table table tr td{
			text-align: left;
			padding:5px 15px;
		}
	</style>
	<div class="afgc-card-info">
		<div class="afgc-info-header">
			<title><?php echo esc_attr( $afgc_coupon_code ); ?></title>

			<?php if ( 'yes' == $is_afgc_virtual ) { ?>

				<span>( <?php echo esc_html__( 'Digital', 'addify_gift_cards' ); ?> )</span>

			<?php } else { ?> 

				<span>( <?php echo esc_html__( 'Physical', 'addify_gift_cards' ); ?> )</span>

			<?php } ?>

			<ul>

				<li class="afgc-delivered-status"><a href="#"><?php echo esc_attr( $afgc_gift_card_status ); ?></a></li>

			</ul>

			<label><?php echo esc_html__( ' Available Balance ', 'addify_gift_cards' ); ?></label>

			<h4><span><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span> <?php echo esc_attr($coupon_amount); ?></h4>
		</div>

		<div class="afgc-info-content">
			<div class="">
				<div class="afgc-dashboard-box">
					<div class="afgc_dashboard-item-content">
						<h4><?php echo esc_html__( 'Issued Date', 'addify_gift_cards' ); ?></h4>
						<p><?php echo esc_attr( date_format( $order_date_created, 'M-d-Y' ) ); ?></p>
					</div>
				</div>
				<div class="afgc-dashboard-box">
					<div class="afgc_dashboard-item-content">
						<h4><?php echo esc_html__( 'Issued Amount', 'addify_gift_cards' ); ?></h4>
						<p><span><?php echo esc_html( get_woocommerce_currency_symbol() ); ?></span><?php echo esc_attr( $coupon_total_amount ); ?></p>
					</div>
				</div>
				<div class="afgc-dashboard-box">
					<div class="afgc_dashboard-item-content">
						<h4><?php echo esc_html__( 'Purchased Order', 'addify_gift_cards' ); ?></h4>
						<p><a href="<?php echo esc_url( get_edit_post_link( $afgc_order_id ) ); ?>">#<?php echo esc_attr( $afgc_order_id ); ?></a></p>
					</div>
				</div>
				<div class="afgc-dashboard-box">
					<div class="afgc_dashboard-item-content">
						<h4><?php echo esc_html__( 'Redeemed Date', 'addify_gift_cards' ); ?></h4>
						<?php if ( ! empty( $last_usage_date  ) ) { ?>
							<p><?php echo esc_html( gmdate( 'M-d-Y', strtotime( $last_usage_date ) ) ); ?></p>
						<?php } ?>
					</div>
				</div>
			</div>

			<div class="afgc-recp">
				<div class="afgc-card-from-info">
					<label><?php echo esc_html__( 'From:', 'addify_gift_cards' ); ?></label>

					<?php if ( 'yes' == $is_afgc_virtual && isset($afgc_purchase_for['afgc_sender_name']) ) { ?>
						<input type="text" name="" value="<?php echo esc_attr( $afgc_purchase_for['afgc_sender_name'] ); ?>" readonly>
					<?php } else { ?> 
						
						<input type="text" name="" value="<?php echo esc_attr( $afgc_purchase_for['afgc_phy_gift_sender_name'] ); ?>" readonly>

					<?php } ?>

				</div>
				<div class="afgc-card-to-info">
					<label><?php echo esc_html__( 'To:', 'addify_gift_cards' ); ?></label>

					<?php if ( 'yes' == $is_afgc_virtual ) { ?>
						
						<ul>
							<?php

							if ( in_array( 'total_recipient_user_Select', array_keys( $afgc_purchase_for ), true ) ) {

								$total_recp = $afgc_purchase_for['total_recipient_user_Select'];

								$get_array_keys = array_keys( $afgc_purchase_for );

								for ( $i = 0; $i <= $total_recp; $i++ ) {

									if ( in_array( 'afgc_recipient_name' . $i, $get_array_keys, true ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_name' . $i ] ) && ! empty( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ) ) {

										?>
										<li><input type="email" name="" value="<?php echo esc_attr( $afgc_purchase_for[ 'afgc_recipient_name' . $i ] ); ?>" readonly></li>
										<li><input type="email" name="" value="<?php echo esc_attr( $afgc_purchase_for[ 'afgc_recipient_email' . $i ] ); ?>" readonly></li>

										<?php

									}
								}
							}

							?>
						</ul>

					<?php } else { ?>

						<input type="text" name="" value="<?php echo esc_attr( $afgc_purchase_for['afgc_phy_gift_recipient_name'] ); ?>" readonly>

					<?php } ?>
				</div>
			</div>

			<div class="afgc-recp">
				
				<?php if ( 'yes' == $is_afgc_virtual && isset($afgc_purchase_for['afgc_sender_message']) ) { ?>

					<label><?php echo esc_html__( 'Message:', 'addify_gift_cards' ); ?></label>

					<textarea readonly><?php echo esc_attr( $afgc_purchase_for['afgc_sender_message'] ); ?></textarea>

					<?php
				} elseif ( ! empty( $afgc_purchase_for['afgc_phy_gift_sender_message'] ) ) {

					?>
					<label><?php echo esc_html__( 'Message:', 'addify_gift_cards' ); ?></label>

					<textarea readonly><?php echo esc_attr( $afgc_purchase_for['afgc_phy_gift_sender_message'] ); ?></textarea>

					<?php
				}

				?>
			</div>

			<div class="afgc-recp">
				<div class="afgc-card-from-info">
					<label><?php echo esc_html__( 'Delivery Date:', 'addify_gift_cards' ); ?></label>

					<?php
					if ( 'yes' == $is_afgc_virtual ) {

						if ( '' == $afgc_purchase_for['afgc_delivery_date'] ) {
							?>

							<input type="text" name="" value="Now" readonly>

						<?php } else { ?>

							<input type="text" name="" value="<?php echo esc_attr( $afgc_purchase_for['afgc_delivery_date'] ); ?>" readonly>

							<?php
						}
					} else {
						?>
					 

						<input type="text" name="" value="<?php echo esc_attr( $afgc_purchase_for['afgc_phy_gift_delivery_date'] ); ?>" readonly>

										<?php } ?>

				</div>
				<div class="afgc-card-to-info">
					
					<label><?php echo esc_html__( 'Expiry:', 'addify_gift_cards' ); ?></label>
					<?php if ( $coupon_exp ) { ?>
					<input type="text" name="" value="<?php echo esc_attr( $coupon_exp ); ?>" readonly>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>



<?php if ( 'pending' != $afgc_gift_card_status ) { ?>

	<div class="afgc-log-table">
		<h3><?php echo esc_html__( 'Coupon Log', 'addify_gift_cards' ); ?></h3>
		<table>
			<thead>
				<tr>
					<th><?php echo esc_html__( 'Code', 'addify_gift_cards' ); ?></th>
					<th><?php echo esc_html__( 'Order', 'addify_gift_cards' ); ?></th>
					<th><?php echo esc_html__( 'Discount Amount', 'addify_gift_cards' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $usage_of_coupon as $coupon_usage ) : ?>
					<tr>
						<td><?php echo esc_attr( $afgc_coupon_code ); ?></td>
						<td><a href="<?php echo esc_url( get_edit_post_link( $coupon_usage['order_id'] ) ); ?>"> <font>#<?php echo esc_attr( $coupon_usage['order_id'] ); ?></font><?php echo esc_url( get_the_title( $coupon_usage['order_id'] ) ); ?></a></td>
						<td><span><?php echo wp_kses_post( wc_price( $coupon_usage['discount'] ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	
	<?php
}
