<?php

$afgc_gift_card_terms = get_terms(
	array(
		'taxonomy'   => 'afgc_gallery_cat',
		'hide_empty' => false,

	)
);

?>

<div class="afgc-choose-image-wrapper">
	<div class="afgc-choose-image-modal">
		<div class="afgc-choose-image-modal-header">
			<button class="afgc-choose-image-modal-close" id="afgc_choose_image_modal_close">x</button>
		</div>
		<div class="afgc-choose-image-content">
			
			<div class="afgc-tax-tabs-menu">
				<h3><?php echo esc_html__( 'Categories', 'addify_gift_cards' ); ?></h3>
				<ul class="afgc-tab-nav" id="tabs-nav">

					<?php foreach ( $afgc_gift_card_terms as $afgc_gift_card_term ) { ?>

						<li>
							<a href="#<?php echo esc_attr( $afgc_gift_card_term->slug ); ?>" data-toggle="tab"><?php echo esc_attr( $afgc_gift_card_term->name ); ?></a>
						</li>

					<?php } ?>

				</ul>
			</div>

			<div class="afgc-tax-tabs-content">

				<?php

				if ( $afgc_gift_card_terms ) {

					foreach ( $afgc_gift_card_terms as $afgc_gift_card_term ) {
						?>

						<div class="tab-content" id="<?php echo esc_attr( $afgc_gift_card_term->slug ); ?>">

							<?php
							$thumbnail_ids = (array) get_term_meta( $afgc_gift_card_term->term_id, 'product_cat_thumbnail_id', true );
							?>
							
							<h3><?php echo esc_attr( $afgc_gift_card_term->name ); ?></h3>

							<ul class="afgc-tax-gallery">
								<?php

								foreach ( $thumbnail_ids as $thumbnail_id ) {

									if ( ! empty( wp_get_attachment_thumb_url( $thumbnail_id ) ) ) {
										?>

										<li class="afgc-tax-gallery-item">

											<input type="hidden" name="product_cat_thumbnail_id[]" value="<?php echo esc_attr( $thumbnail_id ); ?>">

											<img data-src="<?php echo esc_url( wp_get_attachment_image_url( $thumbnail_id, 'full' ) ); ?>" src="<?php echo esc_url( wp_get_attachment_thumb_url( $thumbnail_id ) ); ?>">

										</li>

										<?php

									}
								}

								?>
							</ul>
						</div>

					<?php } ?>

				<?php } else { ?> 

					<h3><?php echo esc_html__( 'Nothing Found', 'addify_gift_cards' ); ?></h3>

					<p><?php echo esc_html__( 'No Gift Card Category Found.', 'addify_gift_cards' ); ?></p>

				<?php } ?>

			</div>
		</div>
	</div>
</div>
