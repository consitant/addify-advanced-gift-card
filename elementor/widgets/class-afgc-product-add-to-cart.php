<?php
/**
 * Elementor Gift Card Add to Cart Widget
 *
 * Dynamic widget for Elementor Single Product Page templates
 * Displays the add to cart button for gift card products
 *
 * @package Addify Gift Card
 * @since 1.7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gift Card Add to Cart Widget for Elementor Theme Builder
 */
class AFGC_Elementor_Widget_Product_Add_To_Cart extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-product-add-to-cart';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Gift Card Add to Cart', 'addify_giftcard' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-cart';
	}

	/**
	 * Get widget categories
	 *
	 * @return array
	 */
	public function get_categories() {
		return array( 'afgc-gift-cards', 'woocommerce-elements' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'gift', 'card', 'cart', 'add', 'buy', 'purchase', 'button' );
	}

	/**
	 * Get script dependencies
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'afgc-front' );
	}

	/**
	 * Get style dependencies
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'afgc-front', 'afgc-elementor-widgets' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {

		// Content Tab
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Button Settings', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_preview_button',
			array(
				'label'        => esc_html__( 'Show Preview Button', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'preview_button_text',
			array(
				'label'       => esc_html__( 'Preview Button Text', 'addify_giftcard' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => esc_html__( 'Preview', 'addify_giftcard' ),
				'placeholder' => esc_html__( 'Preview', 'addify_giftcard' ),
				'condition'   => array(
					'show_preview_button' => 'yes',
				),
			)
		);

		$this->add_control(
			'add_to_cart_text',
			array(
				'label'       => esc_html__( 'Add to Cart Text', 'addify_giftcard' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Add to Cart', 'addify_giftcard' ),
				'description' => esc_html__( 'Leave empty to use the default text from WooCommerce.', 'addify_giftcard' ),
			)
		);

		$this->add_responsive_control(
			'button_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'addify_giftcard' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'addify_giftcard' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'addify_giftcard' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'left',
				'selectors' => array(
					'{{WRAPPER}} .afgc-add-to-cart-widget' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_layout',
			array(
				'label'   => esc_html__( 'Button Layout', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'inline',
				'options' => array(
					'inline'  => esc_html__( 'Inline', 'addify_giftcard' ),
					'stacked' => esc_html__( 'Stacked', 'addify_giftcard' ),
				),
			)
		);

		$this->add_responsive_control(
			'button_gap',
			array(
				'label'      => esc_html__( 'Button Gap', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 10,
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-add-to-cart-buttons' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Preview Button
		$this->start_controls_section(
			'section_style_preview',
			array(
				'label'     => esc_html__( 'Preview Button', 'addify_giftcard' ),
				'tab'       => \Elementor\Controls_Manager::TAB_STYLE,
				'condition' => array(
					'show_preview_button' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'preview_button_tabs' );

		$this->start_controls_tab(
			'preview_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'preview_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_preview_btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'preview_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_preview_btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'preview_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'preview_hover_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_preview_btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'preview_hover_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_preview_btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'preview_border',
				'label'     => esc_html__( 'Border', 'addify_giftcard' ),
				'selector'  => '{{WRAPPER}} .afgc_preview_btn',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'preview_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc_preview_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'preview_padding',
			array(
				'label'      => esc_html__( 'Padding', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc_preview_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'preview_typography',
				'label'    => esc_html__( 'Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc_preview_btn',
			)
		);

		$this->end_controls_section();

		// Style Tab - Add to Cart Button
		$this->start_controls_section(
			'section_style_add_to_cart',
			array(
				'label' => esc_html__( 'Add to Cart Button', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'add_to_cart_button_tabs' );

		$this->start_controls_tab(
			'add_to_cart_button_normal',
			array(
				'label' => esc_html__( 'Normal', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'add_to_cart_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_submit_btn' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'add_to_cart_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_submit_btn' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'add_to_cart_button_hover',
			array(
				'label' => esc_html__( 'Hover', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'add_to_cart_hover_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_submit_btn:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'add_to_cart_hover_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc_submit_btn:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'      => 'add_to_cart_border',
				'label'     => esc_html__( 'Border', 'addify_giftcard' ),
				'selector'  => '{{WRAPPER}} .afgc_submit_btn',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'add_to_cart_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc_submit_btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'add_to_cart_padding',
			array(
				'label'      => esc_html__( 'Padding', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc_submit_btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'add_to_cart_typography',
				'label'    => esc_html__( 'Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc_submit_btn',
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'add_to_cart_shadow',
				'label'    => esc_html__( 'Box Shadow', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc_submit_btn',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get the current product
	 *
	 * @return WC_Product|false
	 */
	private function get_current_product() {
		global $product;

		if ( $product && is_a( $product, 'WC_Product' ) ) {
			return $product;
		}

		if ( is_singular( 'product' ) ) {
			return wc_get_product( get_the_ID() );
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $document ) {
				$preview_id = $document->get_settings( 'preview_id' );
				if ( $preview_id ) {
					return wc_get_product( $preview_id );
				}
			}
		}

		// Fallback for editor preview
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => 1,
				'tax_query'      => array(
					array(
						'taxonomy' => 'product_type',
						'field'    => 'slug',
						'terms'    => 'gift_card',
					),
				),
			);
			$query = new \WP_Query( $args );
			if ( $query->have_posts() ) {
				return wc_get_product( $query->posts[0]->ID );
			}
		}

		return false;
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$product  = $this->get_current_product();

		if ( ! $product ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="afgc-elementor-notice">';
				echo '<p>' . esc_html__( 'No gift card product found.', 'addify_giftcard' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		if ( ! $product->is_type( 'gift_card' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="afgc-elementor-notice">';
				echo '<p>' . esc_html__( 'This widget only works with Gift Card products.', 'addify_giftcard' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		wp_enqueue_script( 'afgc-front' );
		wp_enqueue_style( 'afgc-front' );

		// Get button text
		$add_to_cart_text = ! empty( $settings['add_to_cart_text'] ) ? $settings['add_to_cart_text'] : $product->single_add_to_cart_text();
		$preview_text     = ! empty( $settings['preview_button_text'] ) ? $settings['preview_button_text'] : esc_html__( 'Preview', 'addify_giftcard' );

		// Button layout class
		$layout_class = 'inline' === $settings['button_layout'] ? 'afgc-buttons-inline' : 'afgc-buttons-stacked';

		echo '<div class="afgc-add-to-cart-widget">';
		echo '<div class="afgc-add-to-cart-buttons ' . esc_attr( $layout_class ) . '">';

		// Preview button
		if ( 'yes' === $settings['show_preview_button'] ) {
			echo '<button type="button" id="preview_button" class="afgc_preview_btn button wp-element-button">';
			echo esc_html( $preview_text );
			echo '</button>';
		}

		// Add to cart button
		echo '<button type="submit" name="add-to-cart" value="' . esc_attr( $product->get_id() ) . '" class="single_add_to_cart_button button alt afgc_submit_btn wp-element-button">';
		echo esc_html( $add_to_cart_text );
		echo '</button>';

		echo '</div>'; // .afgc-add-to-cart-buttons
		echo '</div>'; // .afgc-add-to-cart-widget
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<#
		var layoutClass = settings.button_layout === 'inline' ? 'afgc-buttons-inline' : 'afgc-buttons-stacked';
		var previewText = settings.preview_button_text || '<?php echo esc_js( __( 'Preview', 'addify_giftcard' ) ); ?>';
		var addToCartText = settings.add_to_cart_text || '<?php echo esc_js( __( 'Add to Cart', 'addify_giftcard' ) ); ?>';
		#>
		<div class="afgc-add-to-cart-widget">
			<div class="afgc-add-to-cart-buttons {{{ layoutClass }}}">
				<# if ( settings.show_preview_button === 'yes' ) { #>
				<button type="button" class="afgc_preview_btn button wp-element-button">{{{ previewText }}}</button>
				<# } #>
				<button type="button" class="single_add_to_cart_button button alt afgc_submit_btn wp-element-button">{{{ addToCartText }}}</button>
			</div>
		</div>
		<?php
	}
}
