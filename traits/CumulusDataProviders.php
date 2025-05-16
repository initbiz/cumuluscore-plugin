<?php

declare(strict_types=1);

namespace Initbiz\CumulusCore\Traits;

use Faker\Factory as Faker;

/**
 * CumulusDataProviders is a trait with data providers for Testing
 */
trait CumulusDataProviders
{
    public function fakeUserData()
    {
        $faker = Faker::create();
        $data = [
            'name' => $faker->firstName,
            'surname' => $faker->lastName,
            'email' => $faker->email,
            'phone_no' => $faker->phoneNumber,
            'password' => $faker->password,
            'tax_number' => "1234123423",
            'thoroughfare' => $faker->streetName,
            'premise' => $faker->buildingNumber,
            'postal_code' => $faker->postcode,
            'city' => $faker->city,
            'country_code' => $faker->countryCode,
            'account_no' => $faker->iban($faker->countryCode),
            'username' => $faker->userName,
            'terms_acceptance' => 'on',
        ];
        return $data;
    }

    public function fakeClusterData()
    {
        $faker = Faker::create();
        $data = [
            'name' => $faker->company
        ];
        return $data;
    }

    public function providerUserData()
    {
        $data = $this->fakeUserData();
        return [
            ["userData" => $data]
        ];
    }
    public function providerClusterData()
    {
        $data = $this->fakeClusterData();
        return [
            ["clusterData" => $data]
        ];
    }

    public function providerUserWithClusterData()
    {
        $cluster = $this->providerClusterData();
        $user  = $this->providerUserData();
        return [
            [
                "userData" => $user[0]['userData'],
                "clusterData" => $cluster[0]['clusterData']
            ]
        ];
    }
}
