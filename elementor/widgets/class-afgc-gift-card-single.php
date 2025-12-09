<?php
/**
 * Elementor Single Gift Card Widget
 *
 * @package Addify Gift Card
 * @since 1.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Single Gift Card Widget
 */
class AFGC_Elementor_Widget_Gift_Card_Single extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-gift-card-single';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Single Gift Card', 'addify_giftcard' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-product-add-to-cart';
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
		return array( 'gift', 'card', 'product', 'single', 'form' );
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

		// Get all gift card products.
		$gift_card_products = $this->get_gift_card_products();

		$this->add_control(
			'product_id',
			array(
				'label'       => esc_html__( 'Select Gift Card Product', 'addify_giftcard' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'options'     => $gift_card_products,
				'default'     => ! empty( $gift_card_products ) ? key( $gift_card_products ) : '',
				'label_block' => true,
			)
		);

		$this->add_control(
			'show_image',
			array(
				'label'        => esc_html__( 'Show Product Image', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_title',
			array(
				'label'        => esc_html__( 'Show Product Title', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_price',
			array(
				'label'        => esc_html__( 'Show Price', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_description',
			array(
				'label'        => esc_html__( 'Show Description', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$this->add_control(
			'show_gallery',
			array(
				'label'        => esc_html__( 'Show Image Gallery', 'addify_giftcard' ),
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

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-single-product-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-single-product-title',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Price Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-single-product-price' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get all gift card products
	 *
	 * @return array
	 */
	private function get_gift_card_products() {
		$products = array();

		$args = array(
			'post_type'      => 'product',
			'posts_per_page' => -1,
			'tax_query'      => array(
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => 'gift_card',
				),
			),
		);

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$products[ get_the_ID() ] = get_the_title();
			}
			wp_reset_postdata();
		}

		return $products;
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( empty( $settings['product_id'] ) ) {
			echo '<p>' . esc_html__( 'Please select a gift card product.', 'addify_giftcard' ) . '</p>';
			return;
		}

		$product_id = intval( $settings['product_id'] );
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			echo '<p>' . esc_html__( 'Product not found.', 'addify_giftcard' ) . '</p>';
			return;
		}

		// Set global product for template compatibility.
		global $product, $post;
		$post = get_post( $product_id );
		setup_postdata( $post );

		echo '<div class="afgc-single-gift-card-widget">';

		// Product image.
		if ( 'yes' === $settings['show_image'] ) {
			echo '<div class="afgc-single-product-image">';
			echo wp_kses_post( $product->get_image( 'large' ) );
			echo '</div>';
		}

		// Product title.
		if ( 'yes' === $settings['show_title'] ) {
			echo '<h2 class="afgc-single-product-title">' . esc_html( $product->get_name() ) . '</h2>';
		}

		// Product price.
		if ( 'yes' === $settings['show_price'] ) {
			echo '<div class="afgc-single-product-price">' . wp_kses_post( $product->get_price_html() ) . '</div>';
		}

		// Product description.
		if ( 'yes' === $settings['show_description'] ) {
			echo '<div class="afgc-single-product-description">' . wp_kses_post( wpautop( $product->get_description() ) ) . '</div>';
		}

		// Include the gift card form template.
		if ( file_exists( AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-gift-card-product-temp.php' ) ) {
			// Enqueue required scripts.
			wp_enqueue_script( 'afgc_front_script' );
			wp_enqueue_style( 'afgc_front_style' );

			// Hide gallery if setting is off.
			if ( 'yes' !== $settings['show_gallery'] ) {
				echo '<style>.afgc-virtual-gift-card-choose-image { display: none !important; }</style>';
			}

			include AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-gift-card-product-temp.php';
		}

		echo '</div>';

		wp_reset_postdata();
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<div class="afgc-single-gift-card-widget">
			<p><?php echo esc_html__( 'Single gift card will be displayed here. Preview is not available in the editor.', 'addify_giftcard' ); ?></p>
		</div>
		<?php
	}
}
