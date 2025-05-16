<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Tests\Classes;

use Faker\Factory as Faker;
use RainLab\User\Models\User;
use Initbiz\CumulusCore\Models\Cluster;

class DataProvider
{
    public static function getFaker()
    {
        $faker = Faker::create();
        $faker->addProvider(new \Faker\Provider\pl_PL\Payment($faker));
        $faker->addProvider(new \Faker\Provider\pl_PL\Person($faker));
        return $faker;
    }

    public static function fakeCluster()
    {
        $faker = self::getFaker();

        $cluster = new Cluster();

        $cluster->name              = $faker->company;
        $cluster->phone             = $faker->phoneNumber;
        $cluster->email             = $faker->email;
        $cluster->tax_number        = $faker->taxpayerIdentificationNumber;
        $cluster->thoroughfare      = $faker->streetName . ' ' . $faker->buildingNumber;
        $cluster->postal_code       = $faker->postcode;
        $cluster->city              = $faker->city;
        $cluster->country_id        = 179;
        $cluster->description       = $faker->sentence;
        $cluster->account_number    = $faker->iban($faker->countryCode);

        return $cluster;
    }

    public static function fakeUser()
    {
        $faker = self::getFaker();

        $user = new User();
        $password = $faker->password(8, 20);

        $user->first_name = $faker->firstName;
        $user->last_name = $faker->lastName;
        $user->email = $faker->email;
        $user->phone = $faker->phoneNumber;
        $user->mobile = $faker->phoneNumber;
        $user->street_addr = $faker->streetName . ' ' . $faker->buildingNumber;
        $user->zip = $faker->postcode;
        $user->city = $faker->city;
        $user->country = 179;
        $user->password = $password;
        $user->password_confirmation = $password;

        return $user;
    }
}
