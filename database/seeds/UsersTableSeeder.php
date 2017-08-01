<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Miguel Quiroz',
            'email' => 'tamquiroz@gmail.com',
            'password' => bcrypt('secret'),
        ]);
    }
}
