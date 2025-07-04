<?php

declare(strict_types=1);
return [
    'plugin' => [
        'name' => 'CumulusCore',
        'description' => 'Plugin do budowania aplikacji w architekturze SaaS',
    ],

    'navigation' => [
        'main' => 'Cumulus',
        'users' => 'Użytkownicy',
        'clusters' => 'Klastry',
        'plans' => 'Plany',
    ],

    'users' => [
        'last_first_name' => 'Imię i nazwisko',
        'cluster_tab' => 'Klastry',
    ],

    'settings' => [
        'menu_category' => 'Cumulus',
        'menu_features_label' => 'Funkcje',
        'menu_features_description' => 'Zarządzanie funkcjami Cumulusa',
        'clear_features_cache' => 'Wyczyść pamięć podręczną funkcji Cumulusa',
        'features_page_title' => 'Zarządzaj funkcjami Cumulusa',
        'features_list_code' => 'Kod',
        'features_list_name' => 'Nazwa',
        'features_list_description' => 'Opis',
        'menu_auto_assign_label' => 'Automatyczne przypisywanie',
        'menu_auto_assign_description' => 'Automatyczne przywpisywanie użytkowników i klastrów',
        'tab_auto_assign_user' => 'Automatyczne przypisywanie użytkowników',
        'enable_auto_assign_user' => 'Włącz automatyczne przypisywanie użytkowników',
        'enable_auto_assign_user_comment' => 'To zadziała wyłącznie dla użytowników zarejestrowanych z użyciem onRegister',
        'auto_assign_user_label' => 'Przypisywanie użytkowników',
        'tab_auto_assign_cluster' => 'Automatyczne przypisywanie klastrów',
        'new_cluster' => 'Stwórz nowy klaster',
        'concrete_cluster' => 'Wybierz istniejący klaster',
        'get_cluster' => 'Weź klaster ze zmiennej',
        'get_cluster_label' => 'Nazwa zmiennej',
        'new_cluster_variable' => 'Zmienna, która zostanie użyta to stworzenia nowego klastra',
        'concrete_cluster_label' => 'Wybierz plan do automatycznego przypisywania klastrów',
        'enable_auto_assign_user_to_group' => 'Włącz automatyczne przypisywanie użytkowników do grupy',
        'enable_auto_assign_user_to_group_comment' => 'To zadziała wyłącznie dla użytkowników zarejestrowanych z użyciem onRegister',
        'group_to_auto_assign_user' => 'Grupa do której zostaną przypisani użytkownicy automatycznie',
        'enable_auto_assign_cluster' => 'Włącz automatyczne przypisywanie klastrów do planów',
        'enable_auto_assign_cluster_comment' => 'To zadziała wyłącznie wtedy, kiedy tworzenie nowych klastrów jest aktywne na poprzedniej zakładce',
        'auto_assign_cluster_label' => 'Automatyczne przypisywanie klastrów',
        'tab_cluster_assign_plan' => 'Automatyczne przypisywanie klastrów',
        'concrete_plan' => 'Wybierz plan do przypisania nowym klastrom',
        'get_plan' => 'Weź plan ze zmiennej',
        'concrete_plan_label' => 'Wybierz plan',
        'get_plan_label' => 'Nazwa zmiennej',
        'general_label' => 'Ogólne',
        'general_description' => 'Ogólne ustawienia Cumulusa',
        'enable_usernames_in_urls' => 'Włącz używanie nazw użytkowników w adresach URL',
        'enable_usernames_in_urls_comment' => 'Używaj nazw użytkowników w adreach URL zamiast slugów',
    ],

    'permissions' => [
        'cumulus_tab' => 'Cumulus',
        'settings_access_general' => 'Zarządzaj ogólnymi ustawieniami Cumulusa',
        'settings_access_auto_assign' => 'Zarządzaj ustawieniami automatycznego przypisywania',
        'settings_access_manage_features' => 'Zarządzaj ustawieniami funkcji Cumulusa',
        'access_users' => 'Zarządzanie użytkownikami',
        'access_clusters' => 'Zarządzanie klastrami',
        'access_plans' => 'Zarządzanie planami',
    ],

    'backend_dashboard' => [
        'welcome' => 'Witaj',
        'welcome_message' => 'Niedługo będziesz w stanie oglądać w tym miejscu przydatne statystyki dotyczące Twojego systemu. <br /> Na obecną chwilę ta funkcja jest w budowie.',
    ],

    'menu_item' => [
        'cumulus_page' => 'Strona Cumulusowa',
        'cumulus_tab_label' => 'Cumulus',
    ],

    'component_properties' => [
        'cluster_uniq' => 'Unikalne ID klastra',
        'cluster_uniq_desc' => 'Zmienna, z której będzie wybrany unikalny identyfikator obecnego klastra',
    ],

    'cumulus_guard' => [
        'name' => 'Cumulus guard',
        'description' => 'Komponent sprawdzający czy użytkownik może wejść na stronę klastra',
    ],

    'feature_guard' => [
        'name' => 'Feature Guard',
        'description' => 'Komponent sprawdzający czy klaster może wejść do danej funkcji Cumulusa',
        'cumulus_features' => 'Funkcje Cumulusa',
        'cumulus_features_desc' => 'Wybierz funkcje Cumulusa, aby ograniczyć dostęp do strony',
    ],

    'user_clusters_list' => [
        'name' => 'Lista klastrów',
        'description' => 'Komponent pokazujący listę wszystkich klastrów, do których przypisany jest użytkownik',
        'cluster_dashboard_page' => 'Strona z ekranem powitalnym klastra',
        'cluster_dashboard_page_desc' => 'Strona, do której użytkownicy zostaną przekierowani po kliknięciu w klaster',
    ],

    'cluster' => [
        'list_title' => 'Zarządzaj klastrami',
        'cluster' => 'Klaster',
        'delete_confirm' => 'Czy jesteś pewny, że chcesz usunąć obecny klaster?',
        'thoroughfare' => 'Linia adresowa',
        'name' => 'Pełna nazwa',
        'plan' => 'Plan',
        'slug' => 'Slug',
        'created_at' => 'Data utworzenia',
        'username' => 'Nazwa użytkownika',
        'username_comment' => 'Nazwa użytkownika jest używana w adresach URL zamiast sluga jeżeli włączysz tę funkcję w ustawieniach',
        'logo' => 'Logo',
        'city' => 'Miasto',
        'website' => 'Strona internetowa',
        'phone' => 'Telefon',
        'postal_code' => 'Kod pocztowy',
        'description' => 'Opis',
        'email' => 'Adres e-mail',
        'tax_number' => 'NIP',
        'account_number' => 'Numer konta bankowego',
        'country' => 'Państwo',
        'additional_details' => 'Dodatkowe szczegóły',
        'accounting_details_tab' => 'Dane finansowe',
        'contact_details_tab' => 'Dane kontaktowe',
        'plan_filter' => 'Wybierz plany',
        'users' => 'Użytkownicy',
    ],

    'plan' => [
        'plan' => 'Plan',
        'is_trial' => 'Plan trialowy',
        'is_registration_allowed' => 'Zezwalaj na rejestrację nowych użytkowników do tego planu',
        'name' => 'Nazwa',
        'slug' => 'Slug',
        'features' => 'Funkcje Cumulusa',
        'delete_confirm' => 'Czy jesteś pewny, że chcesz usunąć ten plan?',
        'related_plans_tab' => 'Upgrade / downgrade',
        'related_plans' => 'powiązany plan',
        'priority' => 'Priorytet',
        'related_plan_relation' => 'Powiązanie',
        'related_plan_downgrade' => 'Downgrade',
        'related_plan_upgrade' => 'Upgrade',
        'related_plan_alternative' => 'Alternatywny',
    ],

    'plan_list' => [
        'title' => 'Zarządzaj planami',
        'name' => 'Nazwa',
        'is_trial' => 'Trial',
        'is_registration_allowed' => 'Rejestracja dozwolona',
    ],

    'restore' => [
        'flash_success' => 'Pomyślnie przywrócono elementy',
        'flash_empty' => 'Lista elementów do przywrócenia nie może być pusta',
        'confirmation' => 'Czy na pewno przywrócić zaznaczone elementy?',
        'button' => 'Przywróć',
    ],

    'trashed' => [
        'filter_button' => 'Pokaż z kosza',
    ],

    'announcers' => [
        'register_user' => 'Nowy użytkownik zarejestrowany',
        'welcome_messages' => 'Wiadomości powitalne',
        'onboarding_users' => 'Wiadomości wdrożeniowe',
    ],
];
