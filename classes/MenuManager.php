<?php namespace Initbiz\CumulusCore\Classes;

use Url;
use Cms\Classes\Theme;
use Cms\Classes\Layout;
use Cms\Classes\Page as CmsPage;
use October\Rain\Support\Singleton;
use Initbiz\CumulusCore\Classes\Helpers;
use Initbiz\CumulusCore\Repositories\ClusterRepository;

class MenuManager extends Singleton
{
    protected $clusterRepository;

    /**
     * Initialize this singleton.
     */
    protected function init()
    {
        $this->clusterRepository = new ClusterRepository();
    }

    public function hideClusterMenuItems($items)
    {
        $clusterRepository = new ClusterRepository;
        $currentCluster = Helpers::getCluster();

        $iterator = function ($menuItems) use (&$iterator, $clusterRepository, $currentCluster) {
            $result = [];
            foreach ($menuItems as $item) {
                if (isset($item->viewBag['cumulusFeatures']) && $item->viewBag['cumulusFeatures'] !== "0") {
                    $itemFeatures = (array) $item->viewBag['cumulusFeatures'];
                    $item->viewBag['isHidden'] = "1";

                    foreach ($itemFeatures as $feature) {
                        if ($clusterRepository->canEnterFeature($currentCluster, $feature)) {
                            $item->viewBag['isHidden'] = "0";
                            break;
                        }
                    }
                }
                if ($item->items) {
                    $item->items = $iterator($item->items);
                }
                $result[] = $item;
            }
            return $result;
        };
        $items = $iterator($items);
        return $items;
    }

    public function resolveItem($item, $url, $theme)
    {
        if (!$item->cmsPage) {
            return;
        }

        $pageUrl = Helpers::getPageUrl($item->cmsPage, $theme);
        if (!$pageUrl) {
            return;
        }

        $pageUrl = Url::to($pageUrl);
        $result = [];
        $result['url'] = $pageUrl;
        $result['isActive'] = $pageUrl == $url;

        return $result;
    }

    public function getCmsPages()
    {
        $result = null;

        $theme = Theme::getActiveTheme();

        $pages = CmsPage::listInTheme($theme, true);
        $layouts = Layout::listInTheme($theme, true);
        $cmsPages = [];
        foreach ($pages as $page) {
            $cmsPages[] = $page;
        }
        $result['cmsPages'] = $cmsPages;

        return $result;
    }
}
