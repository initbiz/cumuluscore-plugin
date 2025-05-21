<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore;

use Event;
use Backend;
use System\Classes\PluginBase;
use Initbiz\CumulusCore\Classes\Helpers;

class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User',
        'RainLab.Notify',
        'Initbiz.InitDry',
        'RainLab.Location',
        'RainLab.UserPlus',
    ];

    public function pluginDetails()
    {
        return [
            'name' => 'initbiz.cumuluscore::lang.plugin.name',
            'description' => 'initbiz.cumuluscore::lang.plugin.description',
            'author' => 'Initbiz',
            'icon' => 'oc-icon-cloud'
        ];
    }

    public function boot()
    {
        Event::subscribe(\Initbiz\CumulusCore\EventHandlers\AutoAssignHandler::class);
        Event::subscribe(\Initbiz\CumulusCore\EventHandlers\RainlabPagesHandler::class);
        Event::subscribe(\Initbiz\CumulusCore\EventHandlers\RainlabUserHandler::class);
    }

    public function register()
    {
        $this->registerConsoleCommand(
            'cumulus:clean-keys-file',
            \Initbiz\CumulusCore\Console\CleanClusterKeysFile::class
        );

        $this->registerConsoleCommand(
            'cumulus:extract-cluster-keys-file',
            \Initbiz\CumulusCore\Console\ExtractClusterKeysFile::class
        );
    }

    public function registerComponents()
    {
        return [
            \Initbiz\CumulusCore\Components\FeatureGuard::class =>  'featureGuard',
            \Initbiz\CumulusCore\Components\CumulusGuard::class =>  'cumulusGuard',
            \Initbiz\CumulusCore\Components\UserClustersList::class =>  'clustersList',
        ];
    }

    public function registerSettings()
    {
        return [
            'general' => [
                'label'         => 'initbiz.cumuluscore::lang.settings.general_label',
                'description'   => 'initbiz.cumuluscore::lang.settings.general_description',
                'category'      => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon'          => 'icon-cogs',
                'class'         => \Initbiz\CumulusCore\Models\GeneralSettings::class,
                'permissions'   => ['initbiz.cumuluscore.settings_access_general'],
                'order'         => 100
            ],
            'auto_assign' => [
                'label'         => 'initbiz.cumuluscore::lang.settings.menu_auto_assign_label',
                'description'   => 'initbiz.cumuluscore::lang.settings.menu_auto_assign_description',
                'category'      => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon'          => 'icon-sitemap',
                'class'         => \Initbiz\CumulusCore\Models\AutoAssignSettings::class,
                'permissions'   => ['initbiz.cumuluscore.settings_access_auto_assign'],
                'order'         => 110
            ],
            'features' => [
                'label'         => 'initbiz.cumuluscore::lang.settings.menu_features_label',
                'description'   => 'initbiz.cumuluscore::lang.settings.menu_features_description',
                'category'      => 'initbiz.cumuluscore::lang.settings.menu_category',
                'icon'          => 'icon-cubes',
                'url'           => Backend::url('initbiz/cumuluscore/features'),
                'permissions'   => ['initbiz.cumuluscore.settings_access_manage_features'],
                'order'         => 120
            ]
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'canEnterFeature' => [$this, 'canEnterFeature'],
                'canEnterAnyFeature' => [$this, 'canEnterAnyFeature']
            ]
        ];
    }

    public function registerCumulusAnnouncerTypes()
    {
        return [
            \Initbiz\CumulusCore\AnnouncerTypes\UserRegisterAnnouncerType::class,
        ];
    }

    /**
     * Twig filter method that checks if a user can enter the feature
     *
     * @param string $featureCode
     * @return boolean
     */
    public function canEnterFeature(string $featureCode)
    {
        $cluster = Helpers::getCluster();

        if (!$cluster) {
            return false;
        }

        return $cluster->canEnterFeature($featureCode);
    }

    /**
     * Twig filter method that checks if a user can enter any of the features supplied
     *
     * @param array $featureCodes
     * @return boolean
     */
    public function canEnterAnyFeature($featureCodes)
    {
        $cluster = Helpers::getCluster();

        if (!$cluster) {
            return false;
        }

        if (!is_array($featureCodes)) {
            $featureCodes = (array) $featureCodes;
        }

        return $cluster->canEnterAnyFeature($featureCodes);
    }
}
