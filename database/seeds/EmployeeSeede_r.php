<?php

use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employee')->insert(
            [
            'firstName' => 'Victor Samuel',
            'middleName' => 'Villeza',
            'lastName' => 'Quinto',
            'employeeId' => '00001'
            ],
            [
                'firstName' => 'Lorea Frances',
                'middleName' => 'Villeza',
                'lastName' => 'Ricafort',
                'employeeId' => '00002'
            ]
        );
    }
}
