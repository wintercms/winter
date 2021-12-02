<?php

return [
    'cms_object' => [
        'invalid_file' => 'Nom de fichier invalide : :name. Les noms de fichiers ne peuvent contenir que des caractères alphanumériques, des tirets bas, des tirets et des points. Voici des exemples de noms de fichiers valides : page.htm, ma-page, sous_repertoire/nouvelle.page',
        'invalid_property' => 'L’attribut ":name" ne peut pas être défini',
        'file_already_exists' => 'Le fichier ":name" existe déjà.',
        'error_saving' => 'Erreur lors de l’enregistrement du fichier ":name". Veuillez vérifier les permissions en écriture.',
        'error_creating_directory' => 'Erreur lors de la création du répertoire :name. Veuillez vérifier les permissions en écriture.',
        'invalid_file_extension' => 'Extension de fichier invalide : :invalid. Les extensions autorisées sont : :allowed.',
        'error_deleting' => 'Erreur lors de la suppression du modèle ":name". Veuillez vérifier les permissions en écriture.',
        'delete_success' => 'Les modèles ont été supprimés avec succès : :count.',
        'file_name_required' => 'Le nom du fichier est requis.',
        'safe_mode_enabled' => 'Le mode protégé est activé.',
    ],
    'dashboard' => [
        'active_theme' => [
            'widget_title_default' => 'Site Web',
            'online' => 'En ligne',
            'maintenance' => 'En cours de maintenance',
            'manage_themes' => 'Gestion des thèmes',
            'customize_theme' => 'Personnaliser le thème',
        ]
    ],
    'theme' => [
        'not_found_name' => 'Le thème ":name" n’a pas été trouvé.',
        'by_author' => 'Par :name',
        'active' => [
            'not_set' => 'Aucun thème n’est activé.',
            'not_found' => 'Le thème activé est introuvable.',
        ],
        'edit' => [
            'not_set' => 'Le thème à modifier n’est pas activé.',
            'not_found' => 'Le thème à modifier est introuvable.',
            'not_match' => 'L’objet auquel vous souhaitez accéder n’appartient pas au thème en cours de modification. Veuillez recharger la page.'
        ],
        'settings_menu' => 'Thème frontal',
        'settings_menu_description' => 'Aperçu des thèmes installés et sélection du thème actif.',
        'default_tab' => 'Propriétés',
        'name_label' => 'Nom',
        'name_create_placeholder' => 'Nom du nouveau thème',
        'author_label' => 'Auteur',
        'author_placeholder' => 'Nom de la personne ou de la compagnie',
        'description_label' => 'Description',
        'description_placeholder' => 'Description du thème',
        'homepage_label' => 'Page d’accueil',
        'homepage_placeholder' => 'Adresse URL du site Web',
        'code_label' => 'Code',
        'code_placeholder' => 'Un nom de code unique pour la distribution de ce thème',
        'preview_image_label' => 'Aperçu',
        'preview_image_placeholder' => 'Chemin de l’aperçu.',
        'dir_name_label' => 'Nom du répertoire',
        'dir_name_create_label' => 'Le répertoire de destination du thème',
        'theme_label' => 'Thème',
        'theme_title' => 'Thèmes',
        'activate_button' => 'Activer',
        'active_button' => 'Activer',
        'customize_theme' => 'Personnaliser le thème',
        'customize_button' => 'Personnaliser',
        'duplicate_button' => 'Dupliquer',
        'duplicate_title' => 'Dupliquer le thème',
        'duplicate_theme_success' => 'Duplication réalisée avec succès !',
        'manage_button' => 'Gérer',
        'manage_title' => 'Gérer le thème',
        'edit_properties_title' => 'Thème',
        'edit_properties_button' => 'Modifier les propriétés',
        'save_properties' => 'Enregistrer les propriétés',
        'import_button' => 'Importer',
        'import_title' => 'Importer le thème',
        'import_theme_success' => 'Thème importé avec succès !',
        'import_uploaded_file' => 'Fichier archive du thème',
        'import_overwrite_label' => 'Écraser les fichiers existants',
        'import_overwrite_comment' => 'Décocher cette case pour importer uniquement les nouveaux fichiers',
        'import_folders_label' => 'Répertoires',
        'import_folders_comment' => 'Sélectionner les répertoires du thème à importer',
        'export_button' => 'Exporter',
        'export_title' => 'Exporter le thème',
        'export_folders_label' => 'Répertoire',
        'export_folders_comment' => 'Sélectionner les répertoires du thème à exporter',
        'delete_button' => 'Supprimer',
        'delete_confirm' => 'Confirmer la suppression de ce thème ? Cette action est irréversible !',
        'delete_active_theme_failed' => 'Impossible de supprimer le thème actif, merci d’activer un autre thème au préalable.',
        'delete_theme_success' => 'Thème supprimé avec succès !',
        'create_title' => 'Créer un thème',
        'create_button' => 'Créer',
        'create_new_blank_theme' => 'Créer un nouveau thème vierge',
        'create_theme_success' => 'Thème créé avec succès !',
        'create_theme_required_name' => 'Saisir un nom pour ce thème.',
        'new_directory_name_label' => 'Répertoire du thème',
        'new_directory_name_comment' => 'Indiquer un nouveau nom de répertoire pour le thème en dupliqué.',
        'dir_name_invalid' => 'Le nom doit contenir uniquement des chiffres, des symboles latins et les symboles suivants : _-',
        'dir_name_taken' => 'Le nom du répertoire indiqué existe déjà.',
        'find_more_themes' => 'Trouver davantage de thèmes sur le site du CMS Winter.',
        'saving' => 'Enregistrement du thème en cours…',
        'return' => 'Retourner à la liste des thèmes',
    ],
    'maintenance' => [
        'settings_menu' => 'Maintenance',
        'settings_menu_description' => 'Configurer la page de maintenance et ajuster ses options.',
        'is_enabled' => 'Activer la maintenance',
        'is_enabled_comment' => 'Si activé, la page choisie ci-dessous sera affichée pour les visiteurs du site Web.',
        'hint' => 'Le mode maintenance affichera la page de maintenance pour les visiteurs qui ne sont pas authentifiés dans l’interface d’administration.',
        'allowed_ips' => [
            'name' => 'Adresses IP autorisées',
            'description' => 'Adresses IP autorisées à consulter le site lorsque le mode de maintenance est actif.',
            'prompt' => 'Ajouter une adresse IP',
            'ip' => 'Adresse IP',
            'label' => 'Description',
        ],
    ],
    'page' => [
        'not_found_name' => 'La page ":name" est introuvable',
        'not_found' => [
            'label' => 'La page est introuvable',
            'help' => 'La page demandée est introuvable.',
        ],
        'custom_error' => [
            'label' => 'Erreur sur la page',
            'help' => 'Nous sommes désolés, un problème est survenu et la page ne peut être affichée.',
        ],
        'menu_label' => 'Pages',
        'unsaved_label' => 'Page(s) non enregistrée(s)',
        'no_list_records' => 'Aucune page n’a été trouvée',
        'new' => 'Nouvelle page',
        'invalid_url' => 'Format d’adresse URL invalide. L’adresse URL doit commencer par un / et peut contenir des chiffres, des lettres et les symboles suivants : ._-[]:?|/+*^$',
        'delete_confirm_multiple' => 'Confirmer la suppression des pages sélectionnées ?',
        'delete_confirm_single' => 'Confirmer la suppression de cette page ?',
        'no_layout' => '-- aucune maquette --',
        'cms_page' => 'Page CMS',
        'title' => 'Titre de la page',
        'url' => 'URL de la page',
        'file_name' => 'Nom du fichier de la page'
    ],
    'layout' => [
        'not_found_name' => 'La maquette ":name" est introuvable',
        'menu_label' => 'Maquettes',
        'unsaved_label' => 'Maquette(s) non enregistrée(s)',
        'no_list_records' => 'Aucune maquette n’a été trouvée',
        'new' => 'Nouvelle maquette',
        'delete_confirm_multiple' => 'Confirmer la suppression des maquettes sélectionnées ?',
        'delete_confirm_single' => 'Confirmer la suppression de cette maquette ?'
    ],
    'partial' => [
        'not_found_name' => 'Le modèle partiel ":name" est introuvable.',
        'invalid_name' => 'Nom du modèle partiel invalide : :name.',
        'menu_label' => ' Modèles partiels',
        'unsaved_label' => 'Modèle(s) partiel(s) non enregistré(s)',
        'no_list_records' => 'Aucun modèle partiel n’a été trouvé',
        'delete_confirm_multiple' => 'Confirmer la suppression des modèles partiels sélectionnés ?',
        'delete_confirm_single' => 'Confirmer la suppression de ce modèle partiel ?',
        'new' => 'Nouveau modèle partiel'
    ],
    'content' => [
        'not_found_name' => 'Le fichier de contenu ":name" est introuvable.',
        'menu_label' => 'Contenu',
        'unsaved_label' => 'Contenu non enregistré',
        'no_list_records' => 'Aucun fichier de contenu n’a été trouvé',
        'delete_confirm_multiple' => 'Confirmer la suppression des fichiers de contenu ou répertoires sélectionnés ?',
        'delete_confirm_single' => 'Confirmer la suppression de ce fichier de contenu ?',
        'new' => 'Nouveau fichier de contenu'
    ],
    'ajax_handler' => [
        'invalid_name' => 'Nom du gestionnaire AJAX invalide : :name.',
        'not_found' => 'Le gestionnaire AJAX ":name" est introuvable.',
    ],
    'cms' => [
        'menu_label' => 'CMS'
    ],
    'sidebar' => [
        'add' => 'Ajouter',
        'search' => 'Rechercher…'
    ],
    'editor' => [
        'settings' => 'Configuration',
        'title' => 'Titre',
        'new_title' => 'Nouveau titre de la page',
        'url' => 'Adresse URL',
        'filename' => 'Nom du fichier',
        'layout' => 'Maquette',
        'description' => 'Description',
        'preview' => 'Aperçu',
        'meta' => 'Meta',
        'meta_title' => 'Meta Titre',
        'meta_description' => 'Meta Description',
        'markup' => 'Balisage',
        'code' => 'Code',
        'content' => 'Contenu',
        'hidden' => 'Caché',
        'hidden_comment' => 'Les pages cachées sont seulement accessibles aux administrateurs connectés.',
        'enter_fullscreen' => 'Activer le mode plein écran',
        'exit_fullscreen' => 'Annuler le mode plein écran',
        'open_searchbox' => 'Ouvrir la boîte de dialogue Rechercher',
        'close_searchbox'  => 'Fermer la boîte de dialogue Rechercher',
        'open_replacebox' => 'Ouvrir la boîte de dialogue Remplacer',
        'close_replacebox'  => 'Fermer la boîte de dialogue Remplacer',
        'commit' => 'Envoyer',
        'reset' => 'Rétablir',
        'commit_confirm' => 'Êtes-vous sûr de vouloir envoyer vos changements apportés à ce fichier au système de fichier? Cela écrasera le fichier existant sur le système de fichier',
        'reset_confirm' => 'Êtes-vous sûr de vouloir rétablir ce fichier depuis la version présente sur le système de fichier? Cela le remplacera totalement par la version présente sur le système de fichier',
        'committing' => 'Envoi',
        'resetting' => 'Rétablissement',
        'commit_success' => 'Le :type a été envoyé au système de fichier',
        'reset_success' => 'Le :type a été rétabli depuis la version du système de fichier',
    ],
    'asset' => [
        'menu_label' => 'Assets',
        'unsaved_label' => 'Asset(s) non sauvegardé(s)',
        'drop_down_add_title' => 'Ajouter…',
        'drop_down_operation_title' => 'Action…',
        'upload_files' => 'Déposer des fichiers',
        'create_file' => 'Créer un fichier',
        'create_directory' => 'Créer un répertoire',
        'directory_popup_title' => 'Nouveau répertoire',
        'directory_name' => 'Nom du répertoire',
        'rename' => 'Renommer',
        'delete' => 'Supprimer',
        'move' => 'Déplacer',
        'select' => 'Sélectionner',
        'new' => 'Nouveau fichier',
        'invalid_path' => 'Le chemin doit contenir uniquement des chiffres, des lettres, des espaces et les symboles suivants : ._-/',
        'error_deleting_file' => 'Erreur lors de la suppression du fichier :name.',
        'error_deleting_dir_not_empty' => 'Erreur lors de la suppression du répertoire :name. Le répertoire n’est pas vide.',
        'error_deleting_dir' => 'Erreur lors de la suppression du répertoire :name.',
        'invalid_name' => 'Le nom doit contenir uniquement des chiffres, des lettres, des espaces et les symboles suivants : ._-',
        'original_not_found' => 'Le fichier original ou son répertoire est introuvable',
        'already_exists' => 'Un fichier ou un répertoire avec le même nom existe déjà',
        'error_renaming' => 'Erreur lors du renommage du fichier ou du répertoire',
        'name_cant_be_empty' => 'Le nom ne peut être vide',
        'too_large' => 'Le fichier téléchargé est trop volumineux. La taille maximum autorisée est de :max_size',
        'type_not_allowed' => 'Les types de fichiers autorisés sont les suivants : :allowed_types',
        'file_not_valid' => 'Fichier invalide',
        'error_uploading_file' => 'Erreur lors du dépôt du fichier ":name" : :error',
        'move_destination' => 'Répertoire de destination',
        'move_popup_title' => 'Déplacer les assets',
        'selected_files_not_found' => 'Les fichiers sélectionnés sont introuvables',
        'select_destination_dir' => 'Veuillez sélectionner un répertoire de destination',
        'destination_not_found' => 'Le répertoire de destination est introuvable',
        'error_moving_file' => 'Erreur lors du déplacement du fichier :file',
        'error_moving_directory' => 'Erreur lors du déplacement du répertoire :dir',
        'error_deleting_directory' => 'Erreur lors de la suppression du répertoire d’origine :dir',
        'no_list_records' => 'Aucun fichier trouvé',
        'delete_confirm' => 'Supprimer les fichiers ou répertoires sélectionnés ?',
        'path' => 'Chemin'
    ],
    'component' => [
        'menu_label' => 'Composants',
        'unnamed' => 'Sans nom',
        'no_description' => 'Aucune description n’a été fournie',
        'alias' => 'Alias',
        'alias_description' => 'Nom unique fourni lors de l’utilisation du composant sur une page ou une maquette.',
        'validation_message' => 'Les alias du composant sont requis et doivent contenir uniquement des symboles latins, des chiffres et des tirets bas. Les alias doivent commencer par un symbole latin.',
        'invalid_request' => 'Le modèle ne peut être enregistré car les données d’un composant ne sont pas valides.',
        'no_records' => 'Aucun composant n’a été trouvé',
        'not_found' => 'Le composant ":name" est introuvable.',
        'no_default_partial' => 'Ce composant n’as aucun partiel par défaut',
        'method_not_found' => 'Le composant ":name" ne contient pas de méthode ":method".',
        'soft_component' => 'Composant Soft',
        'soft_component_description' => 'Ce composant est manquant mais facultatif.',
    ],
    'template' => [
        'invalid_type' => 'Type de modèle inconnu.',
        'not_found' => 'Le modèle est introuvable.',
        'saved'=> 'Le modèle a été sauvegardé avec succès.',
        'no_list_records' => 'Aucun enregistrement trouvé',
        'delete_confirm' => 'Supprimer les modèles sélectionnés ?',
        'order_by' =>'Trier par'
    ],
    'permissions' => [
        'name' => 'CMS',
        'manage_content' => 'Gérer le contenu du site web',
        'manage_assets' => 'Gérer les assets site web - images, fichiers JavaScript et CSS',
        'manage_pages' => 'Créer, modifier et supprimer des pages du site web',
        'manage_layouts' => 'Créer, modifier et supprimer des maquettes du CMS',
        'manage_partials' => 'Créer, modifier et supprimer des modèles partiels du CMS',
        'manage_themes' => 'Activer, désactiver et configurer les thèmes',
        'manage_theme_options' => 'Gérer les options de personnalisation du thème actif',
    ],
    'theme_log' => [
        'hint' => 'Ce journal affiche tous les changements fait sur le thème actif par les administrateurs via le panneau d’administration.',
        'menu_label' => 'Journal du thème',
        'menu_description' => 'Affiche la liste des modifications apportées au thème actif.',
        'empty_link' => 'Purger le journal du thème',
        'empty_loading' => 'Purge du journal du thème...',
        'empty_success' => 'Journal du thème purgé avec succès',
        'return_link' => 'Retourner au journal du thème',
        'id' => 'ID',
        'id_label' => 'ID du journal',
        'created_at' => 'Date & Heure',
        'user' => 'Utilisateur',
        'type' => 'Type',
        'type_create' => 'Créer',
        'type_update' => 'Modifier',
        'type_delete' => 'Supprimer',
        'theme_name' => 'Thème',
        'theme_code' => 'Code du thème',
        'old_template' => 'Modèle (Ancien)',
        'new_template' => 'Modèle (Nouveau)',
        'template' => 'Modèle',
        'diff' => 'Changements',
        'old_value' => 'Ancienne valeur',
        'new_value' => 'Nouvelle valeur',
        'preview_title' => 'Changement du modèle',
        'template_updated' => 'Le modèle a été modifié',
        'template_created' => 'Le modèle a été créé',
        'template_deleted' => 'Le modèle a été supprimé',
    ],
];
