<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\EventHandlers;

use Backend\Facades\BackendMenu;
use October\Rain\Events\Dispatcher;
use RainLab\User\Controllers\Users;
use Backend\Classes\NavigationManager;
use RainLab\User\Controllers\UserGroups;

class BackendNavigationHandler
{
    public function subscribe(Dispatcher $event)
    {
        if (\App::runningInBackend()) {
            $this->users($event);
        }
    }

    protected function users(Dispatcher $event)
    {
        $event->listen('backend.menu.extendItems', function (NavigationManager $manager) {
            $sideMenuItem = $manager->getSideMenuItem('RainLab.Users', 'user', 'topics');
            $config = $sideMenuItem->getConfig();
            $config['order'] = 230;
            $manager->removeMainMenuItem('Initbiz.KnowledgeBase', 'knowledge_base');
            $manager->addSideMenuItem('Initbiz.EfficientCompany', 'company', 'knowledge_base', $config);
        });

        Users::extend(function ($controller) {
            $controller->bindEvent('page.beforeDisplay', function () {
                BackendMenu::setContext('Initbiz.EfficientCompany', 'company', 'knowledge_base');
            });
        });

        UserGroups::extend(function ($controller) {
            $controller->bindEvent('page.beforeDisplay', function () {
                BackendMenu::setContext('Initbiz.EfficientCompany', 'company', 'knowledge_base');
            });
        });
    }
}
