<?php namespace InitBiz\CumulusCore\Classes;

use InitBiz\CumulusCore\Models\Company;

abstract class GuardBase
{
    protected $moduleSlug;

    public function canEnterModule($companySlug)
    {
        return Company::whereSlug($companySlug)->first()->modules()->whereSlug($this->moduleSlug)->first() ? true : false;
    }

}