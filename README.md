# Timeline Tabs for Elementor — تب تایم‌لاین

یک ویجت المنتور برای ساخت **تایم‌لاین تب‌دار**: یک نوار افقی از تب‌ها (سال، نقطه یا آیکن، عنوان) و زیر یا بالای آن پنل محتوا با حداکثر دو تصویر و یک بخش متن. هر بخش ویجت، تنظیمات محتوا و استایل مستقل خودش را دارد.

- نسخه: `1.1.0`
- نویسنده: [Saeed Amini](https://github.com/saeedamini144)
- Text Domain: `timeline-tabs-el`
- نیازمندی‌ها: وردپرس ۶٫۰+، المنتور ۳٫۵+، PHP ۷٫۴+ (تست‌شده تا المنتور ۴٫۳٫۲)
- بدون وابستگی: جاوااسکریپت وانیلا (بدون jQuery)، CSS خالص با متغیرهای CSS
- راست‌به‌چپ (RTL) و فارسی به‌صورت پیش‌فرض پشتیبانی می‌شود؛ LTR هم کار می‌کند.

---

## فهرست

1. [امکانات](#امکانات)
2. [نصب](#نصب)
3. [ساختار پروژه](#ساختار-پروژه)
4. [معماری و جریان کار](#معماری-و-جریان-کار)
5. [ساختار HTML خروجی](#ساختار-html-خروجی)
6. [فایل اصلی افزونه (PHP)](#فایل-اصلی-افزونه-php)
7. [کلاس ویجت (PHP)](#کلاس-ویجت-php)
8. [کنترل‌های پنل المنتور](#کنترل‌های-پنل-المنتور)
9. [منطق رندر](#منطق-رندر)
10. [جاوااسکریپت (توابع)](#جاوااسکریپت-توابع)
11. [CSS: متغیرها، کلاس‌ها و رفتار واکنش‌گرا](#css-متغیرها-کلاس‌ها-و-رفتار-واکنش‌گرا)
12. [دسترس‌پذیری](#دسترس‌پذیری)
13. [بهینه‌سازی](#بهینه‌سازی)
14. [پیش‌نمایش بدون وردپرس](#پیش‌نمایش-بدون-وردپرس)
15. [سفارشی‌سازی و توسعه](#سفارشی‌سازی-و-توسعه)

---

## امکانات

| دسته | توضیح |
|---|---|
| تب‌ها | هر آیتم ریپیتر = یک تب روی تایم‌لاین + یک پنل محتوا |
| نشانگر | نقطه، آیکن (Elementor Icons)، خودکار یا بدون نشانگر — برای هر آیتم جدا |
| چیدمان پنل | به‌صورت خودکار یک تا سه ستونه، بر اساس اینکه کدام تصویر و متن پر شده |
| تایم‌لاین | خط پایه + خط پیشرفت متحرک از اولین تب تا تب فعال |
| اسکرول افقی | اگر تب‌ها از عرض ویجت بیشتر شوند، نوار اسکرول می‌شود و لبه‌ها محو می‌شوند |
| لمسی | تعویض تب با کشیدن انگشت روی پنل (آگاه به RTL) |
| کیبورد | ← → Home End Enter Space |
| انیمیشن | باز شدن تصاویر از ۴ جهت، محو، زوم، ورود متن؛ احترام به `prefers-reduced-motion` |
| واکنش‌گرا | شکستن چیدمان بر اساس **عرض خودِ ویجت** (container query) |
| استایل | هر بخش (تب، نشانگر، عنوان‌ها، دو تصویر، متن…) سکشن استایل مستقل دارد؛ بازنویسی استایل برای هر آیتم |
| Dynamic Tags | همهٔ فیلدهای متنی و تصویری از Dynamic Content المنتور پشتیبانی می‌کنند |
| Global Colors/Fonts | رنگ‌ها و تایپوگرافی‌های کلیدی به Global های Kit المنتور وصل‌اند |

---

## نصب

1. پوشهٔ [timeline-tabs-elementor/](timeline-tabs-elementor/) را در `wp-content/plugins/` کپی کنید (یا آن را زیپ کرده و از **افزونه‌ها ← افزودن ← بارگذاری افزونه** نصب کنید).
2. افزونه را فعال کنید. اگر المنتور نصب نباشد یا نسخه‌اش پایین‌تر از ۳٫۵٫۰ باشد، یک اعلان در پیشخوان نمایش داده می‌شود و ویجت ثبت نمی‌شود.
3. در ویرایشگر المنتور، ویجت **تب تایم‌لاین** را از دستهٔ «ویجت‌های تایم‌لاین» بکشید و رها کنید.

---

## ساختار پروژه

```
timeline-tabs/
├── index.html                              پیش‌نمایش مستقل (بدون وردپرس)
├── README.md                               همین فایل
└── timeline-tabs-elementor/                خود افزونه
    ├── timeline-tabs-elementor.php         بارگذار: ثابت‌ها، بررسی المنتور، ثبت دسته/ویجت/دارایی‌ها
    ├── widgets/
    │   └── class-timeline-tabs-widget.php  کنترل‌ها و رندر ویجت (TT_Elementor\Widgets\Timeline_Tabs)
    └── assets/
        ├── css/timeline-tabs.css           استایل فرانت (مبتنی بر متغیرهای CSS)
        └── js/timeline-tabs.js             منطق تب‌ها (IIFE، بدون وابستگی)
```

---

## معماری و جریان کار

```
وردپرس: plugins_loaded
   └─ TT_Elementor_Timeline_Tabs::boot()
        ├─ المنتور فعال/نسخه کافی نیست؟ → اعلان ادمین و خروج
        ├─ elementor/elements/categories_registered → register_category()
        ├─ elementor/widgets/register               → register_widgets()
        └─ wp_enqueue_scripts + elementor/editor/after_enqueue_scripts → register_assets()
              (فقط register؛ بارگذاری واقعی را خود ویجت با *_depends انجام می‌دهد)

ویرایشگر المنتور
   └─ Timeline_Tabs::register_controls() → فرم تنظیمات + CSS variables روی .tt

رندر (فرانت و پیش‌نمایش ویرایشگر)
   └─ Timeline_Tabs::render()
        ├─ prepare_item() برای هر آیتم  → نرمال‌سازی + حذف آیتم کاملاً خالی + تعیین ستون‌ها
        └─ خروجی HTML (tablist + tabpanel ها) با data-attribute ها

مرورگر
   └─ timeline-tabs.js → init(.tt) → select(default) → رویدادها (کلیک/کیبورد/سوایپ/اسکرول/ResizeObserver)
```

اصل طراحی: **PHP فقط HTML و مقادیر CSS variable می‌سازد؛ CSS همهٔ ظاهر را از متغیرها می‌خواند؛ JS فقط وضعیت (کلاس `is-active`، aria، موقعیت خط پیشرفت، اسکرول ریل) را مدیریت می‌کند.**

---

## ساختار HTML خروجی

```html
<div class="tt" data-reveal="both" data-anim="1" data-scroll="1"
     data-swipe="1" data-fade="1" data-default="1"
     role="region" aria-label="تایم‌لاین">

  <div class="tt__rail">                         <!-- ناحیهٔ اسکرول افقی -->
    <div class="tt__track" style="--tt-count:6">
      <span class="tt__line"></span>             <!-- خط پایه -->
      <span class="tt__fill"></span>             <!-- خط پیشرفت -->
      <div class="tt__tabs" role="tablist">
        <div class="tt__tab is-active" role="tab" id="tt-ID-tab-0"
             aria-controls="tt-ID-panel-0" aria-selected="true" tabindex="0">
          <span class="tt__top">۱۳۲۷</span>
          <span class="tt__marker"><span class="tt__dot"></span></span>   <!-- یا .tt__icon -->
          <span class="tt__bottom">تأسیس کارگاه</span>
        </div>
        …
      </div>
    </div>
  </div>

  <div class="tt__panels">                       <!-- container: ttp -->
    <div class="tt__panel is-active" role="tabpanel" data-cols="3"
         id="tt-ID-panel-0" aria-labelledby="tt-ID-tab-0" aria-hidden="false">
      <figure class="tt__media tt__media--a"><img …></figure>
      <div class="tt__content">
        <p class="tt__sub">…</p>
        <h2 class="tt__title">…</h2>
        <div class="tt__text">…</div>
      </div>
      <figure class="tt__media tt__media--b"><img …></figure>
    </div>
    …
  </div>

  <span class="tt__decor"></span>                <!-- فقط اگر «لوزی تزئینی» روشن باشد -->
</div>
```

### Data attribute های ریشه (`.tt`)

| attribute | مقدار | چه کسی می‌خواند | معنی |
|---|---|---|---|
| `data-reveal` | `both` / `active` / `hover` / `always` | CSS | زمان نمایش عنوان پایینی |
| `data-anim` | `1` / `0` | JS | انیمیشن تعویض تب |
| `data-scroll` | `1` / `0` | JS | اسکرول خودکار تب انتخاب‌شده به وسط ریل |
| `data-swipe` | `1` / `0` | JS | تعویض تب با سوایپ |
| `data-fade` | `1` / `0` | JS | محو لبه‌های ریل |
| `data-default` | عدد (از ۰) | JS | اندیس تب فعال هنگام بارگذاری |

### مقدار `data-cols` روی هر پنل

| مقدار | وقتی | چیدمان |
|---|---|---|
| `3` | متن + هر دو تصویر | تصویر اول · متن · تصویر دوم |
| `2a` | متن + فقط تصویر اول | تصویر اول · متن |
| `2b` | متن + فقط تصویر دوم | متن · تصویر دوم |
| `1` | فقط متن، یا هیچ تصویری | یک ستون |
| `ii` | بدون متن، اما هر دو تصویر | دو تصویر کنار هم |

---

## فایل اصلی افزونه (PHP)

فایل: [timeline-tabs-elementor.php](timeline-tabs-elementor/timeline-tabs-elementor.php)

### ثابت‌ها

| ثابت | مقدار | کاربرد |
|---|---|---|
| `TT_EL_VERSION` | `'1.1.0'` | نسخهٔ دارایی‌ها (cache busting) |
| `TT_EL_FILE` | `__FILE__` | مسیر فایل اصلی |
| `TT_EL_PATH` | `plugin_dir_path()` | مسیر دیسک پوشهٔ افزونه |
| `TT_EL_URL` | `plugin_dir_url()` | آدرس پوشهٔ افزونه |
| `TT_EL_MIN_ELEMENTOR` | `'3.5.0'` | حداقل نسخهٔ المنتور |

### کلاس `TT_Elementor_Timeline_Tabs` (Singleton)

| متد | توضیح |
|---|---|
| `instance()` | نمونهٔ یکتا را برمی‌گرداند و می‌سازد. در انتهای فایل فراخوانی می‌شود. |
| `__construct()` | خصوصی است؛ فقط `boot` را روی `plugins_loaded` وصل می‌کند تا مطمئن باشیم المنتور لود شده. |
| `boot()` | ترجمه‌ها را لود می‌کند، وجود المنتور (`did_action('elementor/loaded')`) و حداقل نسخه (`ELEMENTOR_VERSION`) را بررسی می‌کند؛ در صورت مشکل اعلان می‌دهد و خارج می‌شود، وگرنه هوک‌های دسته، ویجت و دارایی‌ها را ثبت می‌کند. |
| `register_category($manager)` | دستهٔ `tt-widgets` با عنوان «ویجت‌های تایم‌لاین» و آیکن `eicon-time-line` را اضافه می‌کند. |
| `register_widgets($widgets_manager)` | فایل ویجت را `require_once` می‌کند و `TT_Elementor\Widgets\Timeline_Tabs` را ثبت می‌کند (ویجت فقط در صورت وجود المنتور لود می‌شود). |
| `register_assets()` | CSS و JS را با handle `tt-timeline-tabs` **ثبت** می‌کند (نه enqueue). JS در فوتر و بدون وابستگی است. روی `wp_enqueue_scripts` و ادیتور المنتور صدا زده می‌شود. |
| `notice_missing_elementor()` | اعلان هشدار «نیاز به المنتور» (فقط برای کاربری که `activate_plugins` دارد). |
| `notice_old_elementor()` | اعلان هشدار «نسخهٔ المنتور پایین است» با نمایش حداقل نسخه. |

---

## کلاس ویجت (PHP)

فایل: [class-timeline-tabs-widget.php](timeline-tabs-elementor/widgets/class-timeline-tabs-widget.php)
کلاس: `TT_Elementor\Widgets\Timeline_Tabs extends \Elementor\Widget_Base`

ثابت `ROOT = '{{WRAPPER}} .tt'` سلکتور ریشهٔ همهٔ متغیرهای CSS است.

### متدهای شناسه‌ای ویجت

| متد | مقدار |
|---|---|
| `get_name()` | `tt_timeline_tabs` (اسمی که در JS هم برای hook المنتور استفاده می‌شود) |
| `get_title()` | «تب تایم‌لاین» |
| `get_icon()` | `eicon-time-line` |
| `get_categories()` | `['tt-widgets', 'general']` |
| `get_keywords()` | کلمات جستجو (فارسی و انگلیسی) |
| `get_style_depends()` / `get_script_depends()` | `['tt-timeline-tabs']` — باعث می‌شود CSS/JS فقط در صفحه‌هایی که ویجت دارند لود شوند |
| `has_widget_inner_wrapper()` | `false` — بدون div اضافی داخل ویجت |

### متدهای ثبت کنترل (خصوصی)

`register_controls()` متدهای زیر را به‌ترتیب صدا می‌زند:

| متد | سکشن | تب پنل |
|---|---|---|
| `register_items_section()` | آیتم‌های تایم‌لاین (ریپیتر) | Content |
| `register_settings_section()` | تنظیمات عمومی | Content |
| `register_layout_style()` | چیدمان کلی | Style |
| `register_timeline_style()` | خط تایم‌لاین | Style |
| `register_marker_style()` | نشانگر (نقطه / آیکن) | Style |
| `register_top_title_style()` | عنوان بالایی تب | Style |
| `register_bottom_title_style()` | عنوان پایینی تب | Style |
| `register_panel_style()` | ستون‌بندی محتوا | Style |
| `register_media_style($key, $label)` | استایل تصویر اول (`a`) و دوم (`b`) — یک متد، دو بار با پارامتر متفاوت | Style |
| `register_sub_title_style()` | ساب عنوان | Style |
| `register_title_style()` | عنوان اصلی | Style |
| `register_text_style()` | متن محتوا | Style |
| `register_decor_style()` | لوزی تزئینی | Style |

### متدهای رندر و کمکی

| متد | امضا | توضیح |
|---|---|---|
| `render()` | `protected` | خروجی HTML ویجت (جزئیات در بخش [منطق رندر](#منطق-رندر)) |
| `prepare_item($item)` | `array\|false` | یک ردیف ریپیتر را نرمال‌سازی می‌کند؛ اگر کاملاً خالی باشد `false` برمی‌گرداند. |
| `clean_editor_content($html)` | `string` | پاراگراف‌های خالی (`<p>&nbsp;</p>`، `<p><br></p>`) را حذف می‌کند. اگر متنی نماند اما محتوای رسانه‌ای/ساختاری (`img, iframe, video, audio, table, svg, hr, blockquote, ul, ol`) باشد، رشتهٔ `'media'` برمی‌گرداند؛ وگرنه رشتهٔ خالی. |
| `tag_options()` | `array` | تگ‌های مجاز برای عنوان‌ها: `h1–h6, div, p, span, strong` |
| `tag($tag, $fallback)` | `string` | تگ ورودی را با whitelist بالا اعتبارسنجی می‌کند؛ نامعتبر → `$fallback` |
| `render_image($media, $settings, $size_key, $eager)` | `string` | تصویر را با `wp_get_attachment_image` (srcset و width/height) می‌سازد. اندازهٔ `custom` را با `Group_Control_Image_Size` می‌سازد. اگر فقط URL باشد (مثلاً Dynamic Tag) از `img_tag` استفاده می‌کند. تصویر تب فعال: `loading=eager` + `fetchpriority=high`؛ بقیه `lazy`. همه `decoding=async`. |
| `img_tag($attrs)` | `string` | تگ `<img>` را با escape درست (`esc_url` برای src، `esc_attr` برای بقیه) می‌سازد؛ attribute های خالی حذف می‌شوند ولی `alt` خالی حفظ می‌شود. |

---

## کنترل‌های پنل المنتور

### ۱) آیتم‌های تایم‌لاین (ریپیتر `timeline_items`)

عنوان هر ردیف در پنل: عنوان بالایی ← عنوان پایینی ← عنوان اصلی ← «آیتم». سه آیتم پیش‌فرض (۱۳۲۷، ۱۳۴۱، ۱۴۰۲) ساخته می‌شود. هر آیتم سه زبانه دارد:

**زبانهٔ «تب»**

| ID | نوع | توضیح |
|---|---|---|
| `tab_top_title` | Text | عنوان بالایی (مثلاً سال). خالی = رندر نمی‌شود |
| `tab_bottom_title` | Text | عنوان پایینی؛ طبق `bottom_reveal` نمایش داده می‌شود |
| `marker_type` | Select | `auto` (آیکن اگر انتخاب شده، وگرنه نقطه) / `dot` / `icon` / `none` |
| `tab_icon` | Icons | وقتی `marker_type` روی «نقطه» یا «بدون نشانگر» باشد در پنل مخفی می‌شود |

**زبانهٔ «محتوا»**

| ID | نوع |
|---|---|
| `image_a` / `image_b` | Media (Dynamic فعال) |
| `sub_title` | Text |
| `main_title` | Textarea (HTML مجاز، با `wp_kses_post` پاک‌سازی می‌شود) |
| `content_text` | WYSIWYG |

**زبانهٔ «استایل»** — فقط همان آیتم را بازنویسی می‌کند (از `{{CURRENT_ITEM}}` استفاده می‌شود):

`item_top_color`, `item_top_color_active`, `item_top_typography`, `item_bottom_color`, `item_bottom_typography`, `item_marker_color`, `item_marker_color_active`, `item_icon_size` (ریسپانسیو)، `item_sub_color`, `item_title_color`, `item_text_color`, `item_media_ratio` (ریسپانسیو، مثلاً `13/20`).

### ۲) تنظیمات عمومی

| ID | نوع | پیش‌فرض | اثر |
|---|---|---|---|
| `default_index` | Number | `1` | تب فعال در بارگذاری (از ۱). در رندر بین ۱ و تعداد آیتم‌ها محدود می‌شود. |
| `timeline_position` | Select | `top` | `top`/`bottom`؛ با `prefix_class` کلاس `tt-pos-top/bottom` روی wrapper |
| `bottom_reveal` | Select | `both` | `both` فعال+هاور، `active`، `hover`، `always` ← `data-reveal` |
| `tab_top_tag`, `tab_bottom_tag`, `sub_title_tag`, `main_title_tag` | Select | `span`, `span`, `p`, `h2` | تگ HTML هر عنوان |
| `thumb` | Image Size group | `large` | اندازهٔ فایل تصویر (کلید `thumb_size`) |
| `enable_animation` | Switcher | yes | ← `data-anim` |
| `anim_duration` | Slider (ms) | `700` | `--tt-dur` |
| `auto_scroll` | Switcher | yes | ← `data-scroll` |
| `enable_swipe` | Switcher | yes | ← `data-swipe` |
| `rail_fade` | Switcher | yes | ← `data-fade` |
| `rail_fade_size` | Slider | `32px` | `--tt-rail-fade` |
| `aria_label` | Text | «تایم‌لاین» | `aria-label` روی ریشه و `tablist` |

### ۳) کنترل‌های Style (خلاصه و متغیر CSS مربوط)

| سکشن | کنترل‌های مهم | CSS variable / سلکتور |
|---|---|---|
| چیدمان کلی | حداکثر عرض، padding، پس‌زمینه، border، radius، shadow، فاصلهٔ ریل از پنل | `.tt` مستقیم، `gap` |
| خط تایم‌لاین | رنگ/ضخامت خط، رنگ/ضخامت پیشرفت، عرض حداقل تب، padding تب، پس‌زمینهٔ ریل، پنهان‌کردن خط/پیشرفت | `--tt-line-c`, `--tt-line-h`, `--tt-fill-c`, `--tt-fill-h`, `--tt-tab-min` |
| نشانگر | ارتفاع ناحیه، اندازهٔ نقطه (عادی/فعال)، گردی، رنگ نقطه/آیکن در سه حالت، هاله، اندازه و بزرگ‌نمایی آیکن | `--tt-marker-h`, `--tt-dot-size(-a)`, `--tt-dot-r`, `--tt-dot-c(-h/-a)`, `--tt-icon-c(-h/-a)`, `--tt-ring-c/w(-h/-a)`, `--tt-icon-size`, `--tt-icon-scale-a` |
| عنوان بالایی | تایپوگرافی (+حالت فعال)، حداقل ارتفاع، margin، رنگ سه حالت | `--tt-top-c(-h/-a)` |
| عنوان پایینی | تایپوگرافی (+فعال)، margin، شکستن خط (در موبایل پیش‌فرض روشن)، رنگ سه حالت، شفافیت هاور | `--tt-bot-c(-h/-a)`, `--tt-bot-op-h` |
| ستون‌بندی | فاصلهٔ ستون‌ها، سهم ستون تصویر/متن، ترازبندی عمودی/متن، padding متن، حالت و ترتیب شکستن | `--tt-gap`, `--tt-side`, `--tt-center`, `--tt-valign`, `--tt-align`, `--tt-stack-order` |
| تصویر اول / دوم | عرض، نسبت ابعاد (تبلت `4/5`، موبایل `3/4`)، ارتفاع ثابت/حداکثر، `object-fit`/`position`، رنگ کادر خالی، radius، border، shadow، فیلتر CSS، انیمیشن نمایش، (فقط تصویر دوم) تأخیر | سلکتور `.tt__media--a/b`, `--tt-a-rev`, `--tt-b-rev`, `--tt-b-delay` |
| ساب عنوان | رنگ، تایپوگرافی، margin | `--tt-sub-c` |
| عنوان اصلی | رنگ (Global Primary)، تایپوگرافی، margin، حداکثر عرض | `--tt-title-c` |
| متن محتوا | رنگ، تایپوگرافی، رنگ تیترها و لینک (عادی/هاور)، حداکثر عرض (پیش‌فرض `46ch`)، جای کادر، فاصلهٔ پاراگراف‌ها، margin | `--tt-text-c`, `--tt-text-h-c`, `--tt-link-c(-h)`, `--tt-text-mw`, `--tt-text-mi`, `--tt-text-pgap` |
| لوزی تزئینی | نمایش، رنگ، اندازه، ضخامت، موقعیت افقی/عمودی | `--tt-decor-c/s/w/x/y` |

تقریباً همهٔ اندازه‌ها و فاصله‌ها `responsive` هستند (دسکتاپ/تبلت/موبایل).

---

## منطق رندر

`render()` به ترتیب:

1. تنظیمات را با `get_settings_for_display()` می‌گیرد (تا Dynamic Tag ها اعمال شوند).
2. هر آیتم را از `prepare_item()` عبور می‌دهد؛ آیتم‌های `false` حذف می‌شوند. اگر هیچ آیتمی نماند، چیزی چاپ نمی‌شود.
3. اندیس پیش‌فرض را محاسبه می‌کند: `max(1, min(default_index, count)) - 1`.
4. شناسهٔ یکتا `tt-{widget_id}` می‌سازد (برای `id`, `aria-controls`, `aria-labelledby`؛ چند ویجت در یک صفحه تداخل ندارند).
5. تگ‌های عنوان را با `tag()` اعتبارسنجی می‌کند.
6. attribute های ریشه (`data-*`, `role="region"`, `aria-label`, کلاس `tt--tl-bottom` در حالت پایین) را با `add_render_attribute` می‌سازد.
7. تب‌ها و سپس پنل‌ها را چاپ می‌کند. هر تب و پنل کلاس `elementor-repeater-item-{id}` دارد تا استایل اختصاصی آیتم (`{{CURRENT_ITEM}}`) اعمال شود.
8. اگر `show_decor` روشن باشد `<span class="tt__decor">` اضافه می‌شود.

### منطق `prepare_item()`

- `top`, `bottom`, `sub`, `title` با `trim` تمیز می‌شوند.
- `has_a` / `has_b`: تصویر وقتی «پر» است که `url` یا `id` داشته باشد.
- **نشانگر:** `auto` → آیکن اگر `tab_icon.value` پر باشد، وگرنه نقطه. `icon` بدون آیکن → نقطه. `dot` و `none` همان‌طور می‌مانند.
- `has_text` با `clean_editor_content()` تعیین می‌شود؛ `has_content = sub || title || has_text`.
- آیتمی که `top`، `bottom`، محتوا و هر دو تصویرش خالی باشد حذف می‌شود.
- `cols`:
  - با محتوا: هر دو تصویر → `3`، فقط اولی → `2a`، فقط دومی → `2b`، هیچ → `1`
  - بدون محتوا: هر دو تصویر → `ii`، وگرنه → `1`

### قواعد escape

| فیلد | تابع |
|---|---|
| عنوان بالایی/پایینی، ساب عنوان | `esc_html` |
| عنوان اصلی | `wp_kses_post` |
| متن WYSIWYG | `parse_text_editor` (المنتور) |
| attribute ها | `esc_attr`، آدرس‌ها `esc_url` |

---

## جاوااسکریپت (توابع)

فایل: [timeline-tabs.js](timeline-tabs-elementor/assets/js/timeline-tabs.js) — یک IIFE با `'use strict'`.

### توابع سطح ماژول

| تابع | توضیح |
|---|---|
| `init(root)` | یک نمونهٔ `.tt` را راه‌اندازی می‌کند. **Idempotent** است: با `root.ttReady` جلوی راه‌اندازی دوباره (مثلاً در ادیتور المنتور) را می‌گیرد. اگر تبی نباشد خارج می‌شود. |
| `initAll(scope)` | همهٔ `.tt` های داخل `scope` (پیش‌فرض `document`) را `init` می‌کند. |

### توابع داخل `init`

| تابع | توضیح |
|---|---|
| `canAnimate()` | `true` اگر `data-anim="1"` و کاربر `prefers-reduced-motion` نخواسته باشد. |
| `smooth()` | `'smooth'` یا (با reduced-motion) `'auto'` برای رفتار اسکرول. |
| `centerOf(tab)` | مرکز افقی نشانگر تب را نسبت به `.tt__track` برمی‌گرداند (اگر نشانگر نبود، خود تب). |
| `measureLine()` | ارتفاع خط را از مرکز **اولین نشانگر موجود** حساب و در `--tt-line-top` ست می‌کند؛ خط با هر فونت و اندازه‌ای دقیقاً از وسط نقطه‌ها می‌گذرد. |
| `placeFill()` | `left` و `width` خط پیشرفت را از مرکز اولین تب تا مرکز تب فعال تنظیم می‌کند (به همین دلیل در RTL هم درست است). |
| `edges()` | بررسی می‌کند ریل از عرض بیشتر است یا نه و کلاس‌های `is-fade`, `has-prev`, `has-next` را روی `.tt__rail` می‌گذارد (محاسبهٔ هندسی، مستقل از RTL). |
| `refresh()` | `measureLine` + `placeFill` + `edges` را در یک `requestAnimationFrame` (با جلوگیری از صف‌شدن چندباره) اجرا می‌کند. |
| `centerTab(index, behavior)` | فقط ریل را اسکرول می‌دهد تا تب در وسط قرار بگیرد؛ `scrollIntoView` استفاده نمی‌شود تا صفحه عمودی نپرد. اگر ریل اسکرول نمی‌خواهد، کاری نمی‌کند. |
| `promoteImages(panel)` | تصاویر `loading="lazy"` پنل را به `eager` تغییر می‌دهد. |
| `select(index, userAction)` | هستهٔ ویجت: اندیس نامعتبر یا تکراری را رد می‌کند؛ `is-active`, `aria-selected`, `tabindex`, `aria-hidden` را برای همهٔ تب‌ها/پنل‌ها ست می‌کند؛ تصاویر پنل فعال را eager می‌کند (و اگر انتخاب از طرف کاربر بود، تصاویر پنل قبلی و بعدی را هم)؛ انیمیشن را با حذف و افزودن مجدد `is-anim` (و `offsetWidth` برای ری‌استارت) اجرا می‌کند؛ `refresh` را صدا می‌زند و در صورت `userAction` + `data-scroll` تب را وسط ریل می‌آورد. اولین انتخاب (بارگذاری) بدون انیمیشن است. |
| `indexOfTab(node)` | نزدیک‌ترین `.tt__tab` والد عنصر را پیدا می‌کند و اندیسش را برمی‌گرداند (`-1` در صورت نبود). |
| `step(dir)` | تب بعدی (`+1`) یا قبلی (`-1`) را انتخاب می‌کند، به‌صورت چرخشی (آخر → اول). |

### رویدادها

| رویداد | رفتار |
|---|---|
| `click` روی `.tt__tabs` | event delegation؛ تب کلیک‌شده فعال می‌شود |
| `keydown` روی `.tt__tabs` | `ArrowLeft/Right` (جهت‌ها در RTL معکوس می‌شود)، `Home`، `End`، `Enter`/`Space`. تب جدید focus می‌گیرد با `preventScroll` در صورت فعال بودن اسکرول خودکار |
| `touchstart` / `touchend` روی `.tt__panels` | سوایپ: فقط تک‌انگشتی؛ حداقل ۴۵px افقی؛ افقی ≥ ۱٫۶× عمودی؛ حداکثر ۷۰۰ms؛ داخل `table`/`pre` متن نادیده گرفته می‌شود؛ جهت با RTL تنظیم می‌شود |
| `scroll` روی `.tt__rail` | به‌روزرسانی محو لبه‌ها با `edges` |
| `ResizeObserver` روی track و rail | `refresh` (در نبود آن: `resize` و `orientationchange` روی window) |
| `document.fonts.ready` | بعد از لود فونت‌ها دوباره اندازه‌گیری و تب فعال وسط‌چین می‌شود |

### راه‌اندازی اولیه

1. `select(data-default, false)` تب پیش‌فرض را فعال می‌کند.
2. در صورت `data-scroll`، یک فریم بعد تب فعال بدون انیمیشن وسط ریل می‌آید (برای موبایل با تب‌های زیاد).

### یکپارچگی با المنتور و API عمومی

- `elementor/frontend/init` → hook `frontend/element_ready/tt_timeline_tabs.default` برای راه‌اندازی ویجت هنگام افزودن/ویرایش در ادیتور.
- `window.ttTimelineTabs = { init, initAll }` — برای راه‌اندازی دستی، مثلاً بعد از تزریق دینامیک HTML:

```js
// بعد از افزودن HTML ویجت به DOM با AJAX
window.ttTimelineTabs.initAll(document.querySelector('#container'));
```

---

## CSS: متغیرها، کلاس‌ها و رفتار واکنش‌گرا

فایل: [timeline-tabs.css](timeline-tabs-elementor/assets/css/timeline-tabs.css)

### کلاس‌های ساختاری

| کلاس | نقش |
|---|---|
| `.tt` | ریشه (flex ستونی) |
| `.tt__rail` / `.tt__track` | ناحیهٔ اسکرول و بستر تب‌ها؛ `min-width: max(100%, count × tab-min)` |
| `.tt__line` / `.tt__fill` | خط پایه و خط پیشرفت |
| `.tt__tab` / `.tt__top` / `.tt__marker` / `.tt__dot` / `.tt__icon` / `.tt__bottom` | اجزای تب |
| `.tt__panels` / `.tt__panel` | ظرف پنل‌ها (container query) و پنل‌ها (فقط پنل `is-active` به‌صورت grid نمایش داده می‌شود) |
| `.tt__media--a/b` / `.tt__content` / `.tt__sub` / `.tt__title` / `.tt__text` | محتوای پنل |
| `.tt__decor` | لوزی تزئینی |

### کلاس‌های وضعیت

`is-active` (تب/پنل فعال)، `is-anim` (اجرای انیمیشن ورود)، `is-fade` / `has-prev` / `has-next` (محو لبهٔ ریل).

### کلاس‌های تنظیمات (روی wrapper المنتور)

| کلاس | منبع |
|---|---|
| `tt-pos-top` / `tt-pos-bottom` | `timeline_position` |
| `tt-stack-on` / `tt-stack-off` | `stack_mode` (خالی = خودکار) |
| `tt--tl-bottom` | روی خود `.tt` در حالت تایم‌لاین پایین |

### متغیرهای CSS (نمونه)

همهٔ مقادیر، پیش‌فرض‌شان را در بلوک `.tt{…}` ابتدای CSS دارند و کنترل‌های المنتور آن‌ها را بازنویسی می‌کنند. می‌توانید در CSS سفارشی هم دست بزنید:

```css
.my-timeline .tt{
  --tt-dot-c-a:#d33;     /* رنگ نقطهٔ فعال */
  --tt-fill-h:4px;       /* ضخامت خط پیشرفت */
  --tt-tab-min:140px;    /* حداقل عرض هر تب */
}
```

### رفتار واکنش‌گرا

شکستن چیدمان با **container query** روی `.tt__panels` (`container-name: ttp`) انجام می‌شود، یعنی بر اساس عرض خودِ ویجت و نه صفحه:

| عرض ویجت | رفتار |
|---|---|
| ≤ ۸۸۰px | حالت `3`: متن تمام‌عرض بالا، دو تصویر کنار هم زیر آن. حالت‌های `2a`/`2b`: تک‌ستون با ترتیب `--tt-stack-order` |
| ≤ ۵۶۰px | حالت‌های `3` و `ii` هم تک‌ستون می‌شوند |

- برای مرورگرهای بدون container query (`@supports not`) همین قواعد با بریک‌پوینت‌های المنتور (۱۰۲۴ و ۷۶۷) تکرار شده‌اند.
- `tt-stack-on` همیشه تک‌ستون می‌کند و `tt-stack-off` چیدمان اصلی را حفظ می‌کند.
- حالت‌های `:hover` داخل `@media (hover:hover) and (pointer:fine)` هستند؛ روی لمسی `data-reveal="hover"` مثل «فقط فعال» رفتار می‌کند.
- زیر ۱۰۲۴px لوزی تزئینی پنهان می‌شود.

### انیمیشن‌ها

`tt-rev-r/l/u/d` (باز شدن با `clip-path` از راست/چپ/پایین/بالا)، `tt-fade`، `tt-zoom` برای تصاویر؛ `tt-rise` با تأخیر پلکانی (۰/۵۰/۱۰۰/۱۵۰ms) برای عناصر متن. با `prefers-reduced-motion: reduce` همهٔ انیمیشن‌ها و transition ها عملاً حذف می‌شوند.

---

## دسترس‌پذیری

- الگوی کامل `tablist` / `tab` / `tabpanel` با `aria-selected`, `aria-controls`, `aria-labelledby`, `aria-hidden`.
- Roving tabindex: فقط تب فعال `tabindex=0` دارد.
- پیمایش با کیبورد: ← → (آگاه به RTL)، `Home`، `End`، `Enter`، `Space`.
- `:focus-visible` حلقهٔ فوکوس روی نشانگر نشان می‌دهد.
- ریشه `role="region"` با `aria-label` قابل تنظیم است.
- نشانگرها و خطوط تزئینی `aria-hidden="true"` هستند.

---

## بهینه‌سازی

- CSS/JS فقط در صفحه‌هایی که ویجت دارند بارگذاری می‌شوند (`get_*_depends`).
- JS بدون وابستگی و بدون jQuery؛ یک شنوندهٔ کلیک برای همهٔ تب‌ها؛ `ResizeObserver` به‌جای `resize`؛ اندازه‌گیری‌ها داخل `requestAnimationFrame` و بدون صف‌شدن تکراری.
- تصاویر با `wp_get_attachment_image` (srcset، width/height)؛ تصویر تب فعال `eager`+`fetchpriority=high` و بقیه `lazy`؛ تصاویر تب‌های همسایه بعد از اولین تعامل پیش‌بارگذاری می‌شوند.
- همهٔ پنل‌ها در HTML حاضرند (مناسب سئو)؛ تعویض تب بدون درخواست شبکه.
- `has_widget_inner_wrapper() = false` برای DOM سبک‌تر.
- ظاهر کامل روی CSS variables است و خروجی CSS تولیدی المنتور کوتاه می‌ماند.

---

## پیش‌نمایش بدون وردپرس

فایل [index.html](index.html) در ریشهٔ پروژه، همان HTML خروجی ویجت را با همین CSS/JS نمایش می‌دهد؛ فقط کافی است در مرورگر باز شود.

- سه قاب با عرض‌های **کامل / ۸۲۰px / ۳۹۰px** رندر می‌شوند و لبهٔ هر قاب با `resize: horizontal` قابل کشیدن است؛ پس شکستن چیدمان را می‌شود زنده دید.
- داده‌های نمونه در آرایهٔ `DATA` هستند (فیلد `cols` یکی از `3`, `2a`, `2b`, `1`, `ii`).
- تابع `img(seed, cls)` تصاویر نمونه را از `picsum.photos` می‌سازد.
- تابع `build(host, uid)` قالب `<template id="tpl">` را کپی می‌کند، تب‌ها و پنل‌ها را از `DATA` می‌سازد (با همان ساختار و aria خروجی PHP) و داخل `host` می‌گذارد. در انتها `timeline-tabs.js` همهٔ `.tt` ها را راه‌اندازی می‌کند.

> برای نمایش تصاویر نمونه به اینترنت نیاز است.

---

## سفارشی‌سازی و توسعه

**افزودن یک کنترل استایل جدید** — در متد `register_*_style` مربوط یک کنترل با `selectors` بنویسید و ترجیحاً به‌جای property مستقیم، متغیر CSS ست کنید:

```php
$this->add_control( 'my_color', [
    'label'     => esc_html__( 'رنگ', 'timeline-tabs-el' ),
    'type'      => Controls_Manager::COLOR,
    'selectors' => [ self::ROOT => '--tt-my-c:{{VALUE}};' ],
] );
```

سپس در CSS از `var(--tt-my-c, <پیش‌فرض>)` استفاده کنید.

**افزودن نوع پنل جدید** — مقدار جدید `cols` را در `prepare_item()` تولید کنید و `grid-template-columns` آن را در CSS (به‌همراه بخش‌های container query) اضافه کنید.

**تغییر نقطه‌های شکست** — مقدارهای `880px` و `560px` در `@container ttp (...)` و بریک‌پوینت‌های `@supports not` را در CSS تغییر دهید.

**فراخوانی دستی JS** — `window.ttTimelineTabs.init(element)` یا `initAll(scope)`.

---

## مجوز

مشخص نشده — در صورت انتشار عمومی، یک فایل `LICENSE` اضافه کنید.
