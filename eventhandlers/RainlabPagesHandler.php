<?php

namespace Initbiz\CumulusCore\EventHandlers;

use System\Classes\PluginManager;
use Initbiz\CumulusCore\Classes\MenuManager;
use Initbiz\CumulusCore\Classes\FeatureManager;

class RainlabPagesHandler
{
    public function subscribe($event)
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Pages')) {
            return;
        }

        $this->addCumulusPageType($event);
        $this->getCumulusPageTypeInfo($event);
        $this->resolveCumulusPageItem($event);
        $this->filterClusterMenuItems($event);
        $this->addCumulusFeaturesToPageType($event);
    }

    public function addCumulusPageType($event)
    {
        $event->listen('pages.menuitem.listTypes', function () {
            return [
                'cumulus-page' => 'initbiz.cumuluscore::lang.menu_item.cumulus_page',
            ];
        });
    }

    public function getCumulusPageTypeInfo($event)
    {
        $event->listen('pages.menuitem.getTypeInfo', function ($type) {
            $result = null;

            if ($type == 'cumulus-page') {
                $menuManager = MenuManager::instance();
                $result = $menuManager->getCmsPages();
            }

            return $result;
        });
    }

    public function resolveCumulusPageItem($event)
    {
        $event->listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            $result = null;

            if ($item->type === 'cumulus-page') {
                $menuManager = MenuManager::instance();
                $result = $menuManager->resolveItem($item, $url, $theme);
            }

            return $result;
        });
    }

    public function filterClusterMenuItems($event)
    {
        $event->listen('pages.menu.referencesGenerated', function (&$items) {
            $menuManager = MenuManager::instance();
            $items = $menuManager->hideClusterMenuItems($items);
        });
    }

    public function addCumulusFeaturesToPageType($event)
    {
        $event->listen('backend.form.extendFields', function ($widget) {
            if (
                !$widget->getController() instanceof \RainLab\Pages\Controllers\Index ||
                !$widget->model instanceof \RainLab\Pages\Classes\MenuItem
            ) {
                return;
            }

            $featureManager = FeatureManager::instance();
            $features = $featureManager->getFeaturesOptions();

            //TODO: this should be added as checkboxlist or taglist, the problem is that
            //      the rainlab/pages/formwidgets/assets/js/menu-items-editor.js
            //      gets and sets values using jquery not PHP to get value
            //      as a consequence it sets only last value to yaml
            //      and cannot set value of select2 (taglist) or checkboxlist
            $featureFields = [];

            foreach ($features as $featureCode => $featureDef) {
                $featureFields['viewBag[cumulusFeature-' . $featureCode . ']'] = [
                    'tab' => 'initbiz.cumuluscore::lang.menu_item.cumulus_tab_label',
                    'label' => $featureDef[0],
                    'commentAbove' => $featureDef[1],
                    'type' => 'checkbox',
                ];
            }
            $widget->addTabFields($featureFields);
        });
    }
}
