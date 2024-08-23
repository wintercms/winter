<?php

return [
    'app' => [
        'name' => 'Winter CMS',
        'tagline' => 'Návrat k základom',
    ],
    'locale' => [
        'ar' => 'العربية',
        'be' => 'Беларуская',
        'bg' => 'Български',
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
        'tr' => 'Türkçe',
        'uk' => 'Українська мова',
        'zh-cn' => '简体中文',
        'zh-tw' => '繁體中文',
        'vn' => 'Tiếng việt',
    ],
    'directory' => [
        'create_fail' => 'Nie je možné vytvoriť priečinok: :name',
    ],
    'file' => [
        'create_fail' => 'Nie je možné vytvoriť súbor: :name',
    ],
    'page' => [
        'invalid_token' => [
            'label' => 'Neplatný bezpečnostný token',
        ],
    ],
    'combiner' => [
        'not_found' => "Zlučujúci súbor ':name' nebol nájdený.",
    ],
    'system' => [
        'name' => 'Systém',
        'menu_label' => 'Systém',
        'categories' => [
            'cms' => 'CMS',
            'misc' => 'Ostatné',
            'logs' => 'Záznamy',
            'mail' => 'E-mail',
            'shop' => 'E-shop',
            'team' => 'Tým',
            'users' => 'Užívateľ',
            'system' => 'Systém',
            'social' => 'Sociálne',
            'backend' => 'Administrácia',
            'events' => 'Udalosti',
            'customers' => 'Zákazníci',
            'my_settings' => 'Moje nastavenia',
            'notifications' => 'Oznámenia',
        ],
    ],
    'theme' => [
        'label' => 'Téma',
        'unnamed' => 'Téma bez názvu',
        'name' => [
            'label' => 'Názov témy',
            'help' => 'Názov témy podľa jej unikátneho kódu. Napríklad, Winter.Vanilla',
        ],
    ],
    'themes' => [
        'install' => 'Inštalácia tém',
        'search' => 'vyhľadať témy na nainštalovanie...',
        'installed' => 'Nainštalované témy',
        'no_themes' => 'Žiadne témy inštalované z trhu.',
        'recommended' => 'Odporúčané',
        'remove_confirm' => 'Skutočne chcete odstrániť túto tému?',
    ],
    'plugin' => [
        'label' => 'Plugin',
        'unnamed' => 'Plugin bez názvu',
        'name' => [
            'label' => 'Názov pluginu',
            'help' => 'Názov pluginu podľa jeho unikátneho kódu. Napríklad, Winter.Blog',
        ],
        'by_author' => 'Vytvoril :name',
    ],
    'plugins' => [
        'manage' => 'Správa pluginov',
        'install' => 'Inštalovať pluginy',
        'install_products' => 'Inštalovať produkty',
        'search' => 'vyhľadať pluginy na inštaláciu...',
        'installed' => 'Inštalované pluginy',
        'no_plugins' => 'Žiadne pluginy inštalované z trhu.',
        'recommended' => 'Odporúčané',
        'plugin_label'  => 'Plugin',
        'unknown_plugin' => 'Plugin bol odtránený z disku.',
        'select_label' => 'Vyberte akciu...',
        'check_yes' => 'Áno',
        'check_no' => 'Nie',
        'unfrozen' => 'Aktualizácie povolené',
        'enabled' => 'Plugin aktivovaný',
        'freeze' => 'zakázať aktualizácie pre',
        'unfreeze' => 'povoliť aktualizácie pre',
        'enable' => 'povoliť',
        'disable' => 'zakázať',
        'refresh' => 'obnoviť',
        'remove' => 'Odstrániť',
        'freeze_label' => 'Zakázať aktualizácie',
        'unfreeze_label' => 'Povoliť aktualizácie',
        'enable_label' => 'Povoliť pluginy',
        'disable_label' => 'Zakázať pluginy',
        'refresh_label' => 'Obnoviť dáta pluginov',
        'action_confirm' => 'Skutočne chcete :action tieto pluginy?',
        'freeze_success' => 'Úspešne zakázané aktualizácie vybraných pluginov.',
        'unfreeze_success' => 'Úspešne povolené aktualizácie vybraných pluginov.',
        'enable_success' => 'Vybrané pluginy úspešne povolené.',
        'disable_success' => 'Vybrané pluginy úspešne zakázané.',
        'refresh_confirm' => 'Skutočne chcete obnoviť vybrané pluginy? Táto akcia zmaže všetky dáta pluginov a obnoví ich do pôvodného stavu po inštalácií.',
        'refresh_success' => 'Vybrané pluginy úspešne obnovené',
        'remove_confirm' => 'Skutočne chcete zmazať vybrané pluginy? Táto akcia takisto zmaže všetky priradené dáta.',
        'remove_success' => 'Vybrané pluginy úspešne odstránené.',
    ],
    'project' => [
        'name' => 'Projekt',
        'owner_label' => 'Vlastník',
        'attach' => 'Pripojiť projekt',
        'detach' => 'Odpojiť projekt',
        'none' => 'Žiadny',
        'id' => [
            'label' => 'Project ID',
            'help' => 'Ako nájsť vaše Project ID',
            'missing' => 'Prosím zadajta vaše Project ID.',
        ],
        'detach_confirm' => 'Skutočne chcete odpojiť tento projekt?',
        'unbind_success' => 'Projekt bol odpojený.',
    ],
    'settings' => [
        'menu_label' => 'Nastavenia',
        'not_found' => 'Zvolené nastavenie sa nepodarilo .',
        'missing_model' => 'Stránka s nastaveniami vyžaduje definíciu Modelu.',
        'update_success' => 'nastavenia pre :name úspešne uložené',
        'return' => 'Návrat do systémových nastavení',
        'search' => 'Hľadať',
    ],
    'mail' => [
        'log_file' => 'Súbor záznamov',
        'menu_label' => 'Nastavenia e-mailu',
        'menu_description' => 'Správa nastavení e-mailu.',
        'general' => 'Obecné',
        'method' => 'Metóda posielania správ',
        'sender_name' => 'Meno odosielateľa',
        'sender_email' => 'E-mail odosielateľa',
        'php_mail' => 'PHP mail',
        'smtp' => 'SMTP',
        'smtp_address' => 'SMTP adresa',
        'smtp_authorization' => 'Vyžadovaná SMTP autorizácia',
        'smtp_authorization_comment' => 'Použite tento ak váš SMTP server vyžaduje autorizáciu.',
        'smtp_username' => 'Užívateľské meno',
        'smtp_password' => 'Heslo',
        'smtp_port' => 'SMTP port',
        'sendmail' => 'Sendmail',
        'sendmail_path' => 'Sendmail cest',
        'sendmail_path_comment' => 'Prosím zadajte cestu k sendmail programu.',
    ],
    'mail_templates' => [
        'menu_label' => 'E-mailové šablóny',
        'menu_description' => 'Upravujte e-mailové šablóny, ktoré sú posielané užívateľom a administrátorom, správa e-mailového layoutu.',
        'new_template' => 'Nová šablóna',
        'new_layout' => 'Nový layout',
        'new_partial' => 'Nová čiastková šablóna',
        'template' => 'Šablóna',
        'templates' => 'Šablóny',
        'partial' => 'Čiastková šablóna',
        'partials' => 'Čiastkové šablóny',
        'menu_layouts_label' => 'Layouty e-mailu',
        'menu_partials_label' => 'Čiastkové šablóny e-mailu',
        'layout' => 'Layout',
        'layouts' => 'Layouty',
        'no_layout' => '-- Žiadny layout --',
        'name' => 'Názov',
        'name_comment' => 'Unikátny názov, ktorým sa odkazujeme na túto šablónu',
        'code' => 'Kód',
        'code_comment' => 'Unikátny kód, ktorým sa odkazujeme na túto šablónu',
        'subject' => 'Predmet',
        'subject_comment' => 'Predmet e-mailovej správy',
        'description' => 'Popis',
        'content_html' => 'HTML',
        'content_css' => 'CSS',
        'content_text' => 'Čistý text',
        'test_send' => 'Poslať testovaciu správu',
        'test_success' => 'Testovacia správa poslaná.',
        'test_confirm' => 'Poslať testovaciu správu na :email. Pokračovať?',
        'creating' => 'Vytváranie šablóny...',
        'creating_layout' => 'Vytváranie layoutu...',
        'saving' => 'Ukladanie šablóny...',
        'saving_layout' => 'Ukladanie layoutu...',
        'delete_confirm' => 'Skutočne zmazať túto šablónu?',
        'delete_layout_confirm' => 'Skutočne zmazať tento layout?',
        'deleting' => 'Mazanie šablóny...',
        'deleting_layout' => 'Mazanie layoutu...',
        'sending' => 'Posielanie testovacej správy...',
        'return' => 'Späť na zoznam šablón',
    ],
    'mail_brand' => [
        'menu_label' => 'Personalizácia e-mailov',
        'menu_description' => 'Správa farieb a vzhľadu e-mailových šablón.',
        'page_title' => 'Úprava vzhľadu e-mailu',
        'sample_template' => [
            'heading' => 'Nadpis',
            'paragraph' => 'Tento odstavec obsahuje Lorem Ipsum a link. Cumque dicta <a>doloremque eaque</a>, enim error laboriosam pariatur possimus tenetur veritatis voluptas.',
            'table' => [
                'item' => 'Položka',
                'description' => 'Popis',
                'price' => 'Cena',
                'centered' => 'Na stred',
                'right_aligned' => 'Zarovnané vpravo',
            ],
            'buttons' => [
                'primary' => 'Primárne tlačítko',
                'positive' => 'Pozitívne tlačítko',
                'negative' => 'Negatívne tlačítko',
            ],
            'panel' => 'Aký skvalý je tento panel?',
            'more' => 'Nejaký ďalší text',
            'promotion' => 'Kód kupónu: WINTER',
            'subcopy' => 'Toto je subkópia e-mailu',
            'thanks' => 'Ďakujeme',
        ],
        'fields' => [
            '_section_background' => 'Pozadie',
            'body_bg' => 'Pozadie body značky',
            'content_bg' => 'Pozadie obsahu',
            'content_inner_bg' => 'Vnútorné pozadie obsahu',
            '_section_buttons' => 'Tlačítka',
            'button_text_color' => 'Farba textu tlačítok',
            'button_primary_bg' => 'Farba pozadia primárneho tlačítka',
            'button_positive_bg' => 'Farba pozadia pozitávneho tlačítka',
            'button_negative_bg' => 'Farba pozadia negatávneho tlačítka',
            '_section_type' => 'Typografia',
            'header_color' => 'Farba hlavičky',
            'heading_color' => 'Farba nadpisov',
            'text_color' => 'Farba textu',
            'link_color' => 'Farba linkov',
            'footer_color' => 'Farba ukončenia strany',
            '_section_borders' => 'Okraje',
            'body_border_color' => 'Farba okrajov znčky body',
            'subcopy_border_color' => 'Farba okrajov subkópie',
            'table_border_color' => 'Farba okrajov tabuľky',
            '_section_components' => 'Komponenty',
            'panel_bg' => 'Pozadie panelu',
            'promotion_bg' => 'Pozadie reklamy',
            'promotion_border_color' => 'Farba okrajov reklamy',
        ],
    ],
    'install' => [
        'project_label' => 'Pridať k projetku',
        'plugin_label' => 'Inštalovať plugin',
        'theme_label' => 'Inštalovať tému',
        'missing_plugin_name' => 'Prosím zadajte názov pluginu na nainštalovanie.',
        'missing_theme_name' => 'Prosím zadajte názov témy na nainštalovanie.',
        'install_completing' => 'Dokončovanie inštalačného procesu',
        'install_success' => 'Plugin bol úspešne nainštalovaný',
    ],
    'updates' => [
        'title' => 'Správa aktualizácií',
        'name' => 'Aktualizácie software',
        'menu_label' => 'Aktualizácia a pluginy',
        'menu_description' => 'Aktualizácie systému, správa a inštalácia pluginov a tém.',
        'return_link' => 'Späť na aktualizácie systému',
        'check_label' => 'Skontrolovať aktualizácie',
        'retry_label' => 'Skúsiť znova',
        'plugin_name' => 'Názov',
        'plugin_code' => 'Kód',
        'plugin_description' => 'Popis',
        'plugin_version' => 'Verzia',
        'plugin_author' => 'Autor',
        'plugin_not_found' => 'Plugin nebol nájdený',
        'core_current_build' => 'Aktuálne zostavenie',
        'core_build' => 'Zostavenie :build',
        'core_build_help' => 'Najnovšie zostavenie je dostupné.',
        'core_downloading' => 'Sťahovanie súborov aplikácie',
        'core_extracting' => 'Rozbaľovanie súborov aplikácie',
        'core_set_build' => 'Nastavenie čísla zostavenia',
        'plugins' => 'Pluginy',
        'themes' => 'Témy',
        'disabled' => 'Neaktívny',
        'plugin_downloading' => 'Sťahovanie pluginu: :name',
        'plugin_extracting' => 'Rozbaľovanie pluginu: :name',
        'plugin_version_none' => 'Nový plugin',
        'plugin_current_version' => 'Aktuálna verzia',
        'theme_new_install' => 'Inštalácia novej témy.',
        'theme_downloading' => 'Sťahovanie témy: :name',
        'theme_extracting' => 'Rozbaľovanie témy: :name',
        'update_label' => 'Aktualizácia software',
        'update_completing' => 'Dokončovanie aktualizácie',
        'update_loading' => 'Načítavanie dostupbých aktualizácií...',
        'update_success' => 'Proces aktualizácie úspešne ukončený',
        'update_failed_label' => 'Aktualizácia zlyhala',
        'force_label' => 'Vynútiť aktualizácie',
        'found' => [
            'label' => 'Nájdené nové aktualizácie!',
            'help' => 'Kliknite na Aktualizovať pre začatie aktualizácie.',
        ],
        'none' => [
            'label' => 'Žiadne aktualizácie',
            'help' => 'Žiadne nové aktualizácie neboli nájdené.',
        ],
        'important_action' => [
            'empty' => 'Vybrať akciu',
            'confirm' => 'Potvrdiť aktualizácie',
            'skip' => 'Preskočiť túto aktualizáciu (iba raz)',
            'ignore' => 'Preskočiť tento update (vždy)',
        ],
        'important_action_required' => 'Je vyťadovaná akcia',
        'important_view_guide' => 'Zobraziť návod pre aktualizáciu',
        'important_view_release_notes' => 'Zobraziť poznámky k vydaniu',
        'important_alert_text' => 'Niektoré aktualizácie vyžadujú vašu pozornosť.',
        'details_title' => 'Detaily pluginu',
        'details_view_homepage' => 'Zobraziť domovskú stránku',
        'details_readme' => 'Dokumentácia',
        'details_readme_missing' => 'Nie je poskytnutá žiadna dokumentácia.',
        'details_changelog' => 'Zoznam zmien',
        'details_changelog_missing' => 'Nie je poskytnutý žiadny zoznam zmien.',
        'details_upgrades' => 'Návod pre aktualizáciu',
        'details_upgrades_missing' => 'Nie je poskytnutý žiadny návod pre aktualizáciu.',
        'details_licence' => 'Licencia',
        'details_licence_missing' => 'Nie je poskytnutá žiadna licencia.',
        'details_current_version' => 'Aktuálna verzia',
        'details_author' => 'Autor',
    ],
    'server' => [
        'connect_error' => 'Chyba pripojenia na server.',
        'response_not_found' => 'Aktualizácia nebola nájdená.',
        'response_invalid' => 'Neplatná odpoveď servra.',
        'response_empty' => 'Prázdna odpoveď servra.',
        'file_error' => 'Chyba sťahovania balíčku zo servra.',
        'file_corrupt' => 'Súbor zo servra je poškodený.',
    ],
    'behavior' => [
        'missing_property' => 'Trieda :class musí definovať vlastnosť $:property použitú správaním :behavior.',
    ],
    'config' => [
        'not_found' => 'Nebolo možné nájsť konfiguračný súbor :file definovaný pre :location.',
        'required' => "Konfigurácia použitá v :location musí podporovať hodnotu ':property'.",
    ],
    'zip' => [
        'extract_failed' => "Nepodarilo sa rozbaliť súbor ':file'.",
    ],
    'event_log' => [
        'hint' => 'Tento záznam zobrazuje zoznam potencionálnch chýb v aplikácií, ako napríklad výnimky a ladiace informácie.',
        'menu_label' => 'Záznam udalostí',
        'menu_description' => 'Zobraziť záznam systémových správ s časom a detailami.',
        'empty_link' => 'Zmazať záznam udalostí',
        'empty_loading' => 'Mazanie záznamu udalostí...',
        'empty_success' => 'Záznam udalostí zmazaný',
        'return_link' => 'Späť na záznam udalostí',
        'id' => 'ID',
        'id_label' => 'ID udalosti',
        'created_at' => 'Dátum a čas',
        'message' => 'Správa',
        'level' => 'Úroveň',
        'preview_title' => 'Udalosť',
    ],
    'request_log' => [
        'hint' => 'Tento záznam zobrazuje zoznam požiadaviek prehliadača, ktoré môžu vyžadovať vašu pozornosť. Napríklad, ak návštevník otvorí CMS stránku, ktorú sa nepodarilo nájsť, vytvorí sa záznam so status kódom 404.',
        'menu_label' => 'Záznam požiadaviek',
        'menu_description' => 'Zobraziť zlé alebo presmerované požiadavky, ako napríklad Stránka nenájdená (404).',
        'empty_link' => 'Vyprázdniť záznam požiadaviek',
        'empty_loading' => 'Mazanie záznamu požiadaviek...',
        'empty_success' => 'Záznam požiadaviek zmazaný',
        'return_link' => 'Späť na záznam požiadaviek',
        'id' => 'ID',
        'id_label' => 'ID záznamu',
        'count' => 'Počítadlo',
        'referer' => 'Odkazy',
        'url' => 'URL',
        'status_code' => 'Status',
        'preview_title' => 'Požiadavka',
    ],
    'permissions' => [
        'name' => 'Systém',
        'manage_system_settings' => 'Správa systémových nastavení',
        'manage_software_updates' => 'Správa aktualizácií software',
        'access_logs' => 'Zobrazenie systémových záznamov',
        'manage_mail_templates' => 'Správa e-mailových šablón',
        'manage_mail_settings' => 'Správa e-mailových nastavení',
        'manage_other_administrators' => 'Správa ostatných administrátorov',
        'manage_preferences' => 'Správa nastavení administrácie',
        'manage_editor' => 'Správa nastavení editora kódu',
        'view_the_dashboard' => 'Zobrazenie hlavného panelu',
        'manage_branding' => 'Prispôsobenie administrácie',
    ],
    'log' => [
        'menu_label' => 'Nastavenia záznamov',
        'menu_description' => 'Určte, ktoré obasti by mali používať záznamy.',
        'default_tab' => 'Záznamenavánie',
        'log_events' => 'Zaznamenať systémové udalosti',
        'log_events_comment' => 'Uložiť systémové udalosti aj v databáze ako doplnok k ukladaniu v súbore.',
        'log_requests' => 'Zaznamenať chybné požiadavky',
        'log_requests_comment' => 'Požiadavky prehliadača, ktoré môžu vyžadovať pozornosť, napríklad chyby 404.',
        'log_theme' => 'Zaznamenať zmeny témy',
        'log_theme_comment' => 'V prípade, že je téma upravená pomocou administrácie.',
    ],
    'media' => [
        'invalid_path' => "Chybne zadaná cesta: ':path'.",
        'folder_size_items' => 'súborov',
    ],
];
