<?php namespace InitBiz\CumulusCore\Classes;

use Event;

class Connector
{
    public static function registerNavigation($navigation, $moduleRegisteredName = null)
    {

        Event::listen('initbiz.cumuluscore.menuItems',
            function (&$moduleComponents) use ($navigation, $moduleRegisteredName) {
                foreach ($navigation as $alias => $title) {
                    $moduleComponents[] = [
                        'componentAlias' => $alias,
                        'componentTitle' => $title,
                        'componentModule' => $moduleRegisteredName
                    ];
                }
            });
    }
}
