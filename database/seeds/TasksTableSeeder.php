<?php

use Illuminate\Database\Seeder;

class TasksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->insert([
        	'id'			=>	1,
        	'user_id'		=>	1,
            'name' 			=> 	'Hobbit',
            'description' 	=> 	'Este usuario es un hobbit de la comarca',
        	'date'			=>	'2017-08-12',
        ]);
    }
}
