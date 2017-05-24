<?php namespace InitBiz\CumulusCore\Behaviors;


use ApplicationException;

class ListComponent extends ComponentBehavior
{

    /**
     * ListComponent constructor.
     */
    public function __construct($component)
    {
        parent::__construct($component);

    }

    public function defineProperties()
    {
        return [];
    }

    public function listProperties()
    {
        return [
            'updatePage' => [
                'page'        => 'Page to update record',
                'description' => 'Pick the page where records update component is embedded',
                'type'        => 'dropdown'
            ],
            'createPage' => [
                'page'        => 'Page to create record',
                'description' => 'Pick the page where records create component is embedded',
                'type'        => 'dropdown'
            ]
        ];
    }
}
