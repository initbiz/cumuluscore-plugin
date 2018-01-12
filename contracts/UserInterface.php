<?php namespace Initbiz\CumulusCore\Contracts;

interface UserInterface extends RepositoryInterface {

    public function getUserClusterList(int $userId);


}