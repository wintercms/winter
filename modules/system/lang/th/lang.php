<?php

return [
    'app' => [
        'name' => 'Winter CMS',
        'tagline' => 'กลับคืนสู่สามัญ',
    ],
    'directory' => [
        'create_fail' => 'ไม่สามารถสร้างโฟลเดอร์: :name',
    ],
    'file' => [
        'create_fail' => 'ไม่สามารถสร้างไฟล์: :name',
    ],
    'combiner' => [
        'not_found' => "หาไฟล์ combiner ':name' ไม่พบ",
    ],
    'system' => [
        'name' => 'ระบบ',
        'menu_label' => 'ระบบ',
        'categories' => [
            'cms' => 'CMS',
            'misc' => 'อื่นๆ',
            'logs' => 'บันทึก (Log)',
            'mail' => 'อีเมล',
            'shop' => 'ร้านค้า',
            'team' => 'ทีมงาน',
            'users' => 'ผู้ใช้',
            'system' => 'ระบบ',
            'social' => 'โซเชียล',
            'backend' => 'หลังบ้าน',
            'events' => 'Events',
            'customers' => 'ลูกค้า',
            'my_settings' => 'การตั้งค่าของฉัน',
            'notifications' => 'การแจ้งเตือน',
        ],
    ],
    'theme' => [
        'label' => 'ธีม',
        'unnamed' => 'ธีมยังไม่ได้ตั้งชื่อ',
        'name' => [
            'label' => 'ชื่อธีม',
            'help' => 'ตั้งชื่อธีมตามรหัสที่ไม่ซ้ำใคร เช่น Winter.Vanilla',
        ],
    ],
    'themes' => [
        'install' => 'ติดตั้งธีม',
        'search' => 'ค้นหาธีมที่จะติดตั้ง...',
        'installed' => 'ธีมที่ติดตั้งไว้',
        'no_themes' => 'ไม่มีธีมที่ติดตั้งจากตลาดสินค้า',
        'recommended' => 'แนะนำ',
        'remove_confirm' => 'คุณแน่ใจว่าต้องการลบธีมนี้?',
    ],
    'plugin' => [
        'label' => 'ปลั๊กอิน',
        'unnamed' => 'ปลั๊กอินยังไม่ได้ตั้งชื่อ',
        'name' => [
            'label' => 'ชื่อปลั๊กอิน',
            'help' => 'ตั้งชื่อปลั๊กอินตามรหัสที่ไม่ซ้ำใคร เช่น Winter.Blog',
        ],
        'by_author' => 'โดย :name'
    ],
    'plugins' => [
        'manage' => 'จัดการปลั๊กอิน',
        'install' => 'ติดตั้งปลั๊กอิน',
        'install_products' => 'ติดตั้งผลิตภัณฑ์',
        'search' => 'ค้นหาปลั๊กอินเพื่อติดตั้ง...',
        'installed' => 'ปลั๊กอินที่ติดตั้งไว้',
        'no_plugins' => 'ยังไม่มีการติดตั้งปลั๊กอินจากตลาดสินค้า',
        'recommended' => 'แนะนำ',
        'plugin_label' => 'ปลั๊กอิน',
        'unknown_plugin' => 'ลบปลั๊กอินออกจากระบบแล้ว',
        'select_label' => 'เลือกการทำงาน...',
        'bulk_actions_label' => 'การทำงานเป็นกลุ่ม',
        'check_yes' => 'ใช่',
        'check_no' => 'ไม่',
        'unfrozen' => 'เปิดการอัพเดทไว้แล้ว',
        'enabled' => 'เปิดใช้งานปลั๊กอินแล้ว',
        'freeze' => 'ปิดการอัพเดทสำหรับ',
        'unfreeze' => 'เปิดการอัพเดทสำหรับ',
        'enable' => 'เปิด',
        'disable' => 'ปิด',
        'refresh' => 'ตั้งค่าเริ่มต้น',
        'remove' => 'ลบ',
        'freeze_label' => 'ปิดการอัพเดท',
        'unfreeze_label' => 'เปิดการอัพเดท',
        'enable_label' => 'เปิดใช้งานปลั๊กอิน',
        'disable_label' => 'ปิดใช้งานปลั๊กอิน',
        'refresh_label' => 'ตั้งค่าเริ่มต้นข้อมูลปลั๊กอิน',
        'action_confirm' => 'คุณแน่ใจว่าต้องการ :action ปลั๊กอินเหล่านี้?',
        'freeze_success' => 'ปิดการอัพเดทปลั๊กอินที่เลือกเอาไว้สำเร็จ',
        'unfreeze_success' => 'เปิดการอัพเดทปลั๊กอินที่เลือกเอาไว้สำเร็จ',
        'enable_success' => 'เปิดใช้งานปลั๊กอินที่เลือกไว้สำเร็จ',
        'disable_success' => 'ปิดใช้งานปลั๊กอินที่เลือกไว้สำเร็จ',
        'refresh_confirm' => 'คุณแน่ใจว่าต้องการตั้งค่าเริ่มต้นปลั๊กอินที่เลือกเอาไว้? ข้อมูลของปลั๊กอินแต่ละตัวจะถูกกู้คืนกลับไปสู่ค่าเริ่มต้น',
        'refresh_success' => 'ตั้งค่าเริ่มต้นให้ปลั๊กอินที่เลือกไว้สำเร็จ',
        'remove_confirm' => 'คุณแน่ใจว่าต้องการเอาปลั๊กอินที่เลือกไว้ออกไป? ข้อมูลที่เกี่ยวข้องจะหายไปด้วย',
        'remove_success' => 'เอาปลั๊กอินที่เลือกไว้ออกสำเร็จ',
    ],
    'project' => [
        'name' => 'โครงการ',
        'owner_label' => 'เจ้าของ',
        'attach' => 'ผูกโครงการ',
        'detach' => 'ถอดโครงการ',
        'none' => 'ไม่มี',
        'id' => [
            'label' => 'Project ID',
            'help' => 'วิธีหา Project ID ของคุณ',
            'missing' => 'กรุณากำหนด Project ID',
        ],
        'detach_confirm' => 'คุณแน่ใจว่าต้องการถอดโครงการนี้?',
        'unbind_success' => 'โครงการถูกถอดออกแล้ว',
    ],
    'settings' => [
        'menu_label' => 'การตั้งค่า',
        'not_found' => 'ไม่พบค่าที่กำหนด',
        'update_success' => ':name อัพเดทการตั้งค่าแล้ว',
        'return' => 'กลับสู่หน้าการตั้งค่าระบบ',
        'search' => 'ค้นหา',
    ],
    'mail' => [
        'log_file' => 'ไฟล์บันทึก (log)',
        'menu_label' => 'การตั้งค่าอีเมล',
        'menu_description' => 'จัดการการตั้งค่าอีเมล',
        'general' => 'ทั่วไป',
        'method' => 'วิธีการส่งอีเมล',
        'sender_name' => 'ชื่อผู้ส่ง',
        'sender_email' => 'อีเมลผู้ส่ง',
        'php_mail' => 'PHP mail',
        'smtp' => 'SMTP',
        'smtp_address' => 'SMTP address',
        'smtp_authorization' => 'จำเป็นต้องมีการอนุญาต SMTP',
        'smtp_authorization_comment' => 'เลือกกล่องตัวเลือกถ้าเซิร์ฟเวอร์ SMTP ของท่านต้องมีการอนุญาต',
        'smtp_username' => 'ชื่อผู้ใช้',
        'smtp_password' => 'รหัสผ่าน',
        'smtp_port' => 'พอร์ท SMTP',
        'smtp_ssl' => 'ต้องมีการเชื่อมต่อ SSL',
        'smtp_encryption' => 'โปรโตคอลการเข้ารหัส SMTP',
        'smtp_encryption_none' => 'ไม่เข้ารหัส',
        'smtp_encryption_tls' => 'TLS',
        'smtp_encryption_ssl' => 'SSL',
        'sendmail' => 'Sendmail',
        'sendmail_path' => 'Sendmail path',
        'sendmail_path_comment' => 'กรุณากำหนด path ของโปรแกรม sendmail',
        'ses' => 'SES',
        'ses_key' => 'SES key',
        'ses_key_comment' => 'ใส่ SES API key ของท่าน',
        'ses_secret' => 'SES secret',
        'ses_secret_comment' => 'ใส่ SES API secret key ของท่าน',
        'ses_region' => 'SES region',
        'ses_region_comment' => 'ใส่ SES region ของท่าน (เช่น us-east-1)',
    ],
    'mail_templates' => [
        'menu_label' => 'แม่แบบอีเมล (template)',
        'menu_description' => 'แก้ไขแม่แบบอีเมลที่จะส่งให้ผู้ใช้และผู้ดูแลระบบ, จัดการโครงร่างอีเมล',
        'new_template' => 'แม่แบบใหม่',
        'new_layout' => 'โครงร่างใหม่',
        'new_partial' => 'ส่วนย่อยใหม่',
        'template' => 'แม่แบบ',
        'templates' => 'แม่แบบ',
        'partial' => 'ส่วนย่อย',
        'partials' => 'ส่วนย่อย',
        'menu_layouts_label' => 'โครงร่างอีเมล',
        'menu_partials_label' => 'ส่วนย่อยอีเมล',
        'layout' => 'โครงร่าง',
        'layouts' => 'โครงร่าง',
        'no_layout' => '-- ไม่มีโครงร่าง --',
        'name' => 'ชื่อ',
        'name_comment' => 'ชื่อที่ไม่ซ้ำ สำหรับอ้างถึงแม่แบบนี้',
        'code' => 'รหัส',
        'code_comment' => 'รหัสที่ไม่ซ้ำ สำหรับอ้างถึงแม่แบบนี้',
        'subject' => 'หัวข้อ',
        'subject_comment' => 'หัวข้อเนื้อหาอีเมล',
        'description' => 'รายละเอียด',
        'content_html' => 'HTML',
        'content_css' => 'CSS',
        'content_text' => 'Plaintext',
        'test_send' => 'ส่งเนื้อหาทดสอบ',
        'test_success' => 'ส่งเนื้อหาทดสอบแล้ว',
        'test_confirm' => 'ส่งเนื้อหาทดสอบไปที่ :email ทำต่อไป?',
        'creating' => 'กำลังสร้างแม่แบบ...',
        'creating_layout' => 'กำลังสร้างโครงร่าง...',
        'saving' => 'กำลังบันทึกแม่แบบ...',
        'saving_layout' => 'กำลังบันทึกโครงร่าง...',
        'delete_confirm' => 'ลบแม่แบบนี้?',
        'delete_layout_confirm' => 'ลบโครงร่างนี้?',
        'deleting' => 'กำลังลบแม่แบบ...',
        'deleting_layout' => 'กำลังลบโครงร่าง...',
        'sending' => 'กำลังส่งเนื้อหาทดสอบ...',
        'return' => 'กลับสู่หน้ารายการแม่แบบ',
        'options' => 'ตัวเลือก',
        'disable_auto_inline_css' => 'ปิดการใส่ inline style ลงใน HTML element โดยอัตโนมัติ',
    ],
    'mail_brand' => [
        'menu_label' => 'การสร้างแบรนด์อีเมล',
        'menu_description' => 'แก้ไขสีและภาพลักษณ์ของแม่แบบอีเมล',
        'page_title' => 'ปรับแก้ภาพลักษณ์อีเมล',
        'sample_template' => [
            'heading' => 'หัวเรื่อง',
            'paragraph' => 'นี่เป็นย่อหน้าตัวอย่างที่มีทั้งตัวหนังสือและลิงก์ Cumque dicta <a>doloremque eaque</a>, enim error laboriosam pariatur possimus tenetur veritatis voluptas.',
            'table' => [
                'item' => 'รายการ',
                'description' => 'รายละเอียด',
                'price' => 'ราคา',
                'centered' => 'จัดกึ่งกลาง',
                'right_aligned' => 'จัดชิดขวา',
            ],
            'buttons' => [
                'primary' => 'ปุ่มหลัก',
                'positive' => 'ปุ่มด้านบวก',
                'negative' => 'ปุ่มด้านลบ',
            ],
            'panel' => 'แผงข้อความนี้ดูดีแค่ไหน?',
            'more' => 'ตัวหนังสืออื่นๆอีก',
            'promotion' => 'รหัสคูปอง: WINTER',
            'subcopy' => 'นี่เป็นสำเนาย่อยของอีเมล',
            'thanks' => 'ขอบคุณครับ',
        ],
        'fields' => [
            '_section_background' => 'พื้นหลัง',
            'body_bg' => 'พื้นหลังรอบนอก',
            'content_bg' => 'พื้นหลังเนื้อหา',
            'content_inner_bg' => 'พื้นหลังเนื้อหาด้านใน',
            '_section_buttons' => 'ปุ่ม',
            'button_text_color' => 'สีตัวอักษรในปุ่ม',
            'button_primary_bg' => 'พื้นหลังปุ่มหลัก',
            'button_positive_bg' => 'พื้นหลังปุ่มด้านบวก',
            'button_negative_bg' => 'พื้นหลังปุ่มด้านลบ',
            '_section_type' => 'ตัวอักษร',
            'header_color' => 'สีตัวอักษร Header',
            'heading_color' => 'สีตัวอักษรหัวเรื่อง',
            'text_color' => 'สีตัวอักษรทั่วไป',
            'link_color' => 'สีลิงก์',
            'footer_color' => 'สีตัวอักษร Footer',
            '_section_borders' => 'ขอบ',
            'body_border_color' => 'สีขอบด้านนอก',
            'subcopy_border_color' => 'สีขอบสำเนาย่อย',
            'table_border_color' => 'สีขอบตาราง',
            '_section_components' => 'ส่วนประกอบ',
            'panel_bg' => 'สีพื้นหลังแผงข้อความ',
            'promotion_bg' => 'สีพื้นหลังโปรโมชั่น',
            'promotion_border_color' => 'สีขอบโปรโมชั่น',
        ],
    ],
    'install' => [
        'project_label' => 'ผูกกับโครงการ',
        'plugin_label' => 'ติดตั้งปลั๊กอิน',
        'theme_label' => 'ติดตั้งธีม',
        'missing_plugin_name' => 'กรุณากำหนดชื่อปลั๊กอินที่จะติดตั้ง',
        'missing_theme_name' => 'กรุณากำหนดชื่อธีมที่จะติดตั้ง',
        'install_completing' => 'กำลังจบกระบวนการติดตั้ง',
        'install_success' => 'ติดตั้งปลั๊กอินสำเร็จ',
    ],
    'updates' => [
        'title' => 'จัดการการอัพเดท',
        'name' => 'การอัพเดทซอฟต์แวร์',
        'menu_label' => 'อัพเดท & ปลั๊กอิน (Plugin)',
        'menu_description' => 'อัพเดทระบบ, จัดการ และลงปลั๊กอินหรือธีม',
        'return_link' => 'กลับสู่หน้าการอัพเดทระบบ',
        'check_label' => 'ตรวจสอบอัพเดทใหม่',
        'retry_label' => 'ลองอีกครั้ง',
        'plugin_name' => 'ชื่อ',
        'plugin_code' => 'โค้ด',
        'plugin_description' => 'รายละเอียด',
        'plugin_version' => 'เวอร์ชั่น',
        'plugin_author' => 'ผู้สร้าง',
        'plugin_not_found' => 'ไม่พบปลั๊กอิน',
        'core_current_build' => 'การสร้างตัวปัจจุบัน',
        'core_view_changelog' => 'ดูบันทึกการเปลี่ยนแปลง',
        'core_build' => 'การสร้างที่ :build',
        'core_build_help' => 'การสร้างตัวล่าสุดพร้อมให้ดาวน์โหลดแล้ว',
        'core_downloading' => 'กำลังดาวน์โหลดไฟล์แอพพลิเคชั่น',
        'core_extracting' => 'กำลังเปิดขยายกล่องไฟล์แอพพลิเคชั่น',
        'core_set_build' => 'Setting build number',
        'changelog' => 'บันทึกการเปลี่ยนแปลง',
        'changelog_view_details' => 'ดูรายละเอียด',
        'plugins' => 'ปลั๊กอิน',
        'themes' => 'ธีม',
        'disabled' => 'ปิดการใช้งาน',
        'plugin_downloading' => 'กำลังดาวน์โหลดปลั๊กอิน: :name',
        'plugin_extracting' => 'กำลังเปิดขยายกล่องปลั๊กอิน: :name',
        'plugin_version_none' => 'ปลั๊กอินใหม่',
        'plugin_current_version' => 'รุ่นปัจจุบัน',
        'theme_new_install' => 'การติดตั้งธีมใหม่',
        'theme_downloading' => 'กำลังดาวน์โหลดธีม: :name',
        'theme_extracting' => 'กำลังเปิดขยายกล่องธีม: :name',
        'update_label' => 'อัพเดทซอฟต์แวร์',
        'update_completing' => 'กำลังจบกระบวนการอัพเดท',
        'update_loading' => 'กำลังโหลดอัพเดทที่ใช้ได้...',
        'update_success' => 'กระบวนการอัพเดทเสร็จสมบูรณ์',
        'update_failed_label' => 'อัพเดทไม่สำเร็จ',
        'force_label' => 'บังคับการอัพเดท',
        'found' => [
            'label' => 'พบอัพเดทใหม่!',
            'help' => 'กดปุ่มอัพเดทซอฟต์แวร์เพื่อเริ่มกระบวนการอัพเดท',
        ],
        'none' => [
            'label' => 'ไม่มีอัพเดท',
            'help' => 'ไม่พบอัพเดทใหม่',
        ],
        'important_action' => [
            'empty' => 'เลือกการทำงาน',
            'confirm' => 'ยืนยันการอัพเดท',
            'skip' => 'ข้ามอัพเดทนี้ (ครั้งเดียว)',
            'ignore' => 'ข้ามอัพเดทนี้ (ตลอดไป)',
        ],
        'important_action_required' => 'Action required',
        'important_view_guide' => 'ดูหนังสือแนะนำการอัพเกรด',
        'important_view_release_notes' => 'ดูบันทึกการปล่อยโปรแกรม',
        'important_alert_text' => 'อัพเดทบางตัวต้องมีการตรวจสอบ',
        'details_title' => 'รายละเอียดปลั๊กอิน',
        'details_view_homepage' => 'ดูหน้าโฮมเพจ',
        'details_readme' => 'เอกสารการใช้โปรแกรม',
        'details_readme_missing' => 'ไม่มีเอกสารการใช้โปรแกรมมาด้วย',
        'details_changelog' => 'บันทึกการเปลี่ยนแปลง',
        'details_changelog_missing' => 'ไม่มีบันทึกการเปลี่ยนแปลงมาด้วย',
        'details_upgrades' => 'คู่มือการอัพเกรด',
        'details_upgrades_missing' => 'ไม่มีขั้นตอนการอัพเกรดมาด้วย',
        'details_licence' => 'ใบอนุญาต',
        'details_licence_missing' => 'ไม่มีใบอนุญาตมาด้วย',
        'details_current_version' => 'รุ่นปัจจุบัน',
        'details_author' => 'ผู้สร้าง',
    ],
    'server' => [
        'connect_error' => 'การเชื่อมต่อกับเซิร์ฟเวอร์ผิดพลาด',
        'response_not_found' => 'หาเซิร์ฟเวอร์อัพเดทไม่พบ',
        'response_invalid' => 'การตอบกลับจากเซิร์ฟเวอร์ไม่ถูกต้อง',
        'response_empty' => 'ไม่มีการตอบกลับจากเซิร์ฟเวอร์',
        'file_error' => 'เซิร์ฟเวอร์ไม่สามารถส่งแพคเกจได้',
        'file_corrupt' => 'ไฟล์จากเซิร์ฟเวอร์เสียหาย',
    ],
    'behavior' => [
        'missing_property' => 'คลาส :class ต้องมีการกำหนดคุณสมบัติ $:property ที่ใช้โดยพฤติกรรม :behavior',
    ],
    'config' => [
        'not_found' => 'ไม่สามารถหาไฟล์การตั้งค่า :file ที่กำหนดไว้สำหรับ :location.',
        'required' => "การตั้งค่าที่ใช้ใน :location ต้องมีค่า ':property'.",
    ],
    'zip' => [
        'extract_failed' => "ไม่สามารถแยกไฟล์หลัก ':file'.",
    ],
    'event_log' => [
        'hint' => 'บันทึกนี้แสดงรายการของข้อผิดพลาดที่อาจเกิดขึ้นได้ในแอป เช่น exceptions และข้อมูลการดีบั๊ก.',
        'menu_label' => 'บันทึกเหตุการณ์',
        'menu_description' => 'ดูข้อความบันทึกระบบพร้อมด้วยเวลาที่บันทึกและรายละเอียดอื่นๆ',
        'empty_link' => 'ลบบันทึกเหตุการณ์ทั้งหมด',
        'empty_loading' => 'กำลังลบบันทึกเหตุการณ์...',
        'empty_success' => 'ลบบันทึกเหตุการณ์แล้ว',
        'return_link' => 'กลับสู่หน้าบันทึกเหตุการณ์',
        'id' => 'ไอดี',
        'id_label' => 'ไอดีเหตุการณ์',
        'created_at' => 'วันเวลา',
        'message' => 'ข้อความ',
        'level' => 'ระดับ',
        'preview_title' => 'เหตุการณ์',
    ],
    'request_log' => [
        'hint' => 'บันทึกนี้แสดงรายการการขอเข้าใช้งานผ่านเว็บบราวเซอร์ที่อาจต้องตรวจสอบ เช่นถ้าผู้เข้าเยี่ยมชมเปิดหน้าเว็บไม่พบ และได้รับ status code 404',
        'menu_label' => 'บันทึกการขอเข้าใช้งาน',
        'menu_description' => 'ดูการขอเข้าใช้งานที่ไม่ดี หรือมีการเปลี่ยนเส้นทาง เช่น Page not found (404)',
        'empty_link' => 'ลบบันทึกการขอเข้าใช้งานทั้งหมด',
        'empty_loading' => 'กำลังลบบันทึกการขอเข้าใช้งาน...',
        'empty_success' => 'ลบบันทึกการขอเข้าใช้งานแล้ว',
        'return_link' => 'กลับสู่หน้าบันทึกการขอเข้าใช้งาน',
        'id' => 'ไอดี',
        'id_label' => 'ไอดีบันทึก',
        'count' => 'จำนวนครั้ง',
        'referer' => 'เว็บไซต์อ้างอิง',
        'url' => 'URL',
        'status_code' => 'สถานะ',
        'preview_title' => 'การขอเข้าใช้งาน',
    ],
    'permissions' => [
        'name' => 'ระบบ',
        'manage_system_settings' => 'จัดการการตั้งค่าระบบ',
        'manage_software_updates' => 'จัดการการอัพเดทซอฟต์แวร์',
        'access_logs' => 'ดูบันทึกของระบบ',
        'manage_mail_templates' => 'จัดการเทมเพลตอีเมล',
        'manage_mail_settings' => 'จัดการการตั้งค่าอีเมล',
        'manage_other_administrators' => 'จัดการผู้ดูแลระบบคนอื่น',
        'impersonate_users' => 'ปลอมตัวเป็นผู้ใช้',
        'manage_preferences' => 'จัดการหน้าเว็บหลังบ้านตามใจชอบ',
        'manage_editor' => 'จัดการตัวแก้ไขโค้ดตามใจชอบ',
        'view_the_dashboard' => 'ดูหน้าแผงควบคุม',
        'manage_default_dashboard' => 'จัดการตั้งค่าเริ่มต้นหน้าแผงควบคุม',
        'manage_branding' => 'ปรับแต่งหน้าเว็บหลังบ้าน',
    ],
    'log' => [
        'menu_label' => 'การตั้งค่าบันทึก',
        'menu_description' => 'กำหนดว่าส่วนไหนที่ควรจะมีการบันทึก',
        'default_tab' => 'การบันทึก (Log)',
        'log_events' => 'บันทึกเหตุการณ์เกี่ยวกับระบบ',
        'log_events_comment' => 'เก็บเหตุการณ์เกี่ยวกับระบบในฐานข้อมูล เพิ่มเติมจากที่เก็บไว้ที่ไฟล์บันทึก',
        'log_requests' => 'บันทึกการร้องขอที่ไม่ดี',
        'log_requests_comment' => 'การร้องขอผ่านทางเว็บบราวเซอร์ที่อาจจะต้องเข้าไปตรวจสอบ เช่น 404 errors',
        'log_theme' => 'บันทึกการเปลี่ยนธีม',
        'log_theme_comment' => 'บันทึกเมื่อมีการเปลี่ยนธีมทางเว็บหลังบ้าน',
    ],
    'media' => [
        'invalid_path' => "ไฟล์ที่กำหนดไม่ถูกต้อง: ':path'",
        'folder_size_items' => 'รายการ',
    ],
    'page' => [
        'custom_error' => [
            'label' => 'หน้าเว็บผิดพลาด',
            'help' => "ขอโทษครับ, มีบางอย่างผิดพลาดและไม่สามารถแสดงหน้าเว็บได้",
        ],
        'maintenance' => [
            'label' => "เราจะรีบกลับมา!",
            'help' => "เรากำลังปิดปรับปรุงระบบ, ลองเข้าอีกครั้งในภายหลัง!",
            'message' => "ข้อความ:",
            'available_at' => "ลองใหม่อีกครั้งหลังจาก:",
        ],
        'invalid_token' => [
            'label' => 'โทเค็นความปลอดภัยไม่ถูกต้อง',
        ],
    ],
    'pagination' => [
        'previous' => 'ก่อนหน้า',
        'next' => 'ถัดไป',
    ],
];
