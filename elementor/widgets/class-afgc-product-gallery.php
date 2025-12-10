<?php
/**
 * Elementor Gift Card Product Gallery Widget
 *
 * Dynamic widget for displaying gift card image gallery on Elementor Single Product Page templates
 *
 * @package Addify Gift Card
 * @since 1.7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gift Card Product Gallery Widget for Elementor Theme Builder
 */
class AFGC_Elementor_Widget_Product_Gallery extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-product-gallery';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Gift Card Image Selector', 'addify_giftcard' );
	}

	/**
	 * Get widget icon
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-gallery-grid';
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
		return array( 'gift', 'card', 'gallery', 'image', 'selector', 'product' );
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
				'label' => esc_html__( 'Gallery Settings', 'addify_giftcard' ),
			)
		);

		$this->add_responsive_control(
			'columns',
			array(
				'label'          => esc_html__( 'Columns', 'addify_giftcard' ),
				'type'           => \Elementor\Controls_Manager::SELECT,
				'default'        => '4',
				'tablet_default' => '3',
				'mobile_default' => '2',
				'options'        => array(
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				),
				'selectors'      => array(
					'{{WRAPPER}} .afgc-product-gallery-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
				),
			)
		);

		$this->add_control(
			'show_product_image',
			array(
				'label'        => esc_html__( 'Show Product Featured Image', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_gallery_images',
			array(
				'label'        => esc_html__( 'Show Product Gallery Images', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'show_category_images',
			array(
				'label'        => esc_html__( 'Show Category Gallery Images', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show images from the gift card gallery categories assigned to this product.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_view_all_link',
			array(
				'label'        => esc_html__( 'Show View All Link', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show a link to open the full gallery modal.', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'show_upload_button',
			array(
				'label'        => esc_html__( 'Show Custom Image Upload', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'description'  => esc_html__( 'Show the custom image upload button (if enabled in product settings).', 'addify_giftcard' ),
			)
		);

		$this->add_control(
			'section_title',
			array(
				'label'       => esc_html__( 'Section Title', 'addify_giftcard' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'Gift Card Gallery', 'addify_giftcard' ),
				'description' => esc_html__( 'Leave empty to use the default title from settings.', 'addify_giftcard' ),
			)
		);

		$this->end_controls_section();

		// Style Tab - Container
		$this->start_controls_section(
			'section_style_container',
			array(
				'label' => esc_html__( 'Container', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'container_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-product-gallery-widget' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .afgc-product-gallery-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Title
		$this->start_controls_section(
			'section_style_title',
			array(
				'label' => esc_html__( 'Title', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-product-gallery-widget h5' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-product-gallery-widget h5',
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Title Margin', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-product-gallery-widget h5' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Images
		$this->start_controls_section(
			'section_style_images',
			array(
				'label' => esc_html__( 'Images', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'image_gap',
			array(
				'label'      => esc_html__( 'Gap', 'addify_giftcard' ),
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
					'{{WRAPPER}} .afgc-product-gallery-grid' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-product-gallery-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			array(
				'name'     => 'image_border',
				'label'    => esc_html__( 'Border', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-product-gallery-item img',
			)
		);

		$this->add_control(
			'image_selected_border_color',
			array(
				'label'     => esc_html__( 'Selected Border Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#2196f3',
				'selectors' => array(
					'{{WRAPPER}} .afgc-product-gallery-item.selected img' => 'border-color: {{VALUE}}; border-width: 3px;',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_shadow',
				'label'    => esc_html__( 'Box Shadow', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-product-gallery-item img',
			)
		);

		$this->add_control(
			'image_hover_opacity',
			array(
				'label'     => esc_html__( 'Hover Opacity', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1,
						'step' => 0.1,
					),
				),
				'default'   => array(
					'size' => 0.8,
				),
				'selectors' => array(
					'{{WRAPPER}} .afgc-product-gallery-item:hover img' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - Upload Button
		$this->start_controls_section(
			'section_style_upload',
			array(
				'label' => esc_html__( 'Upload Button', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'upload_button_background',
			array(
				'label'     => esc_html__( 'Background Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-upload-img label' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'upload_button_text_color',
			array(
				'label'     => esc_html__( 'Text Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-upload-img label' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'upload_button_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'addify_giftcard' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} .afgc-upload-img label' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		// Style Tab - View All Link
		$this->start_controls_section(
			'section_style_view_all',
			array(
				'label' => esc_html__( 'View All Link', 'addify_giftcard' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'view_all_color',
			array(
				'label'     => esc_html__( 'Link Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-view-all-link a' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'view_all_hover_color',
			array(
				'label'     => esc_html__( 'Link Hover Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-view-all-link a:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'view_all_typography',
				'label'    => esc_html__( 'Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-view-all-link a',
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
				echo '<p>' . esc_html__( 'No gift card product found. Please create a gift card product first.', 'addify_giftcard' ) . '</p>';
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

		// Check if virtual gift card (only virtual cards have image selection)
		$is_virtual = get_post_meta( $product->get_id(), 'afgc_virtual', true );
		if ( 'yes' !== $is_virtual ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="afgc-elementor-notice">';
				echo '<p>' . esc_html__( 'Image gallery is only available for virtual gift cards.', 'addify_giftcard' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Check if gallery is enabled
		$afgc_enable_card_gallery = get_option( 'afgc_enable_card_gallery' );
		if ( 'yes' !== $afgc_enable_card_gallery ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="afgc-elementor-notice">';
				echo '<p>' . esc_html__( 'Gift Card Gallery is disabled in plugin settings.', 'addify_giftcard' ) . '</p>';
				echo '</div>';
			}
			return;
		}

		// Enqueue scripts
		wp_enqueue_script( 'afgc-front' );
		wp_enqueue_style( 'afgc-front' );

		// Get settings
		$afgc_allow_custom_img         = get_post_meta( $product->get_id(), 'afgc_allow_custom_img', true );
		$afgc_enable_custom_image_btn  = get_post_meta( $product->get_id(), 'afgc_enable_custom_image_btn', true );
		$afgc_card_gallery_section_title = get_option( 'afgc_card_gallery_section_title' );
		$product_categories            = wp_get_post_terms( $product->get_id(), 'afgc_gallery_cat' );

		// Get section title
		$title = ! empty( $settings['section_title'] ) ? $settings['section_title'] : $afgc_card_gallery_section_title;
		if ( empty( $title ) ) {
			$title = esc_html__( 'Gift Card Gallery', 'addify_giftcard' );
		}

		// Get featured image URL
		$featured_img_url = wp_get_attachment_url( get_post_thumbnail_id( $product->get_id() ) );

		echo '<div class="afgc-product-gallery-widget">';

		// Section header
		echo '<div class="afgc-gallery-header">';
		echo '<h5>' . esc_html( $title ) . '</h5>';

		// Custom upload button
		if ( 'yes' === $settings['show_upload_button'] && 'yes' === $afgc_allow_custom_img ) {
			echo '<div class="afgc-upload-img">';
			$upload_label = ! empty( $afgc_enable_custom_image_btn ) ? $afgc_enable_custom_image_btn : esc_html__( 'Upload Card', 'addify_giftcard' );
			echo '<label for="afgc_upload_img_btn_widget">' . esc_html( $upload_label ) . '</label>';
			echo '<input type="file" class="afgc-upload-img-btn" name="afgc_upload_img_btn" id="afgc_upload_img_btn_widget" value="Choose" accept=".png, .gif, .jpeg, .jpg" style="display:none;">';
			echo '</div>';
			echo '<div class="afgc_popup_text" id="afgc_image_popup">' . esc_html__( 'Please upload an image in png, gif, jpeg, or jpg format with a maximum file size of 2MB.', 'addify_giftcard' ) . '</div>';
		}
		echo '</div>';

		// Gallery grid
		echo '<div class="afgc-product-gallery-grid">';

		// Featured image
		if ( 'yes' === $settings['show_product_image'] && has_post_thumbnail( $product->get_id() ) ) {
			echo '<div class="afgc-product-gallery-item selected" data-img-url="' . esc_url( $featured_img_url ) . '">';
			echo get_the_post_thumbnail( $product->get_id(), 'thumbnail' );
			echo '</div>';
		}

		// Product gallery images
		if ( 'yes' === $settings['show_gallery_images'] ) {
			$attachment_ids = $product->get_gallery_image_ids();
			foreach ( $attachment_ids as $attachment_id ) {
				$img_url = wp_get_attachment_url( $attachment_id );
				echo '<div class="afgc-product-gallery-item" data-img-url="' . esc_url( $img_url ) . '">';
				echo wp_get_attachment_image( $attachment_id, 'thumbnail' );
				echo '</div>';
			}
		}

		// View all link
		if ( 'yes' === $settings['show_view_all_link'] && $product_categories ) {
			echo '<div class="afgc-product-gallery-item afgc-view-all-link">';
			echo '<a href="#" id="afgc_view_all_card">' . esc_html__( 'View All', 'addify_giftcard' ) . '</a>';
			echo '</div>';
		}

		echo '</div>'; // .afgc-product-gallery-grid

		// Hidden input for selected image
		echo '<input type="hidden" name="afgc_selected_img" class="afgc_selected_img" value="' . esc_url( $featured_img_url ) . '">';

		echo '</div>'; // .afgc-product-gallery-widget

		// Include the modal template
		if ( 'yes' === $settings['show_view_all_link'] && $product_categories ) {
			if ( file_exists( AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php' ) ) {
				include AFGC_PLUGIN_DIR . '/front/includes/templates/afgc-gift-card-taxonomies-modal.php';
			}
		}
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<div class="afgc-product-gallery-widget afgc-elementor-editor-preview">
			<div class="afgc-editor-placeholder">
				<i class="eicon-gallery-grid"></i>
				<p><?php echo esc_html__( 'Gift Card Image Selector', 'addify_giftcard' ); ?></p>
				<span><?php echo esc_html__( 'The image gallery will be displayed here on the frontend.', 'addify_giftcard' ); ?></span>
			</div>
		</div>
		<?php
	}
}
