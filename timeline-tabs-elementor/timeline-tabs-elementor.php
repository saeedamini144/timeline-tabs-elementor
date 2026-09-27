<?php
/**
 * Plugin Name: Timeline Tabs for Elementor
 * Plugin URI:  https://websemicolon.com/
 * Description: ویجت «تب تایم‌لاین» برای المنتور؛ ساخت تایم‌لاین تب‌دار با تنظیمات محتوا و استایل مستقل برای هر بخش.
 * Version:     1.1.0
 * Author: Saeed Amini
 * Author URI: https://github.com/saeedamini144
 * Text Domain: timeline-tabs-el
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Elementor tested up to: 4.3.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TT_EL_VERSION', '1.1.0' );
define( 'TT_EL_FILE', __FILE__ );
define( 'TT_EL_PATH', plugin_dir_path( __FILE__ ) );
define( 'TT_EL_URL', plugin_dir_url( __FILE__ ) );
define( 'TT_EL_MIN_ELEMENTOR', '3.5.0' );

final class TT_Elementor_Timeline_Tabs {

	/** @var self|null */
	private static $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'boot' ] );
	}

	public function boot(): void {
		load_plugin_textdomain( 'timeline-tabs-el', false, dirname( plugin_basename( TT_EL_FILE ) ) . '/languages' );

		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_missing_elementor' ] );
			return;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, TT_EL_MIN_ELEMENTOR, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'notice_old_elementor' ] );
			return;
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Assets are registered (not enqueued); the widget declares them through
		// get_style_depends()/get_script_depends() so they load only on pages that use it.
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'register_assets' ] );
	}

	public function register_category( $manager ): void {
		$manager->add_category(
			'tt-widgets',
			[
				'title' => esc_html__( 'ویجت‌های تایم‌لاین', 'timeline-tabs-el' ),
				'icon'  => 'eicon-time-line',
			]
		);
	}

	public function register_widgets( $widgets_manager ): void {
		require_once TT_EL_PATH . 'widgets/class-timeline-tabs-widget.php';
		$widgets_manager->register( new \TT_Elementor\Widgets\Timeline_Tabs() );
	}

	public function register_assets(): void {
		wp_register_style(
			'tt-timeline-tabs',
			TT_EL_URL . 'assets/css/timeline-tabs.css',
			[],
			TT_EL_VERSION
		);

		wp_register_script(
			'tt-timeline-tabs',
			TT_EL_URL . 'assets/js/timeline-tabs.js',
			[],
			TT_EL_VERSION,
			true
		);
	}

	public function notice_missing_elementor(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			esc_html__( 'ویجت «تب تایم‌لاین» برای کار کردن به افزونهٔ المنتور نیاز دارد.', 'timeline-tabs-el' )
		);
	}

	public function notice_old_elementor(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			sprintf(
				/* translators: %s: minimum Elementor version */
				esc_html__( 'ویجت «تب تایم‌لاین» به المنتور نسخهٔ %s یا بالاتر نیاز دارد.', 'timeline-tabs-el' ),
				esc_html( TT_EL_MIN_ELEMENTOR )
			)
		);
	}
}

TT_Elementor_Timeline_Tabs::instance();
