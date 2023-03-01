<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

class AccountSeeder extends Seeder
{
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
		    $this->db->table('accounts')->insert($this->generateAccount());
        }
    }

    private function generateAccount(){
        $faker = Factory::create();

        return[
            'username' => $faker->username(),
            'email' => $faker->email,
            'password'=> $faker->password,
        ];
    }
}
