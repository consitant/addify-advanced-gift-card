<?php
/**
 * Elementor Gift Cards Grid Widget
 *
 * @package Addify Gift Card
 * @since 1.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gift Cards Grid Widget
 */
class AFGC_Elementor_Widget_Gift_Cards_Grid extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-gift-cards-grid';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Gift Cards Grid', 'addify_giftcard' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-products';
	}

	/**
	 * Get widget categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'afgc-gift-cards' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'gift', 'card', 'woocommerce', 'products', 'shop' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {

		// Content Tab.
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '3',
				'options' => array(
					'1' => esc_html__( '1', 'addify_giftcard' ),
					'2' => esc_html__( '2', 'addify_giftcard' ),
					'3' => esc_html__( '3', 'addify_giftcard' ),
					'4' => esc_html__( '4', 'addify_giftcard' ),
					'5' => esc_html__( '5', 'addify_giftcard' ),
					'6' => esc_html__( '6', 'addify_giftcard' ),
				),
			)
		);

		$this->add_control(
			'posts_per_page',
			array(
				'label'   => esc_html__( 'Products Per Page', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 12,
				'min'     => 1,
				'max'     => 100,
				'step'    => 1,
			)
		);

		$this->add_control(
			'orderby',
			array(
				'label'   => esc_html__( 'Order By', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'date',
				'options' => array(
					'date'       => esc_html__( 'Date', 'addify_giftcard' ),
					'title'      => esc_html__( 'Title', 'addify_giftcard' ),
					'price'      => esc_html__( 'Price', 'addify_giftcard' ),
					'popularity' => esc_html__( 'Popularity', 'addify_giftcard' ),
					'rating'     => esc_html__( 'Rating', 'addify_giftcard' ),
					'rand'       => esc_html__( 'Random', 'addify_giftcard' ),
				),
			)
		);

		$this->add_control(
			'order',
			array(
				'label'   => esc_html__( 'Order', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'desc',
				'options' => array(
					'asc'  => esc_html__( 'ASC', 'addify_giftcard' ),
					'desc' => esc_html__( 'DESC', 'addify_giftcard' ),
				),
			)
		);

		$this->add_control(
			'hide_out_of_stock',
			array(
				'label'        => esc_html__( 'Hide Out of Stock', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'show_pagination',
			array(
				'label'        => esc_html__( 'Show Pagination', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();

		// Style Tab.
		$this->start_controls_section(
			'section_style',
			array(
				'label' => esc_html__( 'Style', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'column_gap',
			array(
				'label'     => esc_html__( 'Column Gap', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .products' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'row_gap',
			array(
				'label'     => esc_html__( 'Row Gap', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .products' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		// Get current page for pagination.
		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		// Setup query args.
		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => intval( $settings['posts_per_page'] ),
			'paged'          => $paged,
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'gift_card',
				),
			),
		);

		// Order by.
		if ( 'price' === $settings['orderby'] ) {
			$args['meta_key'] = '_price';
			$args['orderby']  = 'meta_value_num';
		} elseif ( 'popularity' === $settings['orderby'] ) {
			$args['meta_key'] = 'total_sales';
			$args['orderby']  = 'meta_value_num';
		} elseif ( 'rating' === $settings['orderby'] ) {
			$args['meta_key'] = '_wc_average_rating';
			$args['orderby']  = 'meta_value_num';
		} else {
			$args['orderby'] = $settings['orderby'];
		}

		$args['order'] = $settings['order'];

		// Hide out of stock products.
		if ( 'yes' === $settings['hide_out_of_stock'] ) {
			$args['meta_query'] = array(
				array(
					'key'     => '_stock_status',
					'value'   => 'instock',
					'compare' => '=',
				),
			);
		}

		// Query products.
		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {

			// Start output buffering.
			ob_start();

			// Set woocommerce loop columns.
			add_filter( 'loop_shop_columns', function() use ( $settings ) {
				return intval( $settings['columns'] );
			});

			// WooCommerce loop start.
			woocommerce_product_loop_start();

			while ( $query->have_posts() ) {
				$query->the_post();
				wc_get_template_part( 'content', 'product' );
			}

			// WooCommerce loop end.
			woocommerce_product_loop_end();

			// Pagination.
			if ( 'yes' === $settings['show_pagination'] && $query->max_num_pages > 1 ) {
				echo '<div class="afgc-pagination">';
				echo paginate_links(
					array(
						'total'   => $query->max_num_pages,
						'current' => $paged,
						'type'    => 'list',
					)
				);
				echo '</div>';
			}

			// Reset post data.
			wp_reset_postdata();

			// Get output.
			$output = ob_get_clean();

			echo '<div class="afgc-gift-cards-grid">';
			echo wp_kses_post( $output );
			echo '</div>';

		} else {
			echo '<p>' . esc_html__( 'No gift cards found.', 'addify_giftcard' ) . '</p>';
		}
	}

	/**
	 * Render widget output in the editor (for preview)
	 */
	protected function content_template() {
		?>
		<# if ( settings.columns ) { #>
			<div class="afgc-gift-cards-grid">
				<p><?php echo esc_html__( 'Gift cards will be displayed here.', 'addify_giftcard' ); ?></p>
			</div>
		<# } #>
		<?php
	}
}
