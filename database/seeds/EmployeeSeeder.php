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
       factory(App\Employee::class, 5)->create()->each(function($employee) {
            $employee->details()->save(factory(App\EmployeeDetail::class)->make());
       });
    }
}
