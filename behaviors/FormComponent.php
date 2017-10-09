<?php namespace Initbiz\CumulusCore\Behaviors;


use ApplicationException;

class FormComponent extends ComponentBehavior
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
}

