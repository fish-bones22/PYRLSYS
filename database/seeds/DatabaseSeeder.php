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
        $this->call(EmployeeSeeder::class);
        //$this->call(UserRolesSeeder::class);
        DB::table('user_roles')->insert([
            [
            'roleName' => 'Accounts Management',
            'description' => 'View, add, update, and delete application users and other misc. information',
            'roleKey' => 'accountsmanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
            'roleName' => 'Human Resource Management',
            'description' => 'View, add , update, and delete applicant and employee records',
            'roleKey' => 'humanresourcemanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
            'roleName' => 'Man Hour Management',
            'description' => 'View, add , update, and delete man hour records',
            'roleKey' => 'manhourmanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ],
            [
            'roleName' => 'Payroll Management',
            'description' => 'View, add , update, and delete payroll records',
            'roleKey' => 'payrollmanagement',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
            ]
        ]);
    }
}
