<?php

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

global $product, $post;

?>
	<section class="afgc-shordcodes-gifts-cards">
		<div class="woocommerce">
			<ul class="products columns-<?php echo esc_attr( wc_get_loop_prop( 'columns' ) ); ?>" >
			<?php
				$args = array(
					'post_type'      => 'product',
					'posts_per_page' => -1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'product_type',
							'field'    => 'slug',
							'terms'    => array( 'gift_card' ),
							'operator' => 'IN',
						),
					),
				);

				$loop = new WP_Query( $args );

				while ( $loop->have_posts() ) :
					$loop->the_post();
					global $product;
					wc_get_template_part( 'content', 'product' );
					?>
				<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
			</ul>
		</div>
	</section>

<?php


