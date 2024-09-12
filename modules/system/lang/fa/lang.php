<?php

return [
    'app' => [
        'name' => 'مدیریت محتوی',
        'tagline' => 'ورود به پنل مدیریت',
    ],
    'locale' => [
        'ar' => 'العربية',
        'be' => 'Беларуская',
        'bg' => 'Български',
        'ca' => 'Català',
        'cs' => 'Čeština',
        'da' => 'Dansk',
        'en' => 'English (United States)',
        'en-au' => 'English (Australia)',
        'en-ca' => 'English (Canada)',
        'en-gb' => 'English (United Kingdom)',
        'et' => 'Eesti',
        'de' => 'Deutsch',
        'el' => 'Ελληνικά',
        'es' => 'Español',
        'es-ar' => 'Español (Argentina)',
        'fa' => 'فارسی',
        'fr' => 'Français',
        'fr-ca' => 'Français (Canada)',
        'hu' => 'Magyar',
        'id' => 'Bahasa Indonesia',
        'it' => 'Italiano',
        'ja' => '日本語',
        'kr' => '한국어',
        'lt' => 'Lietuvių',
        'lv' => 'Latviešu',
        'nb-no' => 'Norsk (Bokmål)',
        'nl' => 'Nederlands',
        'pl' => 'Polski',
        'pt-br' => 'Português (Brasil)',
        'pt-pt' => 'Português (Portugal)',
        'ro' => 'Română',
        'rs' => 'Srpski',
        'ru' => 'Русский',
        'fi' => 'Suomi',
        'sv' => 'Svenska',
        'sk' => 'Slovenský',
        'sl' => 'Slovenščina',
        'th' => 'ไทย',
        'tr' => 'Türkçe',
        'uk' => 'Українська мова',
        'zh-cn' => '简体中文',
        'zh-tw' => '繁體中文',
        'vn' => 'Tiếng việt',
    ],
    'directory' => [
        'create_fail' => 'مشکلی در ایجاد پوشه ی :name به وجود آمده است',
    ],
    'file' => [
        'create_fail' => 'مشکلی در ایجاد فایل :name به وجود آمده است',
    ],
    'combiner' => [
        'not_found' => "فایل ترکیب کننده ':name' یافت نشد.",
    ],
    'system' => [
        'name' => 'سیستم',
        'menu_label' => 'سیستم',
        'categories' => [
            'cms' => 'مدیریت محتوی',
            'misc' => 'متفرقه',
            'logs' => 'وقایع',
            'mail' => 'پست الکترونیکی',
            'shop' => 'فروشگاه',
            'team' => 'تیم',
            'users' => 'کاربران',
            'system' => 'سیستم',
            'social' => 'شبکه اجتماعی',
            'backend' => 'بخش مدیریت',
            'events' => 'رویداد ها',
            'customers' => 'مشتریان',
            'my_settings' => 'تنظیمات من',
            'notifications' => 'اطلاعیه ها',
        ],
    ],
    'theme' => [
        'label' => 'قالب',
        'unnamed' => 'قالب بدون نام',
        'name' => [
            'label' => 'نام قالب',
            'help' => 'یک اسم یکتا برای نام قالب برای مثال Winter.Vanilla',
        ],
    ],
    'themes' => [
        'install' => 'نصب قالب جدید',
        'search' => 'جستجوی قالب برای نصب',
        'installed' => 'قالب های نصب شده',
        'no_themes' => 'هیچ قالبی یافت نشد',
        'recommended' => 'پیشنهادی',
        'remove_confirm' => 'آیا از حذف این قالب اطمینان دارید؟',
    ],
    'plugin' => [
        'label' => 'افزونه',
        'unnamed' => 'افزونه بدون نام',
        'name' => [
            'label' => 'نام افزونه',
            'help' => 'یک اسم یکتا برای نام افزونه برای مثال: Winter.Blog',
        ],
        'by_author' => 'توسط :name',
    ],
    'plugins' => [
        'manage' => 'مدیریت افزونه ها',
        'install' => 'نصب افزونه جدید',
        'install_products' => 'نصب محصولات',
        'search' => 'جستجوی افزونه جهت نصب',
        'installed' => 'افزونه های نصب شده',
        'no_plugins' => 'هیچ افزونه ای نصب نشده است.',
        'recommended' => 'پیشنهادی',
        'plugin_label' => 'افزونه',
        'unknown_plugin' => 'افزونه از فایل های سیستمی حذف شده است.',
        'select_label' => 'انتخاب عملکرد...',
        'bulk_actions_label' => 'اعمال عملکردها',
        'check_yes' => 'بله',
        'check_no' => 'خیر',
        'unfrozen' => 'به روزرسانی فعال است.',
        'enabled' => 'افزونه فعال است.',
        'freeze' => 'غیرفعال سازی به روزرسانی',
        'unfreeze' => 'فعال سازی به روزرسانی',
        'enable' => 'فعال',
        'disable' => 'غیرفعال',
        'refresh' => 'بازیابی',
        'remove' => 'حذف',
        'freeze_label' => 'بارگذاری مجدد',
        'unfreeze_label' => 'غیر فعال سازی به روزرسانی',
        'enable_label' => 'افزونه های فعال',
        'disable_label' => 'افزونه های غیرفعال',
        'refresh_label' => 'بازسازی اطلاعات افزونه',
        'action_confirm' => 'آیا مطمئین هستید می خواهید  :action را در مورد این افزونه اعمال کنید؟',
        'freeze_success' => 'به روزرسانی با موفقیت برای افزونه های انتخاب شده غیرفعال شد.',
        'unfreeze_success' => 'به روزرسانی با موفقیت برای افزونه های انتخاب شده فعال شد.',
        'enable_success' => 'افزونه انتخاب شده با موفقیت فعال شد.',
        'disable_success' => 'افزونه انتخاب شده با موفقیت غیرفعال شد.',
        'refresh_confirm' => 'مطمئن هستید می خواهید افزونه را بازنشانی کنید؟ با این کار داده های هر افزونه بازنشانی می شود و به حالت نصب اولیه باز می گردد.',
        'refresh_success' => 'افزونه انتخاب شده با موفقیت بازنشانی شد.',
        'remove_confirm' => 'آیا از حذف این افزونه اطمینان دارید؟',
        'remove_success' => "افزونه ها با موفقیت از سیستم حذف شدند.",
        'replace' => [
            'multi_install_error' => 'جایگزینی چندین افزونه در حال حاضر پشتیبانی نمی شود',
        ],
    ],
    'project' => [
        'name' => 'پروژه',
        'owner_label' => 'صاحب امتیاز',
        'attach' => 'افرودن به پروژه',
        'detach' => 'حذف از پروژه',
        'none' => 'هیچ',
        'id' => [
            'label' => 'مشخصه ی پروژه',
            'help' => 'مشخصه ی پروژه ی خود را چگونه بیابید',
            'missing' => 'لطفا مشخصه ی پروژه ی خود را وارد نمایید.',
        ],
        'detach_confirm' => 'آیا از حذف این پروژه اطمینان دارید؟',
        'unbind_success' => 'پروژه با موفقیت پاک شد.',
    ],
    'settings' => [
        'menu_label' => 'تنظیمات',
        'not_found' => 'تنظیمات مورد نظر یافت نشد.',
        'missing_model' => 'صفحه تنظیمات شامل یک مدل تعریف نشده می باشد.',
        'update_success' => 'تنظیمات برای :name با موفقیت تغییر یافت.',
        'return' => 'بازگشت به تنظیمات سیستم',
        'search' => 'جستجو',
    ],
    'mail' => [
        'log_file' => 'فایل گزارش',
        'menu_label' => 'تنظیمات پست الکترونیکی',
        'menu_description' => 'مدیریت تنظیمات پست الکترونیکی.',
        'general' => 'عمومی',
        'method' => 'روش ارسال',
        'sender_name' => 'نام ارسال کننده',
        'sender_email' => 'پست الکترونیکی ارسال کننده',
        'php_mail' => 'تابع داخلی PHP',
        'smtp' => 'SMTP',
        'smtp_address' => 'آدرس SMTP',
        'smtp_authorization' => 'مجوز اتصال به SMTP مورد نیاز است',
        'smtp_authorization_comment' => 'این گزینه را جهت اتصال به سرور SMTP در صورت نیاز فعال نمایید.',
        'smtp_username' => 'نام کاربری',
        'smtp_password' => 'کلمه عبور',
        'smtp_port' => 'درگاه SMTP',
        'sendmail' => 'Sendmail',
        'sendmail_path' => 'آدرس برنامه Sendmail',
        'sendmail_path_comment' => 'لطفا محل ذخیره نرم افزار sendmail را مشخص نمایید.',
    ],
    'mail_templates' => [
        'menu_label' => 'قالب های نامه الکترونیکی',
        'menu_description' => 'مدیریت و تغییر قالب های نامه الکترونیکی ای که برای کاربران و مدیران ارسال می شود، مدیریت طرح بندی نامه الکترونیکی.',
        'new_template' => 'قالب جدید',
        'new_layout' => 'طرح بندی ی جدید',
        'new_partial' => 'بخش جدید',
        'template' => 'قالب',
        'templates' => 'قالب ها',
        'partial' => 'بخش',
        'partials' => 'بخش ها',
        'menu_layouts_label' => 'طرح بندی نامه الکترونیکی',
        'menu_partials_label' => 'بخش های پست الکترونیکی',
        'layout' => 'طرح بندی',
        'layouts' => 'طرح بندی ها',
        'no_layout' => '-- بدون طرح بندی --',
        'name' => 'نام',
        'name_comment' => 'نام یکتای مشخص کننده ی این قالب',
        'code' => 'کد',
        'code_comment' => 'کد یکتای مشخص کننده ی این قالب',
        'subject' => 'موضوع',
        'subject_comment' => 'موجوع نامه الکترونیکی',
        'description' => 'توضیحات',
        'content_html' => 'اچ تی ام ال',
        'content_css' => 'شیوه نامه',
        'content_text' => 'متن معمولی',
        'test_send' => 'ارسال پیغام آزمایشی',
        'test_success' => 'پیغام آزمایشی ارسال شد.',
        'test_confirm' => 'پیغام آزمایشی به آدرس :email ارسال خواهد شد آیا میخواهید ادامه دهید؟',
        'creating' => 'درحال ایجاد قالب...',
        'creating_layout' => 'در حال ایجاد طرح بندی...',
        'saving' => 'ذخیره سازی قالب...',
        'saving_layout' => 'درحال ذخیره سازی طرح بندی...',
        'delete_confirm' => 'آیا از حذف این قالب اطمینان دارید؟',
        'delete_layout_confirm' => 'آیا از حذف این طرح بندی اطمینان دارید؟',
        'deleting' => 'درحال حذف قالب...',
        'deleting_layout' => 'در حال حذف طرح بندی...',
        'sending' => 'ارسال نامه الکترونیکی آزمایشی...',
        'return' => 'بازگشت به لیست قالب ها',
        'options' => 'گزینه ها',
        'disable_auto_inline_css' => 'غیر فعال کردن خودکار css inline',
    ],
    'mail_brand' => [
        'menu_label' => 'پست الکترونیکی',
        'menu_description' => 'تغییر رنگ بندی و تنظیمات قالب پست الکترونیکی.',
        'page_title' => 'شخصی سازی ظاهر پست الکترونیکی',
        'sample_template' => [
            'heading' => 'عنوان',
            'paragraph' => 'در این پاراگراف متن لورم ایپسوم و لینک قرار میگیرد.',
            'table' => [
                'item' => 'مورد',
                'description' => 'توضیحات',
                'price' => 'قیمت',
                'centered' => 'وسط چین',
                'right_aligned' => 'راست چین',
            ],
            'buttons' => [
                'primary' => 'دکمه اصلی',
                'positive' => 'دکمه مثبت',
                'negative' => 'دکمه منفی',
            ],
            'panel' => 'این پنل چگونه است؟',
            'more' => 'متن اضافی',
            'promotion' => 'کد کپن تخفیف: WINTER',
            'subcopy' => 'این یک کپی از پست الکترونیکی می باشد.',
            'thanks' => 'با تشکر',
        ],
        'fields' => [
            '_section_background' => 'پس زمینه',
            'body_bg' => 'پس زمینه اصلی',
            'content_bg' => 'پس زمینه متن',
            'content_inner_bg' => 'پس زمینه متن داخلی',
            '_section_buttons' => 'دکمه ها',
            'button_text_color' => 'رنگ متن دکمه',
            'button_primary_bg' => 'پس رمینه دکمه اصلی',
            'button_positive_bg' => 'پس زمینه دکمه مثبت',
            'button_negative_bg' => 'پس رمینه دکمه منفی',
            '_section_type' => 'تایپوگرافی',
            'header_color' => 'رنگ تیتر',
            'heading_color' => 'رنگ تیتر ها',
            'text_color' => 'رنگ متن',
            'link_color' => 'رنگ لینک',
            'footer_color' => 'رنگ پاورقی',
            '_section_borders' => 'حاشیه ها',
            'body_border_color' => 'رنگ حاشیه اصلی',
            'subcopy_border_color' => 'رنگ حاشیه کپی',
            'table_border_color' => 'رنگ حاشیه جدول',
            '_section_components' => 'اجزاء',
            'panel_bg' => 'پس رمینه پنل',
            'promotion_bg' => 'پس زمینه شاخص',
            'promotion_border_color' => 'رنگ حاشیه شاخص',
        ],
    ],
    'install' => [
        'project_label' => 'ضمیمه کردن به نرم افزار',
        'plugin_label' => 'نصب افزونه',
        'theme_label' => 'نصب قالب',
        'missing_plugin_name' => 'لطفا نام افزونه را جهت نصب وارد نمایید.',
        'missing_theme_name' => 'لطفا نام قالب را جهت نصب وارد نمایید.',
        'install_completing' => 'مرحله ی پایانی عملیات نصب',
        'install_success' => 'افزونه با موفقیت نصب شد.',
    ],
    'updates' => [
        'title' => 'مدیریت بروز رسانی',
        'name' => 'بروز رسانی نرم افزار',
        'menu_label' => 'بروز رسانی ها',
        'menu_description' => 'به روز رسانی ی سیستم، مدیریت افزونه ها و قالب های نصب شده.',
        'return_link' => 'بازگشت به صفحه به روز رسانی ها',
        'check_label' => 'بررسی بروز رسانی',
        'retry_label' => 'تلاش مجدد',
        'plugin_name' => 'نام',
        'plugin_code' => 'کد یکتا',
        'plugin_description' => 'توضیحات',
        'plugin_version' => 'نسخه',
        'plugin_author' => 'تولید کننده',
        'plugin_not_found' => 'افزونه یافت شد',
        'plugin_version_not_found' => 'نسخه افزونه یافت نشد',
        'core_current_build' => 'نسخه ی کنونی',
        'core_view_changelog' => 'مشاهده تغییرات',
        'core_build' => 'نسخه ی :build',
        'core_build_help' => 'به روز رسانی جدید موجود است',
        'core_downloading' => 'دریافت فایل های نرم افزار',
        'core_extracting' => 'گشودن فایل های نرم افزار',
        'core_set_build' => 'شماره ساخت تنظیمات',
        'update_warnings_title' => 'یک سری مشکلاتی که شناسایی شده و نیاز به توجه دارند:',
        'update_warnings_plugin_missing' => 'افزونه  :parent_code برای  :code قبل از استفاده نیاز به نصب دارد ',
        'update_warnings_plugin_replace' => 'افزونه  :plugin جایگزین :alias می شود, خواهشمند است :alias را جهت جلوگیری از هرگونه تداخل حذف کنید',
        'update_warnings_plugin_replace_cli' => 'افزونه جایگزین  :alias شده است , لطفا  :alias را جهت جلوگیری از هرگونه تداخل حذف کنید',
        'changelog' => 'تغییرات',
        'changelog_view_details' => 'مشاهده جزئیات',
        'plugins' => 'افرونه ها',
        'themes' => 'قالب ها',
        'disabled' => 'غیر فعال شده',
        'plugin_downloading' => 'دریافت افزونه: :name',
        'plugin_extracting' => 'گشودن افزونه: :name',
        'plugin_version_none' => 'افزونه ی جدید',
        'plugin_current_version' => 'نسخه کنونی',
        'theme_new_install' => 'قالب جدید نصب شد.',
        'theme_downloading' => 'دریافت قالب: :name',
        'theme_extracting' => 'گشودن قالب: :name',
        'update_label' => 'بروز رسانی نرم افزار',
        'update_completing' => 'اعمال بروز رسانی',
        'update_loading' => 'بارگزاری بروز رسانی های موجود...',
        'update_success' => 'بروز رسانی با موفقیت انجام شد.',
        'update_failed_label' => 'بروز رسانی موفق نبود',
        'force_label' => 'اصرار در بروز رسانی',
        'found' => [
            'label' => 'بروز رسانی جدید وجود دارد',
            'help' => 'بر روی بروز رسانی نرم افزار جهت شروع عملیات بروز رسانی کلیک کنید.',
        ],
        'none' => [
            'label' => 'شما از آخرین نسخه استفاده می کنید',
            'help' => 'هیچ به روز رسانی یافت نشد.',
        ],
        'important_action' => [
            'empty' => 'اقدام مورد نظر را انتخاب کنید',
            'confirm' => 'تایید به روز رسانی',
            'skip' => 'این افزونه را به روز نکن (فقط یک بار)',
            'ignore' => 'این افزونه را به روز نکن (همیشه)',
        ],
        'important_action_required' => 'انتخاب عملیات مورد نیاز است',
        'important_view_guide' => 'نمایش راهنمای به روز رسانی',
        'important_view_release_notes' => 'مشاهده یاداشت های انتشار',
        'important_alert_text' => 'برخی از به روز رسانی ها به تایید شما نیاز دارند',
        'details_title' => 'مشخصات افزونه',
        'details_view_homepage' => 'نمایش صفحه اصلی',
        'details_readme' => 'مستندات',
        'details_readme_missing' => 'مستنداتی در دسترس نمی باشد',
        'details_changelog' => 'تغییرات',
        'details_changelog_missing' => 'لیست تغییرات موجود نمی باشد.',
        'details_upgrades' => 'راهنمای به روز رسانی',
        'details_upgrades_missing' => 'راهنمایی جهت به روز رسانی در دسترس نیست.',
        'details_licence' => 'گواهی نامه',
        'details_licence_missing' => 'هیچ گواهی نامه ای موجود نیست.',
        'details_current_version' => 'نسخه کنونی',
        'details_author' => 'نویسنده',
    ],
    'server' => [
        'connect_error' => 'خطا در برقراری ارتباط با سرور.',
        'response_not_found' => 'سرور به روز رسانی یافت نشد.',
        'response_invalid' => 'پاسخ نا معتبر از سوی سرور.',
        'response_empty' => 'پاسخ خالی از سوی سرور.',
        'file_error' => 'خطا در ارسال یا دریافت اطلاعات به سرور.',
        'file_corrupt' => 'فایل دریافت شده از سرور ناقص است.',
    ],
    'behavior' => [
        'missing_property' => 'کلاس :class باید شامل خصوصیت $:property که در :behavior استفاده شده است باشد.',
    ],
    'config' => [
        'not_found' => 'فایل پیکربندی  :file تعریف شده در :location یافت نشد.',
        'required' => "پیکربندی استفاده شده در :location باید مقدار ':property' را ارائه نماید.",
    ],
    'zip' => [
        'extract_failed' => "عدم توانایی در گشودن فایل هسته ':file'.",
    ],
    'event_log' => [
        'hint' => 'این گزارش نمایش دهنده خطاهای عملکردی در سیستم می باشد، مانند خطاهای در حال اجرا و اطلاعات خطایابی.',
        'menu_label' => 'گزارش رویدادها',
        'menu_description' => 'نمایش گزارش های سیستمی همراه با زمان و توضیح آنها.',
        'empty_link' => 'پاک  سازی گزارش رویداد ها',
        'empty_loading' => 'درحال پاکسازی گزارشها...',
        'empty_success' => 'گزارشها با موفقیت پاک شدند.',
        'return_link' => 'بازگشت به گزارش رویداد ها',
        'id' => 'مشخصه',
        'id_label' => 'مشخصه ی رویداد',
        'created_at' => 'ساعت و تاریخ',
        'message' => 'پیغام',
        'level' => 'مرحله',
        'preview_title' => 'اتفاق',
    ],
    'request_log' => [
        'hint' => 'این گزارش درخواستهایی از طرف مرورگر را که نیاز به بررسی دارند را نمایش می دهد. به عنوان مثال اگر بازدید کنند صفحه ای را که موجود نیست درخواست کند، یک گزینه با کد وضعیت 404 ایجاد می شود.',
        'menu_label' => 'گزارش درخواست ها',
        'menu_description' => 'نمایش درخواستهای مشکل ساز و انتقال داده شده مانند خطای : صفحه مورد نظر یافت نشد 404.',
        'empty_link' => 'پاک کردن گزارش درخواستها',
        'empty_loading' => 'در حال پاکسازی...',
        'empty_success' => 'گزارش درخواستها با موفقیت پاکسازی شد.',
        'return_link' => 'بازگشت به گزارش درخواستها',
        'id' => 'مشخصه',
        'id_label' => 'مشخصه ی گزارش',
        'count' => 'شمارشگر',
        'referer' => 'منتقل شده از',
        'url' => 'آدرس',
        'status_code' => 'وضعیت',
        'preview_title' => 'درخواست',
    ],
    'permissions' => [
        'name' => 'سیستم',
        'manage_system_settings' => 'مدیریت تنظیمات سیستم',
        'manage_software_updates' => 'مدیریت به روز رسانی نرم افزار',
        'access_logs' => 'نمایش وقایع سیستم',
        'manage_mail_templates' => 'مدیریت قالب پست الکترونیکی',
        'manage_mail_settings' => 'مدیریت تنظیمات پست الکترونیکی',
        'manage_other_administrators' => 'مدیریت سایر مدیران',
        'impersonate_users' => 'تغییر هویت کاربران',
        'manage_preferences' => 'مدیریت تنظیمات بخش مدیریت',
        'manage_editor' => 'مدیریت تنظیمات ویرایشگر کد',
        'manage_own_editor' => 'مدیریت تنظیمات ویژه ویرایشگر کد شخصی',
        'view_the_dashboard' => 'نمایش صفحه مدیریت',
        'manage_default_dashboard' => 'مدیریت داشبورد',
        'manage_branding' => 'شخصی سازی قسمت مدیریت',
    ],
    'log' => [
        'menu_label' => 'تنظیمات وقایع',
        'menu_description' => 'بخش هایی را که میخواهید وقایعش ثبت گردد مشخص کنید.',
        'default_tab' => 'ثبت کردن وقایع',
        'log_events' => 'ثبت رویداد های سیستمی',
        'log_events_comment' => 'ثبت وقایع و رویداد های سیستمی در پایگاه داده به جای فایل',
        'log_requests' => 'ثبت درخواست های نامشخص',
        'log_requests_comment' => 'درخواست های مرورگر که باعث خطاهایی مانند خطای 404 می شود.',
        'log_theme' => 'ثبت وقایع قالب ها',
        'log_theme_comment' => 'ثبت تغیراتی که در بخش مدیریت برای قالب ها اعمال می گردد.',
    ],
    'media' => [
        'invalid_path' => "آدرس فایل ':path' معتبر نیست.",
        'folder_size_items' => 'مورد(ها)',
    ],
    'page' => [
        'custom_error' => [
            'label' => 'خطای صفحه',
            'help' => 'متأسفیم ، اما مشکلی پیش آمد و صفحه نمایش داده نمی شود.',
        ],
        'invalid_token' => [
            'label' => 'کلید امنیتی معتبر نمی باشد.',
        ],
        'maintenance' => [
            'label' => "به زودی بازخواهیم گشت!",
            'help' => "در حال  تعمیر و نگهداری هستیم ، چندی دیگر دوباره بررسی کنید!",
            'message' => 'پیام:',
            'available_at' => 'در خدمت شما خواهیم بود در: :',
        ],
    ],
    'pagination' => [
        'previous' => 'قبلی',
        'next' => 'بعدی',
    ],
];
