<?php
/**
 * Timeline Tabs widget.
 *
 * @package timeline-tabs-el
 */

namespace TT_Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Timeline_Tabs extends Widget_Base {

	const ROOT = '{{WRAPPER}} .tt';

	public function get_name() {
		return 'tt_timeline_tabs';
	}

	public function get_title() {
		return esc_html__( 'تب تایم‌لاین', 'timeline-tabs-el' );
	}

	public function get_icon() {
		return 'eicon-time-line';
	}

	public function get_categories() {
		return [ 'tt-widgets', 'general' ];
	}

	public function get_keywords() {
		return [ 'timeline', 'tabs', 'history', 'تایم لاین', 'تب', 'تاریخچه', 'زمان' ];
	}

	public function get_style_depends() {
		return [ 'tt-timeline-tabs' ];
	}

	public function get_script_depends() {
		return [ 'tt-timeline-tabs' ];
	}

	/** DOM سبک‌تر: بدون لایهٔ اضافی داخل ویجت. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/* =====================================================================
	 * کنترل‌ها
	 * ================================================================== */

	protected function register_controls() {
		$this->register_items_section();
		$this->register_settings_section();

		$this->register_layout_style();
		$this->register_timeline_style();
		$this->register_marker_style();
		$this->register_top_title_style();
		$this->register_bottom_title_style();
		$this->register_panel_style();
		$this->register_media_style( 'a', esc_html__( 'استایل تصویر اول', 'timeline-tabs-el' ) );
		$this->register_media_style( 'b', esc_html__( 'استایل تصویر دوم', 'timeline-tabs-el' ) );
		$this->register_sub_title_style();
		$this->register_title_style();
		$this->register_text_style();
		$this->register_decor_style();
	}

	/* ---------------------------------------------------------------- آیتم‌ها */

	private function register_items_section() {
		$this->start_controls_section(
			'section_items',
			[ 'label' => esc_html__( 'آیتم‌های تایم‌لاین', 'timeline-tabs-el' ) ]
		);

		$repeater = new Repeater();

		$repeater->start_controls_tabs( 'item_tabs' );

		/* --- تب: عنوان‌ها و آیکن --- */
		$repeater->start_controls_tab( 'item_tab_head', [ 'label' => esc_html__( 'تب', 'timeline-tabs-el' ) ] );

		$repeater->add_control(
			'tab_top_title',
			[
				'label'       => esc_html__( 'عنوان بالایی (اصلی)', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'مثال: ۱۳۲۷', 'timeline-tabs-el' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
				'description' => esc_html__( 'خالی بماند، نمایش داده نمی‌شود.', 'timeline-tabs-el' ),
			]
		);

		$repeater->add_control(
			'tab_bottom_title',
			[
				'label'       => esc_html__( 'عنوان پایینی', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'placeholder' => esc_html__( 'مثال: تأسیس کارگاه', 'timeline-tabs-el' ),
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
				'description' => esc_html__( 'با فعال شدن یا هاور روی تب نمایش داده می‌شود.', 'timeline-tabs-el' ),
			]
		);

		$repeater->add_control(
			'marker_type',
			[
				'label'   => esc_html__( 'نشانگر روی خط', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'auto',
				'options' => [
					'auto' => esc_html__( 'خودکار (آیکن در صورت وجود)', 'timeline-tabs-el' ),
					'dot'  => esc_html__( 'نقطه', 'timeline-tabs-el' ),
					'icon' => esc_html__( 'آیکن', 'timeline-tabs-el' ),
					'none' => esc_html__( 'بدون نشانگر', 'timeline-tabs-el' ),
				],
			]
		);

		$repeater->add_control(
			'tab_icon',
			[
				'label'     => esc_html__( 'آیکن تب', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::ICONS,
				'skin'      => 'inline',
				'condition' => [ 'marker_type!' => [ 'dot', 'none' ] ],
			]
		);

		$repeater->end_controls_tab();

		/* --- محتوا --- */
		$repeater->start_controls_tab( 'item_tab_content', [ 'label' => esc_html__( 'محتوا', 'timeline-tabs-el' ) ] );

		$repeater->add_control(
			'image_a',
			[
				'label'   => esc_html__( 'تصویر اول', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [ 'url' => '' ],
			]
		);

		$repeater->add_control(
			'image_b',
			[
				'label'   => esc_html__( 'تصویر دوم', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => [ 'active' => true ],
				'default' => [ 'url' => '' ],
			]
		);

		$repeater->add_control(
			'sub_title',
			[
				'label'       => esc_html__( 'ساب عنوان', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'main_title',
			[
				'label'       => esc_html__( 'عنوان اصلی', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 2,
				'dynamic'     => [ 'active' => true ],
			]
		);

		$repeater->add_control(
			'content_text',
			[
				'label'   => esc_html__( 'محتوا', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::WYSIWYG,
				'dynamic' => [ 'active' => true ],
			]
		);

		$repeater->end_controls_tab();

		/* --- استایل اختصاصی آیتم --- */
		$repeater->start_controls_tab( 'item_tab_style', [ 'label' => esc_html__( 'استایل', 'timeline-tabs-el' ) ] );

		$repeater->add_control(
			'item_style_note',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'این مقادیر تنظیمات کلی را فقط برای همین آیتم بازنویسی می‌کنند.', 'timeline-tabs-el' ),
				'content_classes' => 'elementor-descriptor',
			]
		);

		$repeater->add_control(
			'item_top_heading',
			[
				'label'     => esc_html__( 'عنوان بالایی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'item_top_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__top' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_control(
			'item_top_color_active',
			[
				'label'     => esc_html__( 'رنگ حالت فعال', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}}.is-active .tt__top' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_top_typography',
				'label'    => esc_html__( 'فونت', 'timeline-tabs-el' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .tt__top',
			]
		);

		$repeater->add_control(
			'item_bottom_heading',
			[
				'label'     => esc_html__( 'عنوان پایینی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'item_bottom_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__bottom' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'item_bottom_typography',
				'label'    => esc_html__( 'فونت', 'timeline-tabs-el' ),
				'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .tt__bottom',
			]
		);

		$repeater->add_control(
			'item_marker_heading',
			[
				'label'     => esc_html__( 'نشانگر', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'item_marker_color',
			[
				'label'     => esc_html__( 'رنگ نشانگر', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}} .tt__dot'  => 'background-color:{{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}} .tt__icon' => 'color:{{VALUE}};',
				],
			]
		);

		$repeater->add_control(
			'item_marker_color_active',
			[
				'label'     => esc_html__( 'رنگ نشانگر فعال', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}.is-active .tt__dot'  => 'background-color:{{VALUE}};',
					'{{WRAPPER}} {{CURRENT_ITEM}}.is-active .tt__icon' => 'color:{{VALUE}};',
				],
			]
		);

		$repeater->add_responsive_control(
			'item_icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکن', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 120 ] ],
				'selectors'  => [ '{{WRAPPER}} {{CURRENT_ITEM}}' => '--tt-icon-size:{{SIZE}}{{UNIT}};' ],
			]
		);

		$repeater->add_control(
			'item_content_heading',
			[
				'label'     => esc_html__( 'محتوا', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$repeater->add_control(
			'item_sub_color',
			[
				'label'     => esc_html__( 'رنگ ساب عنوان', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__sub' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_control(
			'item_title_color',
			[
				'label'     => esc_html__( 'رنگ عنوان اصلی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__title' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_control(
			'item_text_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__text' => 'color:{{VALUE}};' ],
			]
		);

		$repeater->add_responsive_control(
			'item_media_ratio',
			[
				'label'       => esc_html__( 'نسبت تصاویر این آیتم', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '13/20',
				'selectors'   => [ '{{WRAPPER}} {{CURRENT_ITEM}} .tt__media' => 'aspect-ratio:{{VALUE}};' ],
			]
		);

		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();

		$this->add_control(
			'timeline_items',
			[
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ tab_top_title || tab_bottom_title || main_title || "آیتم" }}}',
				'default'     => [
					[
						'tab_top_title'    => esc_html__( '۱۳۲۷', 'timeline-tabs-el' ),
						'tab_bottom_title' => esc_html__( 'تأسیس کارگاه', 'timeline-tabs-el' ),
						'sub_title'        => esc_html__( 'آغاز راه', 'timeline-tabs-el' ),
						'main_title'       => esc_html__( 'کارگاهی کوچک، ایده‌هایی بزرگ', 'timeline-tabs-el' ),
						'content_text'     => esc_html__( 'همه‌چیز از یک کارگاه کوچک شروع شد؛ با همان وسواسی که هنوز هم با ماست.', 'timeline-tabs-el' ),
					],
					[
						'tab_top_title'    => esc_html__( '۱۳۴۱', 'timeline-tabs-el' ),
						'tab_bottom_title' => esc_html__( 'جایگاه در بازار', 'timeline-tabs-el' ),
						'sub_title'        => esc_html__( 'تخصص ویژه', 'timeline-tabs-el' ),
						'main_title'       => esc_html__( 'خدمات جامع طراحی', 'timeline-tabs-el' ),
						'content_text'     => esc_html__( 'از نقشهٔ اولیه تا چیدمان نهایی، همهٔ مراحل را زیر یک سقف انجام دادیم.', 'timeline-tabs-el' ),
					],
					[
						'tab_top_title'    => esc_html__( '۱۴۰۲', 'timeline-tabs-el' ),
						'tab_bottom_title' => esc_html__( 'امروز', 'timeline-tabs-el' ),
						'sub_title'        => esc_html__( '۷۵ سال تجربه', 'timeline-tabs-el' ),
						'main_title'       => esc_html__( 'طراحی برای نسل بعد', 'timeline-tabs-el' ),
						'content_text'     => esc_html__( 'امروز با تیمی بزرگ‌تر، با همان دقت کارگاه اول کار می‌کنیم.', 'timeline-tabs-el' ),
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------- تنظیمات عمومی */

	private function register_settings_section() {
		$this->start_controls_section(
			'section_settings',
			[ 'label' => esc_html__( 'تنظیمات عمومی', 'timeline-tabs-el' ) ]
		);

		$this->add_control(
			'default_index',
			[
				'label'       => esc_html__( 'تب فعال در بارگذاری', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::NUMBER,
				'min'         => 1,
				'step'        => 1,
				'default'     => 1,
				'description' => esc_html__( 'شمارهٔ آیتم از ابتدای فهرست.', 'timeline-tabs-el' ),
			]
		);

		$this->add_control(
			'timeline_position',
			[
				'label'        => esc_html__( 'جای تایم‌لاین', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'top',
				'options'      => [
					'top'    => esc_html__( 'بالای محتوا', 'timeline-tabs-el' ),
					'bottom' => esc_html__( 'پایین محتوا', 'timeline-tabs-el' ),
				],
				'prefix_class' => 'tt-pos-',
			]
		);

		$this->add_control(
			'bottom_reveal',
			[
				'label'   => esc_html__( 'نمایش عنوان پایینی', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'both',
				'options' => [
					'both'   => esc_html__( 'فعال + هاور', 'timeline-tabs-el' ),
					'active' => esc_html__( 'فقط تب فعال', 'timeline-tabs-el' ),
					'hover'  => esc_html__( 'فقط هاور', 'timeline-tabs-el' ),
					'always' => esc_html__( 'همیشه', 'timeline-tabs-el' ),
				],
			]
		);

		$this->add_control(
			'tags_heading',
			[
				'label'     => esc_html__( 'تگ‌های HTML', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'tab_top_tag',
			[
				'label'   => esc_html__( 'تگ عنوان بالایی', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'span',
				'options' => $this->tag_options(),
			]
		);

		$this->add_control(
			'tab_bottom_tag',
			[
				'label'   => esc_html__( 'تگ عنوان پایینی', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'span',
				'options' => $this->tag_options(),
			]
		);

		$this->add_control(
			'sub_title_tag',
			[
				'label'   => esc_html__( 'تگ ساب عنوان', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'p',
				'options' => $this->tag_options(),
			]
		);

		$this->add_control(
			'main_title_tag',
			[
				'label'   => esc_html__( 'تگ عنوان اصلی', 'timeline-tabs-el' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h2',
				'options' => $this->tag_options(),
			]
		);

		$this->add_control(
			'images_heading',
			[
				'label'     => esc_html__( 'تصاویر', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumb',
				'label'     => esc_html__( 'اندازهٔ فایل تصویر', 'timeline-tabs-el' ),
				'default'   => 'large',
				'separator' => 'none',
			]
		);

		$this->add_control(
			'anim_heading',
			[
				'label'     => esc_html__( 'حرکت', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'enable_animation',
			[
				'label'        => esc_html__( 'انیمیشن تعویض تب', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'anim_duration',
			[
				'label'      => esc_html__( 'مدت انیمیشن', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'ms' ],
				'range'      => [ 'ms' => [ 'min' => 100, 'max' => 2000, 'step' => 50 ] ],
				'default'    => [ 'unit' => 'ms', 'size' => 700 ],
				'selectors'  => [ self::ROOT => '--tt-dur:{{SIZE}}ms;' ],
				'condition'  => [ 'enable_animation' => 'yes' ],
			]
		);

		$this->add_control(
			'auto_scroll',
			[
				'label'        => esc_html__( 'اسکرول خودکار تب انتخاب‌شده', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'touch_heading',
			[
				'label'     => esc_html__( 'موبایل و لمسی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'enable_swipe',
			[
				'label'        => esc_html__( 'تعویض تب با کشیدن انگشت', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'کشیدن افقی روی بخش محتوا، تب بعدی یا قبلی را باز می‌کند.', 'timeline-tabs-el' ),
			]
		);

		$this->add_control(
			'rail_fade',
			[
				'label'        => esc_html__( 'محو لبه‌های تایم‌لاین', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'return_value' => 'yes',
				'description'  => esc_html__( 'وقتی تب‌ها از عرض بیشتر شوند، لبهٔ قابل اسکرول محو می‌شود تا کاربر متوجه ادامهٔ تایم‌لاین شود.', 'timeline-tabs-el' ),
			]
		);

		$this->add_responsive_control(
			'rail_fade_size',
			[
				'label'      => esc_html__( 'اندازهٔ محو لبه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 32 ],
				'selectors'  => [ self::ROOT => '--tt-rail-fade:{{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'rail_fade' => 'yes' ],
			]
		);

		$this->add_control(
			'aria_label',
			[
				'label'       => esc_html__( 'برچسب دسترس‌پذیری بخش', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'تایم‌لاین', 'timeline-tabs-el' ),
				'label_block' => true,
				'separator'   => 'before',
			]
		);

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------- استایل کلی */

	private function register_layout_style() {
		$this->start_controls_section(
			'style_layout',
			[
				'label' => esc_html__( 'چیدمان کلی', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'root_max_width',
			[
				'label'      => esc_html__( 'حداکثر عرض', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'vw' ],
				'range'      => [ 'px' => [ 'min' => 320, 'max' => 2000 ] ],
				'selectors'  => [ self::ROOT => 'max-width:{{SIZE}}{{UNIT}};margin-inline:auto;' ],
			]
		);

		$this->add_responsive_control(
			'root_padding',
			[
				'label'      => esc_html__( 'فاصلهٔ داخلی', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ self::ROOT => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'root_bg',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => self::ROOT,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'root_border',
				'selector' => self::ROOT,
			]
		);

		$this->add_responsive_control(
			'root_radius',
			[
				'label'      => esc_html__( 'گردی گوشه‌ها', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ self::ROOT => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow:hidden;' ],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'root_shadow',
				'selector' => self::ROOT,
			]
		);

		$this->add_responsive_control(
			'timeline_content_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ تایم‌لاین از محتوا', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 48 ],
				'selectors'  => [ self::ROOT => 'gap:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------- خط تایم‌لاین */

	private function register_timeline_style() {
		$this->start_controls_section(
			'style_timeline',
			[
				'label' => esc_html__( 'خط تایم‌لاین', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'hide_line',
			[
				'label'        => esc_html__( 'پنهان کردن خط', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'none',
				'selectors'    => [ '{{WRAPPER}} .tt__line' => 'display:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'line_color',
			[
				'label'     => esc_html__( 'رنگ خط', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c9c6c0',
				'selectors' => [ self::ROOT => '--tt-line-c:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'line_height',
			[
				'label'      => esc_html__( 'ضخامت خط', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 1 ],
				'selectors'  => [ self::ROOT => '--tt-line-h:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'hide_fill',
			[
				'label'        => esc_html__( 'پنهان کردن خط پیشرفت', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'none',
				'selectors'    => [ '{{WRAPPER}} .tt__fill' => 'display:{{VALUE}};' ],
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'fill_color',
			[
				'label'     => esc_html__( 'رنگ خط پیشرفت', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'global'    => [ 'default' => Global_Colors::COLOR_ACCENT ],
				'selectors' => [ self::ROOT => '--tt-fill-c:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'fill_height',
			[
				'label'      => esc_html__( 'ضخامت خط پیشرفت', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 20 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 2 ],
				'selectors'  => [ self::ROOT => '--tt-fill-h:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'tab_min_width',
			[
				'label'       => esc_html__( 'حداقل عرض هر تب', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px' ],
				'range'       => [ 'px' => [ 'min' => 40, 'max' => 400 ] ],
				'default'     => [ 'unit' => 'px', 'size' => 112 ],
				'mobile_default' => [ 'unit' => 'px', 'size' => 104 ],
				'selectors'   => [ self::ROOT => '--tt-tab-min:{{SIZE}}{{UNIT}};' ],
				'description' => esc_html__( 'اگر مجموع تب‌ها از عرض بخش بیشتر شود، تایم‌لاین افقی اسکرول می‌شود.', 'timeline-tabs-el' ),
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_padding',
			[
				'label'      => esc_html__( 'فاصلهٔ داخلی تب', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .tt__tab' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'rail_bg',
				'label'    => esc_html__( 'پس‌زمینهٔ نوار تایم‌لاین', 'timeline-tabs-el' ),
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tt__rail',
			]
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------- نشانگر */

	private function register_marker_style() {
		$this->start_controls_section(
			'style_marker',
			[
				'label' => esc_html__( 'نشانگر (نقطه / آیکن)', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'marker_height',
			[
				'label'      => esc_html__( 'ارتفاع ناحیهٔ نشانگر', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 10, 'max' => 200 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 40 ],
				'selectors'  => [ self::ROOT => '--tt-marker-h:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'dot_heading',
			[
				'label' => esc_html__( 'نقطه', 'timeline-tabs-el' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_responsive_control(
			'dot_size',
			[
				'label'      => esc_html__( 'اندازه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 2, 'max' => 80 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 18 ],
				'selectors'  => [ self::ROOT => '--tt-dot-size:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'dot_size_active',
			[
				'label'      => esc_html__( 'اندازه در حالت فعال', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 2, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 34 ],
				'selectors'  => [ self::ROOT => '--tt-dot-size-a:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'dot_radius',
			[
				'label'      => esc_html__( 'گردی گوشه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [ '%' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'unit' => '%', 'size' => 50 ],
				'selectors'  => [ self::ROOT => '--tt-dot-r:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->start_controls_tabs( 'dot_colors' );

		$this->start_controls_tab( 'dot_normal', [ 'label' => esc_html__( 'عادی', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'dot_color',
			[
				'label'     => esc_html__( 'رنگ نقطه', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b3b0aa',
				'selectors' => [ self::ROOT => '--tt-dot-c:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label'     => esc_html__( 'رنگ آیکن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#b3b0aa',
				'selectors' => [ self::ROOT => '--tt-icon-c:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'ring_width',
			[
				'label'      => esc_html__( 'ضخامت هالهٔ اطراف', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 0 ],
				'selectors'  => [ self::ROOT => '--tt-ring-w:{{SIZE}}{{UNIT}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'dot_hover', [ 'label' => esc_html__( 'هاور', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'dot_color_hover',
			[
				'label'     => esc_html__( 'رنگ نقطه', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'selectors' => [ self::ROOT => '--tt-dot-c-h:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'icon_color_hover',
			[
				'label'     => esc_html__( 'رنگ آیکن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'selectors' => [ self::ROOT => '--tt-icon-c-h:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'ring_width_hover',
			[
				'label'      => esc_html__( 'ضخامت هاله', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 6 ],
				'selectors'  => [ self::ROOT => '--tt-ring-w-h:{{SIZE}}{{UNIT}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'dot_active', [ 'label' => esc_html__( 'فعال', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'dot_color_active',
			[
				'label'     => esc_html__( 'رنگ نقطه', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'global'    => [ 'default' => Global_Colors::COLOR_ACCENT ],
				'selectors' => [ self::ROOT => '--tt-dot-c-a:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'icon_color_active',
			[
				'label'     => esc_html__( 'رنگ آیکن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'selectors' => [ self::ROOT => '--tt-icon-c-a:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'ring_width_active',
			[
				'label'      => esc_html__( 'ضخامت هاله', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 40 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 7 ],
				'selectors'  => [ self::ROOT => '--tt-ring-w-a:{{SIZE}}{{UNIT}};' ],
			]
		);
		$this->add_control(
			'icon_scale_active',
			[
				'label'     => esc_html__( 'بزرگ‌نمایی آیکن فعال', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0.5, 'max' => 3, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 1.15 ],
				'selectors' => [ self::ROOT => '--tt-icon-scale-a:{{SIZE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'ring_color',
			[
				'label'     => esc_html__( 'رنگ هالهٔ اطراف نشانگر', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(122,113,80,0.14)',
				'selectors' => [ self::ROOT => '--tt-ring-c:{{VALUE}};' ],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'      => esc_html__( 'اندازهٔ آیکن', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem' ],
				'range'      => [ 'px' => [ 'min' => 8, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'  => [ self::ROOT => '--tt-icon-size:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* -------------------------------------------------------- عنوان بالایی */

	private function register_top_title_style() {
		$this->start_controls_section(
			'style_top_title',
			[
				'label' => esc_html__( 'عنوان بالایی تب', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'top_typography',
				'selector' => '{{WRAPPER}} .tt__top',
				'global'   => [ 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ],
			]
		);

		$this->add_responsive_control(
			'top_min_height',
			[
				'label'      => esc_html__( 'حداقل ارتفاع', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 120 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 36 ],
				'selectors'  => [ '{{WRAPPER}} .tt__top' => 'min-height:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'top_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [ '{{WRAPPER}} .tt__top' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->start_controls_tabs( 'top_color_tabs' );

		$this->start_controls_tab( 'top_c_normal', [ 'label' => esc_html__( 'عادی', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'top_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6f6a64',
				'selectors' => [ self::ROOT => '--tt-top-c:{{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'top_c_hover', [ 'label' => esc_html__( 'هاور', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'top_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#151515',
				'selectors' => [ self::ROOT => '--tt-top-c-h:{{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'top_c_active', [ 'label' => esc_html__( 'فعال', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'top_color_active',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#151515',
				'selectors' => [ self::ROOT => '--tt-top-c-a:{{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'top_typography_active',
				'label'    => esc_html__( 'فونت حالت فعال', 'timeline-tabs-el' ),
				'selector' => '{{WRAPPER}} .tt__tab.is-active .tt__top',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* -------------------------------------------------------- عنوان پایینی */

	private function register_bottom_title_style() {
		$this->start_controls_section(
			'style_bottom_title',
			[
				'label' => esc_html__( 'عنوان پایینی تب', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'bottom_typography',
				'selector' => '{{WRAPPER}} .tt__bottom',
			]
		);

		$this->add_responsive_control(
			'bottom_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'unit' => 'px', 'top' => 14, 'right' => 0, 'bottom' => 0, 'left' => 0, 'isLinked' => false ],
				'selectors'  => [ '{{WRAPPER}} .tt__bottom' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'bottom_wrap',
			[
				'label'         => esc_html__( 'شکستن خط', 'timeline-tabs-el' ),
				'type'          => Controls_Manager::SWITCHER,
				'return_value'  => 'normal',
				'mobile_default' => 'normal',
				'selectors'     => [ '{{WRAPPER}} .tt__bottom' => 'white-space:{{VALUE}};' ],
				'description'   => esc_html__( 'روی موبایل به‌طور پیش‌فرض روشن است تا عنوان‌های بلند از عرض تب بیرون نزنند.', 'timeline-tabs-el' ),
			]
		);

		$this->start_controls_tabs( 'bottom_color_tabs' );

		$this->start_controls_tab( 'bot_c_normal', [ 'label' => esc_html__( 'عادی', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'bottom_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#151515',
				'selectors' => [ self::ROOT => '--tt-bot-c:{{VALUE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'bot_c_hover', [ 'label' => esc_html__( 'هاور', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'bottom_color_hover',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ self::ROOT => '--tt-bot-c-h:{{VALUE}};' ],
			]
		);
		$this->add_control(
			'bottom_opacity_hover',
			[
				'label'     => esc_html__( 'شفافیت در هاور', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0, 'max' => 1, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 0.45 ],
				'selectors' => [ self::ROOT => '--tt-bot-op-h:{{SIZE}};' ],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab( 'bot_c_active', [ 'label' => esc_html__( 'فعال', 'timeline-tabs-el' ) ] );
		$this->add_control(
			'bottom_color_active',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ self::ROOT => '--tt-bot-c-a:{{VALUE}};' ],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'bottom_typography_active',
				'label'    => esc_html__( 'فونت حالت فعال', 'timeline-tabs-el' ),
				'selector' => '{{WRAPPER}} .tt__tab.is-active .tt__bottom',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* --------------------------------------------------------- پنل محتوا */

	private function register_panel_style() {
		$this->start_controls_section(
			'style_panel',
			[
				'label' => esc_html__( 'ستون‌بندی محتوا', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'panel_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ ستون‌ها', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 160 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 32 ],
				'selectors'  => [ self::ROOT => '--tt-gap:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'panel_side_fr',
			[
				'label'       => esc_html__( 'سهم ستون تصویر', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [ 'px' => [ 'min' => 0.2, 'max' => 4, 'step' => 0.05 ] ],
				'default'     => [ 'size' => 1 ],
				'selectors'   => [ self::ROOT => '--tt-side:minmax(0,{{SIZE}}fr);' ],
			]
		);

		$this->add_responsive_control(
			'panel_center_fr',
			[
				'label'     => esc_html__( 'سهم ستون متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0.2, 'max' => 6, 'step' => 0.05 ] ],
				'default'   => [ 'size' => 1.35 ],
				'selectors' => [ self::ROOT => '--tt-center:minmax(0,{{SIZE}}fr);' ],
			]
		);

		$this->add_responsive_control(
			'panel_valign',
			[
				'label'     => esc_html__( 'ترازبندی عمودی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => [
					'start'   => esc_html__( 'بالا', 'timeline-tabs-el' ),
					'center'  => esc_html__( 'میانه', 'timeline-tabs-el' ),
					'end'     => esc_html__( 'پایین', 'timeline-tabs-el' ),
					'stretch' => esc_html__( 'کشیده', 'timeline-tabs-el' ),
				],
				'selectors' => [ self::ROOT => '--tt-valign:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'     => esc_html__( 'ترازبندی متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => [
					'start'  => [ 'title' => esc_html__( 'شروع', 'timeline-tabs-el' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'میانه', 'timeline-tabs-el' ), 'icon' => 'eicon-text-align-center' ],
					'end'    => [ 'title' => esc_html__( 'پایان', 'timeline-tabs-el' ), 'icon' => 'eicon-text-align-right' ],
				],
				'selectors' => [ self::ROOT => '--tt-align:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__( 'فاصلهٔ داخلی بخش متن', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .tt__content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'stack_heading',
			[
				'label'     => esc_html__( 'شکستن چیدمان در عرض کم', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'stack_mode',
			[
				'label'        => esc_html__( 'حالت شکستن', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => '',
				'options'      => [
					''    => esc_html__( 'خودکار (بر اساس عرض ویجت)', 'timeline-tabs-el' ),
					'on'  => esc_html__( 'همیشه تک‌ستون', 'timeline-tabs-el' ),
					'off' => esc_html__( 'هرگز نشکن', 'timeline-tabs-el' ),
				],
				'prefix_class' => 'tt-stack-',
				'description'  => esc_html__( 'حالت خودکار به عرض خودِ ویجت نگاه می‌کند، نه عرض صفحه؛ پس داخل ستون‌های باریک هم درست می‌شکند.', 'timeline-tabs-el' ),
			]
		);

		$this->add_responsive_control(
			'stack_order',
			[
				'label'     => esc_html__( 'ترتیب پس از شکستن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '-1',
				'options'   => [
					'-1' => esc_html__( 'متن بالا، تصویر پایین', 'timeline-tabs-el' ),
					'0'  => esc_html__( 'ترتیب اصلی (تصویر اول)', 'timeline-tabs-el' ),
				],
				'selectors' => [ self::ROOT => '--tt-stack-order:{{VALUE}};' ],
				'condition' => [ 'stack_mode!' => 'off' ],
			]
		);

		$this->end_controls_section();
	}

	/* ----------------------------------------------------------- تصاویر */

	private function register_media_style( string $key, string $label ) {
		$sel     = '{{WRAPPER}} .tt__media--' . $key;
		$img_sel = $sel . ' img';

		$this->start_controls_section(
			'style_media_' . $key,
			[
				'label' => $label,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'media_' . $key . '_width',
			[
				'label'      => esc_html__( 'عرض', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px', 'vw' ],
				'range'      => [ '%' => [ 'min' => 10, 'max' => 100 ], 'px' => [ 'min' => 40, 'max' => 1200 ] ],
				'default'    => [ 'unit' => '%', 'size' => 100 ],
				'selectors'  => [ $sel => 'width:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'media_' . $key . '_ratio',
			[
				'label'          => esc_html__( 'نسبت ابعاد', 'timeline-tabs-el' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '13/20',
				'tablet_default' => '4/5',
				'mobile_default' => '3/4',
				'options'        => [
					''      => esc_html__( 'پیش‌فرض', 'timeline-tabs-el' ),
					'13/20' => '13:20',
					'3/4'   => '3:4',
					'4/5'   => '4:5',
					'1/1'   => '1:1',
					'4/3'   => '4:3',
					'3/2'   => '3:2',
					'16/9'  => '16:9',
					'21/9'  => '21:9',
					'auto'  => esc_html__( 'آزاد (ارتفاع دستی)', 'timeline-tabs-el' ),
				],
				'selectors'      => [ $sel => 'aspect-ratio:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'media_' . $key . '_height',
			[
				'label'      => esc_html__( 'ارتفاع ثابت', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh', 'em' ],
				'range'      => [ 'px' => [ 'min' => 60, 'max' => 1200 ], 'vh' => [ 'min' => 10, 'max' => 100 ] ],
				'selectors'  => [ $sel => 'height:{{SIZE}}{{UNIT}};aspect-ratio:auto;' ],
			]
		);

		$this->add_responsive_control(
			'media_' . $key . '_max_height',
			[
				'label'       => esc_html__( 'حداکثر ارتفاع', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', 'vh' ],
				'range'       => [ 'px' => [ 'min' => 80, 'max' => 1200 ], 'vh' => [ 'min' => 10, 'max' => 100 ] ],
				'selectors'   => [ $sel => 'max-height:{{SIZE}}{{UNIT}};' ],
				'description' => esc_html__( 'جلوی بلندشدن بیش از حد تصویرِ تمام‌عرض روی موبایل را می‌گیرد؛ تصویر از بالا و پایین برش می‌خورد.', 'timeline-tabs-el' ),
			]
		);

		$this->add_control(
			'media_' . $key . '_fit',
			[
				'label'     => esc_html__( 'نحوهٔ برش تصویر', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'cover',
				'options'   => [
					'cover'      => esc_html__( 'پرکردن کادر', 'timeline-tabs-el' ),
					'contain'    => esc_html__( 'جا شدن کامل', 'timeline-tabs-el' ),
					'fill'       => esc_html__( 'کشیده', 'timeline-tabs-el' ),
					'scale-down' => esc_html__( 'کوچک‌شده', 'timeline-tabs-el' ),
					'none'       => esc_html__( 'بدون تغییر', 'timeline-tabs-el' ),
				],
				'selectors' => [ $img_sel => 'object-fit:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'media_' . $key . '_position',
			[
				'label'     => esc_html__( 'نقطهٔ تمرکز', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center center',
				'options'   => [
					'center center' => esc_html__( 'میانه', 'timeline-tabs-el' ),
					'center top'    => esc_html__( 'بالا', 'timeline-tabs-el' ),
					'center bottom' => esc_html__( 'پایین', 'timeline-tabs-el' ),
					'left center'   => esc_html__( 'چپ', 'timeline-tabs-el' ),
					'right center'  => esc_html__( 'راست', 'timeline-tabs-el' ),
				],
				'selectors' => [ $img_sel => 'object-position:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'media_' . $key . '_bg',
			[
				'label'     => esc_html__( 'رنگ کادر خالی', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ece8e2',
				'selectors' => [ $sel => 'background-color:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'media_' . $key . '_radius',
			[
				'label'      => esc_html__( 'گردی گوشه‌ها', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ $sel => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'media_' . $key . '_border',
				'selector' => $sel,
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'media_' . $key . '_shadow',
				'selector' => $sel,
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'media_' . $key . '_filter',
				'selector' => $img_sel,
			]
		);

		$this->add_control(
			'media_' . $key . '_reveal',
			[
				'label'     => esc_html__( 'انیمیشن نمایش', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'a' === $key ? 'tt-rev-r' : 'tt-rev-l',
				'options'   => [
					'tt-rev-r' => esc_html__( 'باز شدن از راست', 'timeline-tabs-el' ),
					'tt-rev-l' => esc_html__( 'باز شدن از چپ', 'timeline-tabs-el' ),
					'tt-rev-u' => esc_html__( 'باز شدن از پایین', 'timeline-tabs-el' ),
					'tt-rev-d' => esc_html__( 'باز شدن از بالا', 'timeline-tabs-el' ),
					'tt-fade'  => esc_html__( 'محو', 'timeline-tabs-el' ),
					'tt-zoom'  => esc_html__( 'زوم', 'timeline-tabs-el' ),
					'none'     => esc_html__( 'بدون انیمیشن', 'timeline-tabs-el' ),
				],
				'selectors' => [ self::ROOT => '--tt-' . $key . '-rev:{{VALUE}};' ],
				'separator' => 'before',
			]
		);

		if ( 'b' === $key ) {
			$this->add_control(
				'media_b_delay',
				[
					'label'      => esc_html__( 'تأخیر انیمیشن', 'timeline-tabs-el' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'ms' ],
					'range'      => [ 'ms' => [ 'min' => 0, 'max' => 1500, 'step' => 20 ] ],
					'default'    => [ 'unit' => 'ms', 'size' => 80 ],
					'selectors'  => [ self::ROOT => '--tt-b-delay:{{SIZE}}ms;' ],
				]
			);
		}

		$this->end_controls_section();
	}

	/* -------------------------------------------------------- ساب عنوان */

	private function register_sub_title_style() {
		$this->start_controls_section(
			'style_sub',
			[
				'label' => esc_html__( 'ساب عنوان', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'sub_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'selectors' => [ self::ROOT => '--tt-sub-c:{{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'sub_typography',
				'selector' => '{{WRAPPER}} .tt__sub',
			]
		);

		$this->add_responsive_control(
			'sub_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [ 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 14, 'left' => 0, 'isLinked' => false ],
				'selectors'  => [ '{{WRAPPER}} .tt__sub' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------- عنوان اصلی */

	private function register_title_style() {
		$this->start_controls_section(
			'style_title',
			[
				'label' => esc_html__( 'عنوان اصلی', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [ 'default' => Global_Colors::COLOR_PRIMARY ],
				'selectors' => [ self::ROOT => '--tt-title-c:{{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .tt__title',
				'global'   => [ 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ],
			]
		);

		$this->add_responsive_control(
			'title_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [ 'unit' => 'px', 'top' => 0, 'right' => 0, 'bottom' => 18, 'left' => 0, 'isLinked' => false ],
				'selectors'  => [ '{{WRAPPER}} .tt__title' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'title_max_width',
			[
				'label'      => esc_html__( 'حداکثر عرض', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'range'      => [ 'px' => [ 'min' => 100, 'max' => 1200 ] ],
				'selectors'  => [ '{{WRAPPER}} .tt__title' => 'max-width:{{SIZE}}{{UNIT}};margin-inline:auto;' ],
			]
		);

		$this->end_controls_section();
	}

	/* ------------------------------------------------------------- متن */

	private function register_text_style() {
		$this->start_controls_section(
			'style_text',
			[
				'label' => esc_html__( 'متن محتوا', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'رنگ متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#6f6a64',
				'global'    => [ 'default' => Global_Colors::COLOR_TEXT ],
				'selectors' => [ self::ROOT => '--tt-text-c:{{VALUE}};' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .tt__text',
				'global'   => [ 'default' => Global_Typography::TYPOGRAPHY_TEXT ],
			]
		);

		$this->add_control(
			'text_heading_color',
			[
				'label'     => esc_html__( 'رنگ تیترهای داخل متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ self::ROOT => '--tt-text-h-c:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'link_color',
			[
				'label'     => esc_html__( 'رنگ لینک', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#7a7150',
				'selectors' => [ self::ROOT => '--tt-link-c:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'link_color_hover',
			[
				'label'     => esc_html__( 'رنگ لینک در هاور', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#645c40',
				'selectors' => [ self::ROOT => '--tt-link-c-h:{{VALUE}};' ],
			]
		);

		$this->add_responsive_control(
			'text_max_width',
			[
				'label'       => esc_html__( 'حداکثر عرض', 'timeline-tabs-el' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => [ 'px', '%', 'em', 'ch' ],
				'range'       => [ 'px' => [ 'min' => 120, 'max' => 1200 ], 'ch' => [ 'min' => 20, 'max' => 120 ] ],
				'default'     => [ 'unit' => 'ch', 'size' => 46 ],
				'selectors'   => [ self::ROOT => '--tt-text-mw:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_control(
			'text_center_box',
			[
				'label'     => esc_html__( 'جای کادر متن', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'auto',
				'options'   => [
					'auto' => esc_html__( 'میانه', 'timeline-tabs-el' ),
					'0'    => esc_html__( 'چسبیده به ابتدای ستون', 'timeline-tabs-el' ),
				],
				'selectors' => [ self::ROOT => '--tt-text-mi:{{VALUE}};' ],
			]
		);

		$this->add_control(
			'text_paragraph_gap',
			[
				'label'      => esc_html__( 'فاصلهٔ پاراگراف‌ها', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'em', 'px', 'rem' ],
				'range'      => [ 'em' => [ 'min' => 0, 'max' => 4, 'step' => 0.1 ] ],
				'default'    => [ 'unit' => 'em', 'size' => 1 ],
				'selectors'  => [ self::ROOT => '--tt-text-pgap:{{SIZE}}{{UNIT}};' ],
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[
				'label'      => esc_html__( 'حاشیه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ '{{WRAPPER}} .tt__text' => 'margin-top:{{TOP}}{{UNIT}};margin-bottom:{{BOTTOM}}{{UNIT}};' ],
			]
		);

		$this->end_controls_section();
	}

	/* --------------------------------------------------------- لوزی تزئینی */

	private function register_decor_style() {
		$this->start_controls_section(
			'style_decor',
			[
				'label' => esc_html__( 'لوزی تزئینی', 'timeline-tabs-el' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'show_decor',
			[
				'label'        => esc_html__( 'نمایش', 'timeline-tabs-el' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'decor_color',
			[
				'label'     => esc_html__( 'رنگ خط', 'timeline-tabs-el' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#c9c6c0',
				'selectors' => [ self::ROOT => '--tt-decor-c:{{VALUE}};' ],
				'condition' => [ 'show_decor' => 'yes' ],
			]
		);

		$this->add_control(
			'decor_size',
			[
				'label'      => esc_html__( 'اندازه', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 6, 'max' => 160 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 32 ],
				'selectors'  => [ self::ROOT => '--tt-decor-s:{{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'show_decor' => 'yes' ],
			]
		);

		$this->add_control(
			'decor_width',
			[
				'label'      => esc_html__( 'ضخامت خط', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0.5, 'max' => 12, 'step' => 0.5 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 1.5 ],
				'selectors'  => [ self::ROOT => '--tt-decor-w:{{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'show_decor' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'decor_x',
			[
				'label'      => esc_html__( 'فاصله از ابتدای بخش', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [ 'px' => [ 'min' => -200, 'max' => 600 ] ],
				'default'    => [ 'unit' => 'px', 'size' => 24 ],
				'selectors'  => [ self::ROOT => '--tt-decor-x:{{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'show_decor' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'decor_y',
			[
				'label'      => esc_html__( 'فاصله از بالا', 'timeline-tabs-el' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ '%', 'px' ],
				'range'      => [ '%' => [ 'min' => 0, 'max' => 100 ] ],
				'default'    => [ 'unit' => '%', 'size' => 58 ],
				'selectors'  => [ self::ROOT => '--tt-decor-y:{{SIZE}}{{UNIT}};' ],
				'condition'  => [ 'show_decor' => 'yes' ],
			]
		);

		$this->end_controls_section();
	}

	/* =====================================================================
	 * رندر
	 * ================================================================== */

	protected function render() {
		$s     = $this->get_settings_for_display();
		$items = ( isset( $s['timeline_items'] ) && is_array( $s['timeline_items'] ) ) ? $s['timeline_items'] : [];

		$prepared = [];

		foreach ( $items as $item ) {
			$data = $this->prepare_item( $item );
			if ( $data ) {
				$prepared[] = $data;
			}
		}

		if ( ! $prepared ) {
			return;
		}

		$count   = count( $prepared );
		$default = max( 1, min( (int) ( $s['default_index'] ?? 1 ), $count ) ) - 1;
		$uid     = 'tt-' . $this->get_id();

		$top_tag    = $this->tag( $s['tab_top_tag'] ?? 'span', 'span' );
		$bottom_tag = $this->tag( $s['tab_bottom_tag'] ?? 'span', 'span' );
		$sub_tag    = $this->tag( $s['sub_title_tag'] ?? 'p', 'p' );
		$title_tag  = $this->tag( $s['main_title_tag'] ?? 'h2', 'h2' );

		$this->add_render_attribute(
			'root',
			[
				'class'       => 'tt',
				'data-reveal' => esc_attr( $s['bottom_reveal'] ?? 'both' ),
				'data-anim'   => 'yes' === ( $s['enable_animation'] ?? 'yes' ) ? '1' : '0',
				'data-scroll' => 'yes' === ( $s['auto_scroll'] ?? 'yes' ) ? '1' : '0',
				'data-swipe'  => 'yes' === ( $s['enable_swipe'] ?? 'yes' ) ? '1' : '0',
				'data-fade'   => 'yes' === ( $s['rail_fade'] ?? 'yes' ) ? '1' : '0',
				'data-default' => (string) $default,
				'role'        => 'region',
			]
		);

		if ( ! empty( $s['aria_label'] ) ) {
			$this->add_render_attribute( 'root', 'aria-label', $s['aria_label'] );
		}

		if ( 'bottom' === ( $s['timeline_position'] ?? 'top' ) ) {
			$this->add_render_attribute( 'root', 'class', 'tt--tl-bottom' );
		}
		?>
		<div <?php $this->print_render_attribute_string( 'root' ); ?>>

			<div class="tt__rail">
				<div class="tt__track" style="--tt-count:<?php echo (int) $count; ?>">
					<span class="tt__line" aria-hidden="true"></span>
					<span class="tt__fill" aria-hidden="true"></span>

					<div class="tt__tabs" role="tablist"<?php echo empty( $s['aria_label'] ) ? '' : ' aria-label="' . esc_attr( $s['aria_label'] ) . '"'; ?>>
						<?php foreach ( $prepared as $i => $item ) : ?>
							<div class="tt__tab elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?><?php echo $i === $default ? ' is-active' : ''; ?>"
								id="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>"
								role="tab"
								aria-controls="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>"
								aria-selected="<?php echo $i === $default ? 'true' : 'false'; ?>"
								tabindex="<?php echo $i === $default ? '0' : '-1'; ?>">

								<?php if ( '' !== $item['top'] ) : ?>
									<<?php echo $top_tag; ?> class="tt__top"><?php echo esc_html( $item['top'] ); ?></<?php echo $top_tag; ?>>
								<?php endif; ?>

								<?php if ( 'none' !== $item['marker'] ) : ?>
									<span class="tt__marker">
										<?php if ( 'icon' === $item['marker'] ) : ?>
											<span class="tt__icon" aria-hidden="true"><?php Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
										<?php else : ?>
											<span class="tt__dot" aria-hidden="true"></span>
										<?php endif; ?>
									</span>
								<?php endif; ?>

								<?php if ( '' !== $item['bottom'] ) : ?>
									<<?php echo $bottom_tag; ?> class="tt__bottom"><?php echo esc_html( $item['bottom'] ); ?></<?php echo $bottom_tag; ?>>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

			<div class="tt__panels">
				<?php foreach ( $prepared as $i => $item ) : ?>
					<div class="tt__panel elementor-repeater-item-<?php echo esc_attr( $item['_id'] ); ?><?php echo $i === $default ? ' is-active' : ''; ?>"
						id="<?php echo esc_attr( $uid . '-panel-' . $i ); ?>"
						role="tabpanel"
						aria-labelledby="<?php echo esc_attr( $uid . '-tab-' . $i ); ?>"
						aria-hidden="<?php echo $i === $default ? 'false' : 'true'; ?>"
						data-cols="<?php echo esc_attr( $item['cols'] ); ?>">

						<?php if ( $item['has_a'] ) : ?>
							<figure class="tt__media tt__media--a">
								<?php echo $this->render_image( $item['image_a'], $s, 'thumb', $i === $default ); ?>
							</figure>
						<?php endif; ?>

						<?php if ( $item['has_content'] ) : ?>
							<div class="tt__content">
								<?php if ( '' !== $item['sub'] ) : ?>
									<<?php echo $sub_tag; ?> class="tt__sub"><?php echo esc_html( $item['sub'] ); ?></<?php echo $sub_tag; ?>>
								<?php endif; ?>

								<?php if ( '' !== $item['title'] ) : ?>
									<<?php echo $title_tag; ?> class="tt__title"><?php echo wp_kses_post( $item['title'] ); ?></<?php echo $title_tag; ?>>
								<?php endif; ?>

								<?php if ( $item['has_text'] ) : ?>
									<div class="tt__text"><?php echo $this->parse_text_editor( $item['text'] ); ?></div>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $item['has_b'] ) : ?>
							<figure class="tt__media tt__media--b">
								<?php echo $this->render_image( $item['image_b'], $s, 'thumb', $i === $default ); ?>
							</figure>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<?php if ( 'yes' === ( $s['show_decor'] ?? '' ) ) : ?>
				<span class="tt__decor" aria-hidden="true"></span>
			<?php endif; ?>
		</div>
		<?php
	}

	/* =====================================================================
	 * کمکی‌ها
	 * ================================================================== */

	/**
	 * آیتم را نرمال‌سازی می‌کند و در صورت خالی بودن کامل، false برمی‌گرداند.
	 *
	 * @param array $item Repeater item.
	 * @return array|false
	 */
	private function prepare_item( $item ) {
		if ( ! is_array( $item ) ) {
			return false;
		}

		$top    = trim( (string) ( $item['tab_top_title'] ?? '' ) );
		$bottom = trim( (string) ( $item['tab_bottom_title'] ?? '' ) );
		$sub    = trim( (string) ( $item['sub_title'] ?? '' ) );
		$title  = trim( (string) ( $item['main_title'] ?? '' ) );
		$text   = (string) ( $item['content_text'] ?? '' );

		$image_a = is_array( $item['image_a'] ?? null ) ? $item['image_a'] : [];
		$image_b = is_array( $item['image_b'] ?? null ) ? $item['image_b'] : [];

		$has_a = ! empty( $image_a['url'] ) || ! empty( $image_a['id'] );
		$has_b = ! empty( $image_b['url'] ) || ! empty( $image_b['id'] );

		$has_icon = ! empty( $item['tab_icon']['value'] );
		$marker   = $item['marker_type'] ?? 'auto';

		if ( 'auto' === $marker ) {
			$marker = $has_icon ? 'icon' : 'dot';
		} elseif ( 'icon' === $marker && ! $has_icon ) {
			$marker = 'dot';
		}

		$has_text    = '' !== $this->clean_editor_content( $text );
		$has_content = ( '' !== $sub ) || ( '' !== $title ) || $has_text;

		// آیتمی که هیچ محتوایی ندارد، نمایش داده نمی‌شود.
		if ( '' === $top && '' === $bottom && ! $has_content && ! $has_a && ! $has_b ) {
			return false;
		}

		if ( $has_content ) {
			$cols = ( $has_a && $has_b ) ? '3' : ( $has_a ? '2a' : ( $has_b ? '2b' : '1' ) );
		} else {
			$cols = ( $has_a && $has_b ) ? 'ii' : '1';
		}

		return [
			'_id'         => $item['_id'] ?? '',
			'top'         => $top,
			'bottom'      => $bottom,
			'marker'      => $marker,
			'icon'        => $item['tab_icon'] ?? [],
			'sub'         => $sub,
			'title'       => $title,
			'text'        => $text,
			'has_text'    => $has_text,
			'has_content' => $has_content,
			'image_a'     => $image_a,
			'image_b'     => $image_b,
			'has_a'       => $has_a,
			'has_b'       => $has_b,
			'cols'        => $cols,
		];
	}

	/** پاراگراف‌های خالی ویرایشگر را نادیده می‌گیرد. */
	private function clean_editor_content( string $html ): string {
		$html = preg_replace( '#<p>(\s|&nbsp;|<br\s*/?>)*</p>#i', '', $html );
		$stripped = trim( wp_strip_all_tags( (string) $html ) );

		if ( '' !== $stripped ) {
			return $stripped;
		}

		// محتوایی که فقط رسانه یا جدول است هم معتبر است.
		return preg_match( '#<(img|iframe|video|audio|table|svg|hr|blockquote|ul|ol)\b#i', (string) $html ) ? 'media' : '';
	}

	/** تگ‌های مجاز برای عناوین. */
	private function tag_options(): array {
		return [
			'h1'     => 'H1',
			'h2'     => 'H2',
			'h3'     => 'H3',
			'h4'     => 'H4',
			'h5'     => 'H5',
			'h6'     => 'H6',
			'div'    => 'div',
			'p'      => 'p',
			'span'   => 'span',
			'strong' => 'strong',
		];
	}

	/** اعتبارسنجی تگ خروجی. */
	private function tag( $tag, string $fallback = 'div' ): string {
		$tag = is_string( $tag ) ? strtolower( trim( $tag ) ) : '';
		return array_key_exists( $tag, $this->tag_options() ) ? $tag : $fallback;
	}

	/**
	 * خروجی تصویر با srcset و بارگذاری بهینه.
	 *
	 * @param array  $media    مقدار کنترل MEDIA.
	 * @param array  $settings تنظیمات ویجت (برای اندازهٔ تصویر).
	 * @param string $size_key نام گروه‌کنترل اندازه.
	 * @param bool   $eager    تصویر تب فعال؟
	 */
	private function render_image( array $media, array $settings, string $size_key, bool $eager ): string {
		$id  = ! empty( $media['id'] ) ? absint( $media['id'] ) : 0;
		$url = ! empty( $media['url'] ) ? $media['url'] : '';

		if ( ! $id && ! $url ) {
			return '';
		}

		$attrs = [ 'decoding' => 'async' ];

		if ( $eager ) {
			$attrs['loading']       = 'eager';
			$attrs['fetchpriority'] = 'high';
		} else {
			$attrs['loading'] = 'lazy';
		}

		if ( $id ) {
			$size = ! empty( $settings[ $size_key . '_size' ] ) ? $settings[ $size_key . '_size' ] : 'full';

			if ( 'custom' !== $size ) {
				$html = wp_get_attachment_image( $id, $size, false, $attrs );
				if ( $html ) {
					return $html;
				}
			} else {
				$src = Group_Control_Image_Size::get_attachment_image_src( $id, $size_key, $settings );
				if ( $src ) {
					$dim            = $settings[ $size_key . '_custom_dimension' ] ?? [];
					$attrs['src']   = $src;
					$attrs['alt']   = trim( wp_strip_all_tags( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ) );
					if ( ! empty( $dim['width'] ) ) {
						$attrs['width'] = absint( $dim['width'] );
					}
					if ( ! empty( $dim['height'] ) ) {
						$attrs['height'] = absint( $dim['height'] );
					}
					return $this->img_tag( $attrs );
				}
			}
		}

		$attrs['src'] = $url;
		$attrs['alt'] = isset( $media['alt'] ) ? (string) $media['alt'] : '';

		return $this->img_tag( $attrs );
	}

	private function img_tag( array $attrs ): string {
		$out = '';

		foreach ( $attrs as $key => $value ) {
			if ( 'alt' !== $key && ( '' === $value || null === $value ) ) {
				continue;
			}
			$value = ( 'src' === $key ) ? esc_url( $value ) : esc_attr( $value );
			$out  .= ' ' . esc_attr( $key ) . '="' . $value . '"';
		}

		return '<img' . $out . ' />';
	}
}
