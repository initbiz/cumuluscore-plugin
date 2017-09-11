<?php namespace InitBiz\CumulusCore\Tests\Traits;

use \InitBiz\CumulusCore\Models\Company;
use \RainLab\User\Models\User;
trait SetUp {

    public function createCompany()
    {
        return Company::create([
            'full_name' => 'Foo Bar',
            'slug' => 'foo-bar'
        ]);
    }

    public function createUser()
    {
        return User::create([
            'name' => 'Jan',
            'surname' => 'Kowalski',
            'email' => 'jan.kowalski@init.biz',
            'password' => 'password123',
        ]);
    }
}