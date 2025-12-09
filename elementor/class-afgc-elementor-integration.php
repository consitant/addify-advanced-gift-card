<?php
/**
 * Elementor Integration Main Class
 *
 * @package Addify Gift Card
 * @since 1.7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Elementor Integration Class
 */
class AFGC_Elementor_Integration {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Minimum Elementor Version
	 *
	 * @var string
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @var string
	 */
	const MINIMUM_PHP_VERSION = '7.4';

	/**
	 * Constructor
	 */
	private function __construct() {
		// Initialize immediately if Elementor is already loaded, otherwise wait for elementor/loaded
		if ( did_action( 'elementor/loaded' ) ) {
			$this->init();
		} else {
			add_action( 'elementor/loaded', array( $this, 'init' ) );
		}
	}

	/**
	 * Get instance of this class
	 *
	 * @return AFGC_Elementor_Integration
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {

		// Check for required Elementor version.
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version.
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'admin_notice_minimum_php_version' ) );
			return;
		}

		// Register widget category.
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_widget_category' ) );

		// Register widgets.
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );

		// Register widget styles.
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_widget_styles' ) );

		// Register widget scripts.
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'register_widget_scripts' ) );
	}

	/**
	 * Admin notice for minimum Elementor version
	 */
	public function admin_notice_minimum_elementor_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'addify_giftcard' ),
			'<strong>' . esc_html__( 'Addify Gift Card Elementor Widgets', 'addify_giftcard' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'addify_giftcard' ) . '</strong>',
			self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Admin notice for minimum PHP version
	 */
	public function admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'addify_giftcard' ),
			'<strong>' . esc_html__( 'Addify Gift Card Elementor Widgets', 'addify_giftcard' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'addify_giftcard' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}

	/**
	 * Register widget category
	 *
	 * @param object $elements_manager Elementor elements manager.
	 */
	public function register_widget_category( $elements_manager ) {
		$elements_manager->add_category(
			'afgc-gift-cards',
			array(
				'title' => esc_html__( 'Gift Cards', 'addify_giftcard' ),
				'icon'  => 'fa fa-gift',
			)
		);
	}

	/**
	 * Register widgets
	 *
	 * @param object $widgets_manager Elementor widgets manager.
	 */
	public function register_widgets( $widgets_manager ) {
		// Include widget files.
		require_once AFGC_PLUGIN_DIR . 'elementor/widgets/class-afgc-gift-cards-grid.php';
		require_once AFGC_PLUGIN_DIR . 'elementor/widgets/class-afgc-gift-card-single.php';
		require_once AFGC_PLUGIN_DIR . 'elementor/widgets/class-afgc-gallery.php';

		// Register widgets.
		$widgets_manager->register( new \AFGC_Elementor_Widget_Gift_Cards_Grid() );
		$widgets_manager->register( new \AFGC_Elementor_Widget_Gift_Card_Single() );
		$widgets_manager->register( new \AFGC_Elementor_Widget_Gallery() );
	}

	/**
	 * Enqueue widget styles
	 */
	public function enqueue_widget_styles() {
		wp_enqueue_style(
			'afgc-elementor-widgets',
			AFGC_PLUGIN_URL . 'elementor/assets/css/elementor-widgets.css',
			array(),
			AFGC_PLUGIN_VERSION
		);
	}

	/**
	 * Register widget scripts
	 */
	public function register_widget_scripts() {
		wp_register_script(
			'afgc-elementor-widgets',
			AFGC_PLUGIN_URL . 'elementor/assets/js/elementor-widgets.js',
			array( 'jquery' ),
			AFGC_PLUGIN_VERSION,
			true
		);
	}
}

// Initialize the integration.
AFGC_Elementor_Integration::get_instance();
