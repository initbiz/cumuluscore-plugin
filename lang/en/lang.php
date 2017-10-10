<?php return [
    'plugin' => [
        'name' => 'CumulusCore',
        'description' => 'Plugin that helps with writing SaaS applications'
    ],
    'navigation' => [
        'main' => 'Cumulus',
        'users' => 'Users',
        'clusters' => 'Clusters',
        'plans' => 'Plans'
    ],
    'users' => [
        'last_first_name' => "Last and first name"
    ],
    'settings' => [
        'users' => 'Users',
        'menu_label' => 'Modules',
        'menu_description' => 'Manage Cumulus modules',
        'menu_category' => 'Cumulus',
    ],
    'cluster_dashboard' => [
        'name' => 'Cluster dashboard',
        'description' => 'Show cluster dashboard',
        'cluster_slug' => 'Cluster slug',
        'cluster_slug_desc' => 'Slug of cluster that dashboard is going to be shown',
    ],
    'menu' => [
        'name' => 'Menu Component',
        'description' => 'Component that renders menu based on menuItems component',
        'cluster_slug' => 'Cluster slug',
        'cluster_slug_desc' => 'Cluster slug defining for which cluster menu should be shown',
    ],
    'menu_item' => [
        'name' => 'Menu item',
        'description' => 'Component that is going to be used on pages that we want to show in navigation',
        'menu_item_title' => 'Menu item title',
        'menu_item_title_desc' => 'User friendly title to be shown on button to this page',
        'cumulus_module' => 'Module to restrict access',
        'cumulus_module_desc' => 'Pick module to restrict user visibility using the module permissions',
        'cumulus_module_none' => 'No special privileges'

    ],
    'cumulus_guard' => [
        'name' => 'Cumulus guard',
        'description' => 'Component checking if user can enter cluster page',
        'cluster_slug' => 'Cluster slug',
        'cluster_slug_desc' => 'Slug of a cluster where user assignment is tested',
    ],
    'module_guard' => [
        'name' => 'Module Guard',
        'description' => 'Guard component that allows cluster enter the module',
        'cluster_slug' => 'Cluster slug',
        'cluster_slug_desc' => 'Slug of a cluster that is going to be tested in module privileges',
        'cumulus_module' => 'Cumulus Module',
        'cumulus_module_desc' => 'Pick module to restrict user access'
    ],
    'user_clusters_list' => [
        'name' => 'Clusters list',
        'description' => 'Component showing all clusters that user is assigned to',
        'cluster_dashboard_page' => 'Cluster dashboard page',
        'cluster_dashboard_page_desc' => 'Page where users are going to be redirected after clicking cluster url'
    ]
];
