<?php
/**
 * Elementor Gift Card Product Form Widget
 *
 * Dynamic widget for Elementor Single Product Page templates
 * Automatically detects the current product context
 *
 * @package Addify Gift Card
 * @since 1.7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gift Card Product Form Widget for Elementor Theme Builder
 */
class AFGC_Elementor_Widget_Product_Form extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-product-form';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Gift Card Form', 'addify_giftcard' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
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
		return array( 'gift', 'card', 'form', 'product', 'woocommerce', 'add to cart' );
	}

	/**
	 * Get script dependencies
	 *
	 * @return array
	 */
	public function get_script_depends() {
		return array( 'afgc-front', 'select2' );
	}

	/**
	 * Get style dependencies
	 *
	 * @return array
	 */
	public function get_style_depends() {
		return array( 'afgc-front', 'select2', 'afgc-elementor-widgets' );
	}

	/**
	 * Register widget controls
	 */
	protected function register_controls() {

		// Content Tab - Display Settings
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Display Settings', 'addify_giftcard' ),
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
				'description'  => esc_html__( 'Show or hide the gift card image gallery selection.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_amount_selector',
			array(
				'label'        => esc_html__( 'Show Amount Selector', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show or hide the amount selection options.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_delivery_options',
			array(
				'label'        => esc_html__( 'Show Delivery Options', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show or hide the delivery method options (Email/Print).', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_recipient_form',
			array(
				'label'        => esc_html__( 'Show Recipient Form', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show or hide the recipient information form.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_sender_form',
			array(
				'label'        => esc_html__( 'Show Sender Form', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show or hide the sender information form.', 'addify_giftcard' ),
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
				'description'  => esc_html__( 'Show or hide the preview button.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_add_to_cart',
			array(
				'label'        => esc_html__( 'Show Add to Cart Button', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show or hide the add to cart button.', 'addify_giftcard' ),
			)
		);

		$this->end_controls_section();

		// Style Tab - Form Container
		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => esc_html__( 'Form Container', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'container_padding',
			array(
				'label'      => esc_html__( 'Padding', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-elementor-product-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'container_border',
				'label'    => esc_html__( 'Border', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-elementor-product-form',
			)
		);

		$this->add_control(
			'container_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-elementor-product-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Section Titles
		$this->start_controls_section(
			'section_style_titles',
			array(
				'label' => esc_html__( 'Section Titles', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form h5' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form h3' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-elementor-product-form h5, {{WRAPPER}} .afgc-elementor-product-form h3',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Title Margin', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-elementor-product-form h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Form Fields
		$this->start_controls_section(
			'section_style_fields',
			array(
				'label' => esc_html__( 'Form Fields', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'field_background',
			array(
				'label'     => esc_html__( 'Field Background', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form input[type="text"]' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form input[type="email"]' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form input[type="number"]' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form input[type="date"]' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form textarea' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form select' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_text_color',
			array(
				'label'     => esc_html__( 'Field Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form input' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form textarea' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form select' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_border_color',
			array(
				'label'     => esc_html__( 'Field Border Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form input' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form textarea' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form select' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_border_radius',
			array(
				'label'      => esc_html__( 'Field Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-elementor-product-form input' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .afgc-elementor-product-form textarea' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .afgc-elementor-product-form select' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'label'    => esc_html__( 'Field Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-elementor-product-form input, {{WRAPPER}} .afgc-elementor-product-form textarea, {{WRAPPER}} .afgc-elementor-product-form select',
			)
		);

		$this->end_controls_section();

		// Style Tab - Labels
		$this->start_controls_section(
			'section_style_labels',
			array(
				'label' => esc_html__( 'Labels', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'label_color',
			array(
				'label'     => esc_html__( 'Label Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form label' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form .afgc-form-group label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'label_typography',
				'label'    => esc_html__( 'Label Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-elementor-product-form label',
			)
		);

		$this->end_controls_section();

		// Style Tab - Buttons
		$this->start_controls_section(
			'section_style_buttons',
			array(
				'label' => esc_html__( 'Buttons', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Button Background', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form .button' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form button[type="submit"]' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_text_color',
			array(
				'label'     => esc_html__( 'Button Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form .button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form button[type="submit"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_background',
			array(
				'label'     => esc_html__( 'Button Hover Background', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-elementor-product-form .button:hover' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-elementor-product-form button[type="submit"]:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => esc_html__( 'Button Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-elementor-product-form .button' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .afgc-elementor-product-form button[type="submit"]' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'label'    => esc_html__( 'Button Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-elementor-product-form .button, {{WRAPPER}} .afgc-elementor-product-form button[type="submit"]',
			)
		);

		$this->end_controls_section();

		// Style Tab - Gallery Images
		$this->start_controls_section(
			'section_style_gallery',
			array(
				'label' => esc_html__( 'Gallery Images', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'gallery_image_border_radius',
			array(
				'label'      => esc_html__( 'Image Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-choose-image-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .afgc-gift-pro-gall-img img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'gallery_image_border_color',
			array(
				'label'     => esc_html__( 'Selected Image Border Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-choose-image-item.selected img' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-gift-pro-gall-img.selected img' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'gallery_image_shadow',
				'label'    => esc_html__( 'Image Shadow', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-choose-image-item img, {{WRAPPER}} .afgc-gift-pro-gall-img img',
			)
		);

		$this->end_controls_section();

		// Style Tab - Amount Options
		$this->start_controls_section(
			'section_style_amount',
			array(
				'label' => esc_html__( 'Amount Options', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'amount_option_background',
			array(
				'label'     => esc_html__( 'Option Background', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-enter-custom-amount li' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'amount_option_selected_background',
			array(
				'label'     => esc_html__( 'Selected Option Background', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-enter-custom-amount li.selected' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .afgc-option-radio input:checked + span' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'amount_option_text_color',
			array(
				'label'     => esc_html__( 'Option Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-enter-custom-amount li' => 'color: {{VALUE}};',
					'{{WRAPPER}} .afgc-option-radio' => 'color: {{VALUE}};',
				),
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

		// If we already have a product in global scope
		if ( $product && is_a( $product, 'WC_Product' ) ) {
			return $product;
		}

		// Try to get product from the query
		if ( is_singular( 'product' ) ) {
			return wc_get_product( get_the_ID() );
		}

		// Try to get from Elementor's document settings (for theme builder)
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get_current();
			if ( $document ) {
				$preview_id = $document->get_settings( 'preview_id' );
				if ( $preview_id ) {
					return wc_get_product( $preview_id );
				}
			}
		}

		// Fallback: get first gift card product for editor preview
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
				echo '<p>' . esc_html__( 'No gift card product found. Please create a gift card product first.', 'addify_giftcard' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Check if this is a gift card product
		if ( ! $product->is_type( 'gift_card' ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="afgc-elementor-notice">';
				echo '<p>' . esc_html__( 'This widget only works with Gift Card products. Current product type: ', 'addify_giftcard' ) . esc_html( $product->get_type() ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Set up global product and post for template compatibility
		global $post;
		$original_post = $post;
		$post = get_post( $product->get_id() );
		setup_postdata( $post );

		// Enqueue required scripts and styles
		wp_enqueue_script( 'afgc-front' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_style( 'afgc-front' );
		wp_enqueue_style( 'select2' );

		echo '<div class="afgc-elementor-product-form">';

		// Generate dynamic CSS based on settings
		$this->render_dynamic_styles( $settings );

		// Include the gift card modal template
		if ( file_exists( AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php' ) ) {
			include AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php';
		}

		// Include the gift card form template
		if ( file_exists( AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-gift-card-product-temp.php' ) ) {
			include AFGC_PLUGIN_DIR . '/admin/includes/templates/afgc-gift-card-product-temp.php';
		}

		echo '</div>';

		// Restore original post data
		$post = $original_post;
		if ( $original_post ) {
			setup_postdata( $original_post );
		} else {
			wp_reset_postdata();
		}
	}

	/**
	 * Render dynamic styles based on widget settings
	 *
	 * @param array $settings Widget settings.
	 */
	private function render_dynamic_styles( $settings ) {
		$styles = '<style>';

		// Hide gallery if disabled
		if ( 'yes' !== $settings['show_gallery'] ) {
			$styles .= '.afgc-elementor-product-form .afgc-virtual-gift-card-choose-image { display: none !important; }';
		}

		// Hide amount selector if disabled
		if ( 'yes' !== $settings['show_amount_selector'] ) {
			$styles .= '.afgc-elementor-product-form .afgc-virtual-custom-amount,';
			$styles .= '.afgc-elementor-product-form .afgc-physical-custom-amount { display: none !important; }';
		}

		// Hide delivery options if disabled
		if ( 'yes' !== $settings['show_delivery_options'] ) {
			$styles .= '.afgc-elementor-product-form .afgc_gift_card_opt { display: none !important; }';
		}

		// Hide recipient form if disabled
		if ( 'yes' !== $settings['show_recipient_form'] ) {
			$styles .= '.afgc-elementor-product-form .afgc-recipient-info { display: none !important; }';
		}

		// Hide sender form if disabled
		if ( 'yes' !== $settings['show_sender_form'] ) {
			$styles .= '.afgc-elementor-product-form .afgc-sender-info { display: none !important; }';
		}

		// Hide preview button if disabled
		if ( 'yes' !== $settings['show_preview_button'] ) {
			$styles .= '.afgc-elementor-product-form .afgc_preview_btn { display: none !important; }';
		}

		// Hide add to cart button if disabled
		if ( 'yes' !== $settings['show_add_to_cart'] ) {
			$styles .= '.afgc-elementor-product-form .afgc_submit_btn { display: none !important; }';
		}

		$styles .= '</style>';

		echo $styles; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<div class="afgc-elementor-product-form afgc-elementor-editor-preview">
			<div class="afgc-editor-placeholder">
				<i class="eicon-form-horizontal"></i>
				<p><?php echo esc_html__( 'Gift Card Form', 'addify_giftcard' ); ?></p>
				<span><?php echo esc_html__( 'The gift card form will be displayed here on the frontend.', 'addify_giftcard' ); ?></span>
			</div>
		</div>
		<?php
	}
}
