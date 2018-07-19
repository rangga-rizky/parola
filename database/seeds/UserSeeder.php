<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $limit = 5;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('users')->insert([ //,
                'name' => $faker->name,
                'password' => bcrypt('secret'),
                'email' => $faker->unique()->email,
            ]);
        }
    }
}
