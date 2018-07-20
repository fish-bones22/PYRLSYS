<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserRolesSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert(
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
        );
    }
}
