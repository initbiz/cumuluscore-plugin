<?php namespace Initbiz\CumulusCore\Contracts;

interface PlanInterface extends RepositoryInterface
{
    /**
     * Get users from plans slugs array
     * @param  array  $plansSlugs array of plans slugs
     * @return Collection Users of plans
     */
    public function getPlansUsers(array $plansSlugs);
}
