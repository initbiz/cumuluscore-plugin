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
        'menu_category' => 'Cumulus',
        'menu_modules_label' => 'Modules',
        'menu_modules_description' => 'List Cumulus modules',
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
        'concrete_cluster_label' => 'Choose the cluster to automatically assign users to',
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
        'concrete_cluster_label' => 'Choose the plan to automatically assign clusters to',
    ],
    'cluster_dashboard' => [
        'name' => 'Cluster dashboard',
        'description' => 'Show cluster dashboard',
    ],
    'menu' => [
        'name' => 'Menu Component',
        'description' => 'Component that renders menu based on menuItems component',
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
    'component_properties' => [
        'cluster_slug' => 'Cluster slug',
        'cluster_slug_desc' => 'Variable from URL with slug of the current cluster',
    ],
    'cumulus_guard' => [
        'name' => 'Cumulus guard',
        'description' => 'Component checking if user can enter cluster page',
    ],
    'module_guard' => [
        'name' => 'Module Guard',
        'description' => 'Guard component that allows cluster enter the module',
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
