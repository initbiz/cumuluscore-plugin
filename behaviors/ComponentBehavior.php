<?php namespace Initbiz\CumulusCore\Behaviors;


use ApplicationException;
use October\Rain\Extension\ExtensionBase;

class ComponentBehavior extends ExtensionBase
{

    protected $component;


    /**
     * Constructor.
     */
    public function __construct($component)
    {
        $this->component = $component;
    }

    public function componentMethod()
    {
        dd('component behavior');
    }


}
