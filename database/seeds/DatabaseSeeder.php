<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(EmployeeSeeder::class);
        //$this->call(UserRolesSeeder::class);
        DB::table('user_roles')->insert([
            [
            'roleName' => 'User Management',
            'description' => 'View, add, update, and delete application users',
            'roleKey' => 'usermanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
            'roleName' => 'Employee Management',
            'description' => 'View, add , update, and delete employee records',
            'roleKey' => 'employeemanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]
        ]);
    }
}
