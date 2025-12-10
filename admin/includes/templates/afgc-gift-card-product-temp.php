<?php
/**
 * Simple custom product
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product, $post;

do_action( 'gift_card_before_add_to_cart_form' );

?>

<form class="gift_card_cart" method="post" enctype='multipart/form-data'>

	<?php

		wp_nonce_field( 'afgc_nonce', 'afgc_nonce' );

		$afgc_another_recp = get_option( 'afgc_another_recp' );

		$is_afgc_virtual = get_post_meta( $product->get_id(), 'afgc_virtual', true );

		$afgc_another_recp_title = get_option( 'afgc_another_recp_title' );

		$afgc_email_checkbox_lable = get_option( 'afgc_email_checkbox_lable' );

		$afgc_print_home_checkbox_lable = get_option( 'afgc_print_home_checkbox_lable' );

		$afgc_card_gallery_section_title = get_option( 'afgc_card_gallery_section_title' );

		$afgc_delivery_section_title = get_option( 'afgc_delivery_section_title' );

		$afgc_enable_custom_image_btn = get_option( 'afgc_enable_custom_image_btn' );

		$gift_card_price = (int) get_post_meta( $product->get_id(), 'afgc_gift_card_amnt', true );

		$afgc_expiration_date = (int) get_post_meta( $product->get_id(), 'afgc_expiration_date', true );

	if ( empty( $gift_card_price ) ) {

		$gift_card_price = (int) get_post_meta( $product->get_id(), '_regular_price', true );

	}

		$afgc_recipient_info_section_title = get_option( 'afgc_recipient_info_section_title' );

		$afgc_special_discount_type = get_post_meta( $product->get_id(), 'afgc_special_discount_type', true );

		$discount_price = get_post_meta( $product->get_id(), 'afgc_special_discount_amount', true );

		$afgc_allow_custom_img = get_post_meta( $post->ID, 'afgc_allow_custom_img', true );
		$afgc_product_as_gift  = get_post_meta( $post->ID, 'afgc_product_as_gift', true );

		$afgc_enable_custom_image_btn = get_post_meta( $post->ID, 'afgc_enable_custom_image_btn', true );

		$afgc_allow_overide_custom_amount = get_post_meta( $post->ID, 'afgc_allow_overide_custom_amount', true );

		$afgc_get_cust_val = get_post_meta( $product->get_id(), 'afgc_gift_card_amnt', true );

		$afgc_allow_overide_min_custom_amount = (int) get_post_meta( $post->ID, 'afgc_allow_overide_min_custom_amount', true );

		// send as a product
		$gift_as_product_id = isset($_GET['gift_product']) ?  absint($_GET['gift_product']): '';
	?>
	<div class="afgc-gift-card-amount-box">
		
		<style>
			.afgc-main-form-preview-container{
				display: block !important;
			}

			.single-product div.product p.price{
				margin: 0 !important;
			}

		</style>

		<?php
		if ( 'yes' == $is_afgc_virtual ) {

			$afgc_enable_card_gallery = get_option( 'afgc_enable_card_gallery' );

			?>

			<div class="afgc-virtual-gift-card">

				<div class="afgc-virtual-gift-card-choose-image">

					<div class="afgc-section-label-custom-btn">

						<?php

						if ( 'yes' == $afgc_enable_card_gallery ) {

							if ( ! empty( $afgc_card_gallery_section_title ) ) {
								?>

									<h5> <?php echo esc_html( get_option( 'afgc_card_gallery_section_title' ) ); ?> </h5>
								
								<?php } else { ?>
							
									<h5> <?php echo esc_html__( 'Gift Card Gallery', 'addify_gift_cards' ); ?> </h5>

								<?php } ?>

							<?php } ?>

						<?php
						if ( 'yes' == $afgc_allow_custom_img ) {
							if ( 'yes' !== $afgc_enable_card_gallery ) {
								?>
							<div class="afgc_custom-image_allow_main">
							<?php } ?>
								<div class="afgc-upload-img">

									<?php if ( ! empty( $afgc_enable_custom_image_btn ) ) { ?>
										
										<label for="afgc_upload_img_btn"><?php echo esc_attr( $afgc_enable_custom_image_btn ); ?></label>

									<?php } else { ?>

										<label for="afgc_upload_img_btn"><?php echo esc_html__( 'Upload Card', 'addify_gift_cards' ); ?></label>

									<?php } ?>

									<input type="file" class="afgc-upload-img-btn" name="afgc_upload_img_btn" id="afgc_upload_img_btn" value="Choose" accept=".png, .gif, .jpeg, .jpg">
									<input type="hidden" name="afgc_custom_image" class="afgc_selected_img" value="">

								</div>
								<div class="afgc_popup_text" id="afgc_image_popup"><?php echo esc_html__( 'Please upload an image in png, gif, jpeg, or jpg format with a maximum file size of 2MB.', 'addify_gift_cards' ); ?></div>
							<?php if ( 'yes' !== $afgc_enable_card_gallery ) { ?>
							</div>
							<?php } ?>

						<?php } ?>

					</div>


					<?php if ( 'yes' == $afgc_enable_card_gallery ) { ?>

						<div class="afgc-choose-image">
							<ul>
								
								<?php if ( has_post_thumbnail() ) { ?>

									<li class="afgc-choose-image-item">

										<?php the_post_thumbnail(); ?>

									</li>

									<?php
								}


								global $product;

								$attachment_ids = $product->get_gallery_image_ids();

								$img_url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );

								foreach ( $attachment_ids as $attachment_id ) {

									?>

								<li class="afgc-gift-pro-gall-img" id="gall">

									<?php echo wp_get_attachment_image( $attachment_id, 'full' ); ?>

								</li>
								  
									<?php
								}

								$product_categories = wp_get_post_terms( $product->get_id(), 'afgc_gallery_cat' );

								if ( $product_categories ) {

									?>

								<li class="afgc-choose-image-item afgc-view">

									<a href="#" id="afgc_view_all_card"><?php echo esc_html__( 'View All ', 'addify_gift_cards' ); ?></a>

								</li>

								<?php } ?>

							</ul>

							<input type="hidden" name="afgc_selected_img" class="afgc_selected_img" value="<?php echo esc_url( $img_url ); ?>">

						</div>

					<?php } ?>

				</div>
				<?php if ('yes' == $afgc_product_as_gift && empty($gift_as_product_id)) { ?>
				<div class="afgc-gift-type-wrap" id="afgc_gift_type_wrap">
					<h5><?php echo esc_html__( 'Select gift type', 'addify_gift_cards' ); ?></h5>
					<select class="afgc-gift-type-select" name="afgc_gift_type">
						<option value="gift-card"><?php echo esc_html__( 'Gift Card', 'addify_gift_cards' ); ?></option>
						<option value="product-gift"><?php echo esc_html__( 'Product Gift', 'addify_gift_cards' ); ?></option>
					</select>
					<div class="agf-selected-product">
					<h5><?php echo esc_html__( 'Select Product', 'addify_gift_cards' ); ?></h5>
					<select name="afgc_gift_products" id="afgc_gift_products" class="product_search" style="margin-bottom: 15px;">
					</select>
					</div>
				</div>
				<?php
				} elseif (!empty($gift_as_product_id) && 0 != $gift_as_product_id ) {
					$product_as_gift = wc_get_product( $gift_as_product_id );
					if ( $product_as_gift ) {
						$product_name  = $product_as_gift->get_name();
						$product_price = wc_price( $product_as_gift->get_price() );
						$product_link  = get_permalink( $product_as_gift->get_id() );
						$product_thumb = $product_as_gift->get_image( 'thumbnail' );
						$variation     = '';

						if ( $product_as_gift->is_type( 'variation' ) ) {
							$variation_data = $product_as_gift->get_variation_attributes();
							if ( ! empty( $variation_data ) ) {
								$variation = wc_get_formatted_variation( $variation_data, true );
							}
						}

						$gift_card_price = $product_as_gift->get_price();

						?>
						<div class="afgc-product-as-gift-wrap">
							<h5><?php echo esc_html__( 'Product Selected as Gift', 'addify_gift_cards' ); ?></h5>

							<div class="afg-product-detail-wrap">
								<div class="afg-product-information">
									<div class="afg-product-thumbnail">
										<a href="<?php echo esc_url( $product_link ); ?>">
											<?php echo wp_kses_post($product_thumb); ?>
										</a>
									</div>

									<div class="afg-product-content">
										<h6>
											<a href="<?php echo esc_url( $product_link ); ?>">
												<?php echo esc_html( $product_name ); ?>
											</a>
										</h6>

										<?php if ( ! empty( $variation ) ) : ?>
											<span class="selected-variation"><?php echo wp_kses_post( $variation ); ?></span>
										<?php endif; ?>

										<p class="price"><?php echo wp_kses_post( $product_price ); ?></p>
										<input type="hidden" name="afgc_gift_products" id="gift-card-as-product-handle" value="<?php echo esc_attr($gift_as_product_id); ?>">
									</div>
								</div>
								<div class="afg-change">
									<a href="#" class="afg-change-link"><?php echo esc_html__( 'Change', 'addify_gift_cards' ); ?></a>
								</div>
							</div>
						</div>
						<?php
					}

				}

				$afgc_get_cust_amnts = explode( ',', $afgc_get_cust_val );
				if (( 'yes' == $afgc_allow_overide_custom_amount || count($afgc_get_cust_amnts) > 1 ) && empty($gift_as_product_id)) {
					?>
						
						<div class="afgc-virtual-custom-amount" id="afgc_virtual_custom_amount">
					
							<h5><?php echo esc_html__( 'Set an amount', 'addify_gift_cards' ); ?></h5>

							<ul class="afgc-enter-custom-amount">
								<?php

								if ( 'yes' == $afgc_allow_overide_custom_amount && $afgc_allow_overide_min_custom_amount ) {

									$afgc_min_custom_amount = $afgc_allow_overide_min_custom_amount;

								} else {

									$afgc_min_custom_amount = 1;

								}

								$afgc_allow_overide_max_custom_amount = (int) get_post_meta( $post->ID, 'afgc_allow_overide_max_custom_amount', true );

								if ( 'yes' == $afgc_allow_overide_custom_amount && $afgc_allow_overide_max_custom_amount ) {

									$afgc_max_custom_amount = $afgc_allow_overide_max_custom_amount;

								} else {

									$afgc_max_custom_amount = 5000;

								}
								
								$afgc_get_cust_amnts = explode( ',', $afgc_get_cust_val );
								if (count($afgc_get_cust_amnts) > 1) {
									foreach ( $afgc_get_cust_amnts as $afgc_get_cust_amnt ) {
										?>
										<li>
											<label class="afgc-option-radio">
												<input type="radio" name="afgc_admin_set_price" value="<?php echo esc_attr($afgc_get_cust_amnt); ?>" data-price="<?php echo esc_attr($afgc_get_cust_amnt); ?>" data-wc-price="<?php echo esc_attr($afgc_get_cust_amnt); ?>">
												<?php echo wp_kses_post(wc_price($afgc_get_cust_amnt)); ?>
											</label>

											<input type="hidden" value="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>" data-price="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>" data-wc-price="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>"
											>
											</li>
										<?php
									}
								}
								if ( 'yes' == $afgc_allow_overide_custom_amount ) {
									$currency_symbol = get_woocommerce_currency_symbol();
									?>

									<li>
										<input type="number" min="<?php echo esc_attr( $afgc_min_custom_amount ); ?>" max="<?php echo esc_attr( $afgc_max_custom_amount ); ?>" name="afgc_virtual_custom_amount" id="afgc_virtual_custom_amount" placeholder="<?php echo esc_html__( 'Other', 'addify_gift_cards' ); ?>" class="afgc_virtual_custom_amount_opt">
									</li>

								<?php } ?>

							</ul>
							
						</div>

				<?php } ?>
				<input type="hidden" name="final_selected_price_of_gift_card" value="<?php echo esc_attr( $gift_card_price ); ?>">

				<!-- Auto-set delivery to Print at home (PDF) -->
				<input type="hidden" name="afgc_pdf_tab" value="yes">

				<div class="afgc-recipient-info" id="afgc-recipient-info">

					<?php
					// wp_nonce_field( 'addify_agf_nonce', 'afgc_nonce' );
					?>

					<?php if ( '' == $afgc_recipient_info_section_title ) { ?>

						<h5><?php echo esc_html__( 'Recipient Info', 'addify_gift_cards' ); ?></h5>

					<?php } else { ?>

						<h5><?php echo esc_html( get_option( 'afgc_recipient_info_section_title' ) ); ?></h5>

						<?php
					}

					$total_fields = 1;

					$afgc_recipient_name = get_post_meta( $product->get_id(), 'afgc_recipient_name', true );

					$afgc_recipient_email = get_post_meta( $product->get_id(), 'afgc_recipient_email', true );

					$afgc_name_input_is_required = 'yes' === get_option( 'afgc_name_input_is_required' ) ? 'required' : '';

					$afgc_email_input_is_required = 'yes' === get_option( 'afgc_email_input_is_required' ) ? 'required' : '';

					?>

					<div id="afgc_items_for_clone" class="afgc_items_cl">
						<span class="afgc-delete-field"><?php echo esc_html__( 'x', 'addify_gift_cards' ); ?></span>
						<div class="afgc-form-group">
							<label for="afgc_recipient_name"><?php echo esc_html__( 'Name:', 'addify_gift_cards' ); ?></label>
							<input type="text" name="afgc_recipient_name<?php echo esc_attr( $total_fields ); ?>" id="afgc_recipient_name" placeholder="Enter the recipient's name" class="afgc-frnt-input-fld afgc-name-input-field afgc-total-name-fields" <?php echo esc_attr( $afgc_name_input_is_required ); ?>>
						</div>

						<div class="afgc-form-group">
							<label for="afgc_recipient_email"><?php echo esc_html__( 'Email:', 'addify_gift_cards' ); ?></label>
							<input type="email" name="afgc_recipient_email<?php echo esc_attr( $total_fields ); ?>" id="afgc_recipient_email" placeholder="Enter the recipient's email" class="afgc-frnt-input-fld afgc-email-input-field afgc-total-email-fields" <?php echo esc_attr( $afgc_email_input_is_required ); ?> >
						</div>
					</div>

					<?php

					if ( 'yes' == $afgc_another_recp ) {

						if ( '' == $afgc_another_recp_title ) {

							?>

							<a href="#" id="afgc_clone_btn" class="afgc-clone-btn"><?php echo esc_html__( 'Add another recipient', 'addify_gift_cards' ); ?></a>

						<?php } else { ?> 

							<a href="#" id="afgc_clone_btn" class="afgc-clone-btn"><?php echo esc_attr( $afgc_another_recp_title ); ?></a>

						<?php } ?>
							
					<?php } ?>

					<input type="hidden" name="total_input_field_admin_select" value="<?php echo esc_attr( $total_fields ); ?>">
					<input type="hidden" name="current_product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">

					<div class="afgc-form-group">

						
						<label for="afgc_delivery_date"><?php echo esc_html__( 'Delivery date:', 'addify_gift_cards' ); ?></label>
						<input type="text" min="<?php echo esc_attr( gmdate( 'Y-m-d' ) ); ?>" name="afgc_delivery_date" onfocus="(this.type='date')"  id="afgc_delivery_date" placeholder="<?php echo esc_html__( 'Now', 'addify_gift_cards' ); ?>" class="afgc-frnt-input-fld" date-virtualSelected_date="<?php echo esc_attr($afgc_expiration_date); ?>">
					</div>
				</div>

				<?php

				$afgc_sender_name = get_option( 'afgc_sender_name' );

				if ( 'yes' == $afgc_sender_name ) {
					?>

					<div class="afgc-sender-info">
						<?php

						$afgc_sender_info_section_title = get_option( 'afgc_sender_info_section_title' );

						if ( '' == $afgc_sender_info_section_title ) {
							?>
								
							<h5><?php echo esc_html__( 'Sender Info', 'addify_gift_cards' ); ?></h5>

						<?php } else { ?>

							<h5><?php echo esc_html( get_option( 'afgc_sender_info_section_title' ) ); ?></h5>

							<?php
						}



						$afgc_sender_message_is_required = 'yes' === get_option( 'afgc_sender_message_is_required' ) ? 'required' : '';

						?>

						<div class="afgc-form-group">
							<label for="afgc_sender_name"><?php echo esc_html__( 'Name:', 'addify_gift_cards' ); ?></label>
							<input type="text" name="afgc_sender_name" id="afgc_sender_name" placeholder="Enter the sender's name" class="afgc-frnt-input-fld">
						</div>

						<div class="afgc-form-group">
							<label for="afgc_sender_message"><?php echo esc_html__( 'Message:', 'addify_gift_cards' ); ?></label>
							<textarea name="afgc_sender_message" id="afgc_sender_message" class="afgc-frnt-txtarea-fld" rows="5" placeholder="<?php echo esc_html__( 'Enter a message for the recipient', 'addify_gift_cards' ); ?>" <?php echo esc_attr( $afgc_sender_message_is_required ); ?>></textarea>
						</div>

					</div>

				<?php } ?>
			</div>

			<div class="afgc-main-form-preview-container">

				<?php

					global $product;

					$afgc_sender_name = get_post_meta( $product->get_id(), 'afgc_sender_name', true );

					$afgc_recipient_name = get_post_meta( $product->get_id(), 'afgc_recipient_name', true );

					$afgc_recipient_email = get_post_meta( $product->get_id(), 'afgc_recipient_email', true );

					$enable_sender = get_option( 'afgc_sender_name' );

				?>
				<div>
					<h3><?php echo esc_html__( 'Virtual Gift Card', 'addify_gift_cards' ); ?></h3>
					<ul>
						<?php if ( 'yes' == $enable_sender ) { ?>
						<li class="afgc-sender-name">
							<label><?php echo esc_html__( 'From:', 'addify_gift_cards' ); ?></label>
							<span><?php echo esc_attr( $afgc_sender_name ); ?></span>
						</li>
					<?php } ?>
						<li class="afgc-recip-name">
							<label><?php echo esc_html__( 'To:', 'addify_gift_cards' ); ?> </label><span><?php echo esc_attr( $afgc_recipient_name ); ?></span>
						</li>
						<li class="afgc-recip-email">
							<label><?php echo esc_html__( 'Email:', 'addify_gift_cards' ); ?> </label><span><?php echo esc_attr( $afgc_recipient_email ); ?></span>
						</li>
					</ul>
				</div>
			</div>

		<?php } else { ?>

			<div class="afgc-physical-gift-card">

				<?php

				$afgc_sender_recipent_name = get_option( 'afgc_sender_recipent_name' );

				$afgc_sender_info_section_title = get_option( 'afgc_physical_section_title' );

				$afgc_printed_message = get_option( 'afgc_printed_message' );

				$afgc_allow_overide_custom_amount = get_post_meta( $post->ID, 'afgc_allow_overide_custom_amount', true );

				$afgc_allow_overide_min_custom_amount = (int) get_post_meta( $post->ID, 'afgc_allow_overide_min_custom_amount', true );
				$afgc_get_cust_amnts                  = explode( ',', $afgc_get_cust_val );
				if ( 'yes' == $afgc_allow_overide_custom_amount || count($afgc_get_cust_amnts) > 1 ) {
					?>

					<div class="afgc-physical-custom-amount" id="afgc_physical_custom_amount">
						<h5><?php echo esc_html__( 'Set an amount', 'addify_gift_cards' ); ?></h5>
						<ul class="afgc-enter-custom-amount">
							<?php

							if ( 'yes' == $afgc_allow_overide_custom_amount && $afgc_allow_overide_min_custom_amount ) {

								$afgc_min_custom_amount = $afgc_allow_overide_min_custom_amount;

							} else {

								$afgc_min_custom_amount = 1;

							}

							$afgc_allow_overide_max_custom_amount = (int) get_post_meta( $post->ID, 'afgc_allow_overide_max_custom_amount', true );

							if ( 'yes' == $afgc_allow_overide_custom_amount && $afgc_allow_overide_max_custom_amount ) {

								$afgc_max_custom_amount = $afgc_allow_overide_max_custom_amount;

							} else {

								$afgc_max_custom_amount = 5000;

							}

							$afgc_get_cust_amnts = explode( ',', $afgc_get_cust_val );

							if (count($afgc_get_cust_amnts) > 1) {

								foreach ( $afgc_get_cust_amnts as $afgc_get_cust_amnt ) {

									?>
									<li>
										<label class="afgc-option-radio">
											<input type="radio" name="afgc_admin_set_price" value="<?php echo esc_attr($afgc_get_cust_amnt); ?>" data-price="<?php echo esc_attr($afgc_get_cust_amnt); ?>" data-wc-price="<?php echo esc_attr($afgc_get_cust_amnt); ?>">
											<?php echo wp_kses_post(wc_price($afgc_get_cust_amnt)); ?>
										</label>

										<input type="hidden" value="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>" data-price="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>" data-wc-price="<?php echo esc_attr( $afgc_get_cust_amnt ); ?>">
									</li>
									<?php
								}
							}
							if ( 'yes' == $afgc_allow_overide_custom_amount ) {

								?>

								<li>
									<input type="number" min="<?php echo esc_attr( $afgc_min_custom_amount ); ?>" max="<?php echo esc_attr( $afgc_max_custom_amount ); ?>" name="afgc_virtual_custom_amount" id="afgc_virtual_custom_amount" placeholder="<?php echo esc_html__( 'Other', 'addify_gift_cards' ); ?>" class="afgc_virtual_custom_amount_opt">
									
								</li>

							<?php } ?>
						</ul>
						
					</div>
					
					<?php
				}
				?>
				<input type="hidden" name="final_selected_price_of_gift_card" value="<?php echo esc_attr( $gift_card_price ); ?>">
					<div class="afgc-recipient-info" id="afgc-recipient-info" style="margin-top: 15px;">
						<?php

						if ( 'yes' == $afgc_sender_recipent_name ) {

							if ( '' == $afgc_sender_info_section_title ) {
								?>
								
								<h5><?php echo esc_html__( 'Delivery Info', 'addify_gift_cards' ); ?></h5>

							<?php } else { ?>

								<h5><?php echo esc_html( get_option( 'afgc_physical_section_title' ) ); ?></h5>

							<?php } ?>

							<div class="afgc-form-group">
								<label for="afgc_phy_gift_recipient_name"><?php echo esc_html__( 'Recipient Name:', 'addify_gift_cards' ); ?></label>
								<input type="text" name="afgc_phy_gift_recipient_name" id="afgc_phy_gift_recipient_name" placeholder="Enter recipient's name" class="afgc-frnt-input-fld afgc-name-input-field afgc-total-email-fields">
							</div>

							<div class="afgc-form-group">
								<label for="afgc_phy_gift_sender_name"><?php echo esc_html__( 'Sender Name:', 'addify_gift_cards' ); ?></label>
								<input type="text" name="afgc_phy_gift_sender_name" id="afgc_phy_gift_sender_name" placeholder="Enter sender's name" class="afgc-frnt-input-fld">
							</div>

							<?php
						}
						?>
						<div class="afgc-form-group">
							<label for="afgc_phy_gift_delivery_date"><?php echo esc_html__( 'Delivery date:', 'addify_gift_cards' ); ?></label>
							<input type="date" name="afgc_phy_gift_delivery_date" id="afgc_phy_gift_delivery_date" placeholder="<?php echo esc_html__( 'Today', 'addify_gift_cards' ); ?>" class="afgc-frnt-input-fld" date-selected_date="<?php echo esc_attr($afgc_expiration_date); ?>" required>
						</div>
						<?php

						if ( 'yes' == $afgc_printed_message ) {

							?>

							<div class="afgc-form-group">
								<label for="afgc_phy_gift_sender_message"><?php echo esc_html__( 'Message:', 'addify_gift_cards' ); ?></label>
								<textarea name="afgc_phy_gift_sender_message" id="afgc_phy_gift_sender_message" class="afgc-frnt-txtarea-fld" rows="5" placeholder="<?php echo esc_html__( 'Enter message for recipient', 'addify_gift_cards' ); ?>"></textarea>
							</div>

							<?php
						}

						?>

					</div>

				
			</div>

		<?php } ?>

	</div>
	<?php if (empty($gift_as_product_id)) { ?>
	<button type="button" id="preview_button" class="afgc_preview_btn button wp-element-button">
		<?php echo esc_html__('Preview', 'addify_gift_cards'); ?>
	</button>
	<?php } ?>
	<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt afgc_submit_btn wp-element-button"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
</form>
<?php
$afgc_enable_store_logo_pdf = get_option( 'afgc_enable_store_logo_pdf' );
$afgc_store_logo            = get_option( 'afgc_store_logo' ); 
$afgc_enable_store_name_pdf = get_option( 'afgc_enable_store_name_pdf' );
$afgc_store_name_pdf        = get_option( 'afgc_store_name_pdf' );
$afgc_gift_card_message     = get_option('afgc_gift_card_message');
$afgc_enable_disclaimer     = get_option('afgc_enable_disclaimer');
$afgc_disclaimer_text       = get_option('afgc_disclaimer_text');
?>
<div id="afgc_preview_popup" class="afgc_preview_popup">
	<div class="afgc_popup_content">
		<span id="afgc_close_popup" class="afgc_close_popup">&times;</span>
		
		<div class="afgc_preview_container">
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
				<?php
				if ( 'yes' == $afgc_enable_store_name_pdf ) {
					?>
				<h3 class="preview-stor-name">
					<?php echo esc_attr( $afgc_store_name_pdf ); ?>
				</h3>
				<?php } ?>
				<h2><?php echo esc_html__('Dear', 'addify_gift_cards'); ?> <span id="afgc_recipient_name_pre"></span></h2>
				<h3><?php echo esc_html__('You have received a gift card worth ', 'addify_gift_cards') . esc_html(get_woocommerce_currency_symbol() ); ?><span id="afgc_selected_price"></span> <?php echo esc_html__('from', 'addify_gift_cards'); ?> <span id="afgc_pre_sender_name"></span></h3>
				<p class="afg-preview-sender-message"></p>
			</div>
			<div class="afd-preview-product-name-wrap">
				<strong><?php echo esc_html__('Name', 'addify_gift_cards'); ?>:</strong><span class="afd-preview-product-name"><?php the_title(); ?></span>
			</div>
			<div class="afd-preview-expire-data-wrap">
				<strong><?php echo esc_html__('Expiry Date', 'addify_gift_cards'); ?>:</strong>
				<?php
				$afgc_expiration_days = get_post_meta( $product->get_id(), 'afgc_expiration_date', true );

				if ( ! empty( $afgc_expiration_days ) ) {

					$afgc_expiration_date = gmdate( 'Y-m-d', strtotime( '+' . $afgc_expiration_days . 'days' ) );

					$afgc_date_format_option = !empty(get_option('afgc_date_format')) ? get_option('afgc_date_format') : 'M-d-Y';
					
					$expiration_date_object = new DateTime( $afgc_expiration_date );
					?>
					<span class="afd-preview-expire-data"><?php echo esc_html($expiration_date_object->format($afgc_date_format_option )); ?></span>
					<?php
				}
				?>

			</div>
			<div class="coupon-code-preiew">
				<button type="button"><?php echo esc_html__('GIFT CARD CODE', 'addify_gift_cards'); ?></button>
			</div>
			<div class="afgc_preview_images">
				<div class="afgc_selected_image"></div>
				<div class="afgc_gallery_images"></div>
			</div>
			<?php if (!empty($afgc_gift_card_message)) { ?>
				<p><?php echo esc_html($afgc_gift_card_message); ?></p>
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
</div>
<?php do_action( 'gift_card_after_add_to_cart_form' ); ?>
