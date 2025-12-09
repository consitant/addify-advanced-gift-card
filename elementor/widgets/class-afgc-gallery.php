<?php
/**
 * Elementor Gift Card Gallery Widget
 *
 * @package Addify Gift Card
 * @since 1.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Gift Card Gallery Widget
 */
class AFGC_Elementor_Widget_Gallery extends \Elementor\Widget_Base {

	/**
	 * Get widget name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'afgc-gift-card-gallery';
	}

	/**
	 * Get widget title
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Gift Card Gallery', 'addify_giftcard' );
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
		return array( 'afgc-gift-cards' );
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return array( 'gift', 'card', 'gallery', 'images' );
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

		// Get gallery categories.
		$gallery_categories = $this->get_gallery_categories();

		$this->add_control(
			'gallery_categories',
			array(
				'label'       => esc_html__( 'Select Gallery Categories', 'addify_giftcard' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => $gallery_categories,
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(),
			)
		);

		$this->add_control(
			'columns',
			array(
				'label'   => esc_html__( 'Columns', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '4',
				'options' => array(
					'2' => esc_html__( '2', 'addify_giftcard' ),
					'3' => esc_html__( '3', 'addify_giftcard' ),
					'4' => esc_html__( '4', 'addify_giftcard' ),
					'5' => esc_html__( '5', 'addify_giftcard' ),
					'6' => esc_html__( '6', 'addify_giftcard' ),
				),
			)
		);

		$this->add_control(
			'images_per_category',
			array(
				'label'   => esc_html__( 'Images Per Category', 'addify_giftcard' ),
				'type'    => \Elementor\Controls_Manager::NUMBER,
				'default' => 8,
				'min'     => 1,
				'max'     => 100,
			)
		);

		$this->add_control(
			'show_category_title',
			array(
				'label'        => esc_html__( 'Show Category Title', 'addify_giftcard' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'addify_giftcard' ),
				'label_off'    => esc_html__( 'No', 'addify_giftcard' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'enable_lightbox',
			array(
				'label'        => esc_html__( 'Enable Lightbox', 'addify_giftcard' ),
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
						'max' => 50,
					),
				),
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .afgc-gallery-grid' => 'grid-column-gap: {{SIZE}}{{UNIT}};',
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
						'max' => 50,
					),
				),
				'default'   => array(
					'size' => 15,
				),
				'selectors' => array(
					'{{WRAPPER}} .afgc-gallery-grid' => 'grid-row-gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'category_title_color',
			array(
				'label'     => esc_html__( 'Category Title Color', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .afgc-gallery-category-title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			array(
				'name'     => 'category_title_typography',
				'label'    => esc_html__( 'Category Title Typography', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-gallery-category-title',
			)
		);

		$this->add_control(
			'image_border_radius',
			array(
				'label'     => esc_html__( 'Image Border Radius', 'addify_giftcard' ),
				'type'      => \Elementor\Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .afgc-gallery-item img' => 'border-radius: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'image_box_shadow',
				'label'    => esc_html__( 'Image Box Shadow', 'addify_giftcard' ),
				'selector' => '{{WRAPPER}} .afgc-gallery-item img',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Get gallery categories
	 *
	 * @return array
	 */
	private function get_gallery_categories() {
		$categories = array();

		$terms = get_terms(
			array(
				'taxonomy'   => 'afgc_gallery_cat',
				'hide_empty' => false,
			)
		);

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$categories[ $term->term_id ] = $term->name;
			}
		}

		return $categories;
	}

	/**
	 * Render widget output on the frontend
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$selected_categories = ! empty( $settings['gallery_categories'] ) ? $settings['gallery_categories'] : array();

		// Get terms.
		$args = array(
			'taxonomy'   => 'afgc_gallery_cat',
			'hide_empty' => false,
		);

		if ( ! empty( $selected_categories ) ) {
			$args['include'] = $selected_categories;
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			echo '<p>' . esc_html__( 'No gallery images found.', 'addify_giftcard' ) . '</p>';
			return;
		}

		echo '<div class="afgc-gallery-widget">';

		foreach ( $terms as $term ) {

			// Get images for this category.
			$images = get_term_meta( $term->term_id, 'afgc_upload_gallery_images', true );

			if ( empty( $images ) ) {
				continue;
			}

			// Decode images if serialized.
			if ( ! is_array( $images ) ) {
				$images = maybe_unserialize( $images );
			}

			if ( ! is_array( $images ) ) {
				continue;
			}

			// Limit images.
			$images = array_slice( $images, 0, intval( $settings['images_per_category'] ) );

			// Show category title.
			if ( 'yes' === $settings['show_category_title'] ) {
				echo '<h3 class="afgc-gallery-category-title">' . esc_html( $term->name ) . '</h3>';
			}

			// Display images.
			echo '<div class="afgc-gallery-grid" style="display: grid; grid-template-columns: repeat(' . esc_attr( $settings['columns'] ) . ', 1fr);">';

			foreach ( $images as $image_id ) {
				$image_url = wp_get_attachment_url( $image_id );

				if ( ! $image_url ) {
					continue;
				}

				echo '<div class="afgc-gallery-item">';

				if ( 'yes' === $settings['enable_lightbox'] ) {
					echo '<a href="' . esc_url( $image_url ) . '" data-elementor-open-lightbox="yes">';
				}

				echo '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $term->name ) . '" />';

				if ( 'yes' === $settings['enable_lightbox'] ) {
					echo '</a>';
				}

				echo '</div>';
			}

			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Render widget output in the editor
	 */
	protected function content_template() {
		?>
		<div class="afgc-gallery-widget">
			<p><?php echo esc_html__( 'Gift card gallery will be displayed here.', 'addify_giftcard' ); ?></p>
		</div>
		<?php
	}
}
