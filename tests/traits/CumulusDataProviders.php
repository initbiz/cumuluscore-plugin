<?php

use \Faker\Factory as Faker;

/**
 * CustomDataProviders is a trait with data providers for Testing
 */
trait CumulusDataProviders
{
    public function providerUserData()
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
            'terms_acceptance' => 'on',
        ];
        return [
            ["data" => $data]
        ];
    }
    public function providerCompanyData() {
        $faker = Faker::create();
        $data = [
            'company' => $faker->company
        ];
        return [
            ["data" => $data]
        ];
    }
}
