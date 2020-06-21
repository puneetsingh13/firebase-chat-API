<?php

use Illuminate\Database\Seeder;
use App\User;

class UserTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 10; $i++) { 

            if($i == 0){
                $role = 'ADMIN';
            } else {
                $role = 'User';
            }

	    	User::create([
	            'name' => 'PS-'.uniqid(),
	            'email' => uniqid().'@gmail.com',
                'password' => \Hash::make('123456'),
                'role' => $role
	        ]);
    	}
    }
}
