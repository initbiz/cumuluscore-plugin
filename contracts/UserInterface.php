<?php namespace Initbiz\CumulusCore\Contracts;

interface UserInterface extends RepositoryInterface
{
    public function getUserClusterList(int $userId);

    public function getActivatedUsers($columns = array('*'));
}
