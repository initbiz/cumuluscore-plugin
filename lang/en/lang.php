<?php

declare(strict_types=1);
return [
    'plugin' => [
        'name' => 'CumulusCore',
        'description' => 'Plugin that helps with writing SaaS applications',
    ],

    'navigation' => [
        'main' => 'Cumulus',
        'users' => 'Users',
        'clusters' => 'Clusters',
        'plans' => 'Plans',
    ],

    'users' => [
        'last_first_name' => 'Last and first name',
        'cluster_tab' => 'Clusters',
    ],

    'settings' => [
        'menu_category' => 'Cumulus',
        'menu_features_label' => 'Features',
        'menu_features_description' => 'Manage Cumulus features',
        'clear_features_cache' => 'Clear features cache',
        'features_page_title' => 'Manage Cumulus features',
        'features_list_code' => 'Code',
        'features_list_name' => 'Name',
        'features_list_description' => 'Description',
        'menu_auto_assign_label' => 'Auto assign',
        'menu_auto_assign_description' => 'Auto assigning users and clusters',
        'tab_auto_assign_user' => 'Auto assign users',
        'enable_auto_assign_user' => 'Enable auto assigning users to cluster',
        'enable_auto_assign_user_comment' => 'This will take effect only for users registered using onRegister',
        'auto_assign_user_label' => 'How users are assigned',
        'tab_auto_assign_cluster' => 'Auto assign clusters',
        'new_cluster' => 'Create new cluster',
        'concrete_cluster' => 'Choose existing cluster',
        'get_cluster' => 'Get cluster from a variable',
        'get_cluster_label' => 'Variable name',
        'new_cluster_variable' => 'Variable with value that will be used to create new cluster',
        'concrete_cluster_label' => 'Choose the plan to automatically assign clusters to',
        'enable_auto_assign_user_to_group' => 'Enable auto assigning users to a group',
        'enable_auto_assign_user_to_group_comment' => 'This will take effect only for users registered using onRegister from RainLab.User',
        'group_to_auto_assign_user' => 'Group to auto assign users',
        'enable_auto_assign_cluster' => 'Enable auto assigning clusters to plans',
        'enable_auto_assign_cluster_comment' => 'This will take effect only if creating new clusters is enabled on previous tab',
        'auto_assign_cluster_label' => 'How clusters are assigned',
        'tab_cluster_assign_plan' => 'Auto assign clusters',
        'concrete_plan' => 'Choose concrete plan to assign new clusters to',
        'get_plan' => 'Get plan from a variable',
        'concrete_plan_label' => 'Choose plan',
        'get_plan_label' => 'Variable name',
        'general_label' => 'General',
        'general_description' => 'General Cumulus settings',
        'enable_usernames_in_urls' => 'Enable clusters\' usernames in URLs',
        'enable_usernames_in_urls_comment' => 'Get clusters\' usernames from URL instead of slugs',
    ],

    'permissions' => [
        'cumulus_tab' => 'Cumulus',
        'settings_access_general' => 'Manage general Cumulus settings',
        'settings_access_auto_assign' => 'Manage auto assigning settings',
        'settings_access_manage_features' => 'Manage features settings',
        'access_users' => 'Manage users',
        'access_clusters' => 'Manage clusters',
        'access_plans' => 'Manage plans',
    ],

    'backend_dashboard' => [
        'welcome' => 'Welcome',
        'welcome_message' => 'You will be able to see some useful statistics about your Cumulus system here soon. <br> Right now the feature is under construction.',
    ],

    'menu_item' => [
        'cumulus_page' => 'Cumulus page',
        'cumulus_tab_label' => 'Cumulus',
    ],

    'component_properties' => [
        'cluster_uniq' => 'Cluster unique id',
        'cluster_uniq_desc' => 'Variable from URL with unique identifier of the current cluster',
    ],

    'cumulus_guard' => [
        'name' => 'Cumulus guard',
        'description' => 'Component checking if user can enter cluster page',
    ],

    'feature_guard' => [
        'name' => 'Feature Guard',
        'description' => 'Guard component that allows cluster enter the feature',
        'cumulus_features' => 'Cumulus features',
        'cumulus_features_desc' => 'Pick features to restrict user access',
    ],

    'user_clusters_list' => [
        'name' => 'Clusters list',
        'description' => 'Component showing all clusters that user is assigned to',
        'cluster_dashboard_page' => 'Cluster dashboard page',
        'cluster_dashboard_page_desc' => 'Page where users are going to be redirected after clicking cluster url',
    ],

    'cluster' => [
        'list_title' => 'Manage clusters',
        'cluster' => 'Cluster',
        'delete_confirm' => 'Are you sure you want to delete this cluster?',
        'thoroughfare' => 'Thoroughfare',
        'name' => 'Full name',
        'plan' => 'Plan',
        'slug' => 'Slug',
        'created_at' => 'Created at',
        'username' => 'Username',
        'username_comment' => 'Username is used in URLs instead slug if you enable this feature in settings',
        'logo' => 'Logo',
        'city' => 'City',
        'website' => 'Website',
        'phone' => 'Telephone',
        'postal_code' => 'Postal code',
        'description' => 'Description',
        'email' => 'Email',
        'tax_number' => 'Tax number',
        'account_number' => 'Bank account number',
        'country' => 'Country',
        'additional_details' => 'Additional details',
        'accounting_details_tab' => 'Accounting details',
        'contact_details_tab' => 'Contact details',
        'plan_filter' => 'Select plans',
        'users' => 'Users',
    ],

    'plan' => [
        'plan' => 'Plan',
        'is_trial' => 'Trial plan',
        'is_registration_allowed' => 'Allow users registration for this plan',
        'name' => 'Name',
        'slug' => 'Slug',
        'features' => 'Features',
        'delete_confirm' => 'Are you sure you want to delete this plan?',
        'related_plans_tab' => 'Upgrade / downgrade',
        'related_plans' => 'related plan',
        'priority' => 'Priority',
        'related_plan_relation' => 'Relation',
        'related_plan_downgrade' => 'Downgrade',
        'related_plan_upgrade' => 'Upgrade',
        'related_plan_alternative' => 'Alternative',
    ],

    'plan_list' => [
        'title' => 'Manage plans',
        'name' => 'Name',
        'is_trial' => 'Trial',
        'is_registration_allowed' => 'Registration allowed',
    ],

    'restore' => [
        'flash_success' => 'Items successfully restored',
        'flash_empty' => 'The list to restore cannot be empty',
        'confirmation' => 'Are you sure you want to restore the selected items?',
        'button' => 'Restore',
    ],

    'trashed' => [
        'filter_button' => 'Show trashed',
    ],

    'announcers' => [
        'register_user' => 'New user registered',
        'welcome_messages' => 'Welcome messages',
        'onboarding_users' => 'Onboarding users',
        'onboarding_users' => 'Onboarding users',
        'registered_days_ago_label' => 'Registered days ago',
        'plans_label' => 'Plans',
    ],
];
