<?php

namespace Tests\Browser;

use Faker\Factory as Faker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EmployeeTest extends DuskTestCase
{

    private $faker;

    public function __construct() {
        $this->faker = Faker::create();
    }

    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testEmployee()
    {
        $this->browse(function (Browser $browser) {

            $browser->get('/employee/new')
            ->type($this->faker->firstName(), 'first_name')
            ->type($this->faker->lastName(), 'last_name')
            ->type($this->faker->middleName(), 'middle_name')
            ->type(str_random(10), 'employee_id')
            ->type('m', 'sex')
            ->type('Single', 'civil_status')

            ->type($this->faker->lastName(), 'spouse_last_name[0]')
            ->type($this->faker->firstName(), 'spouse_first_name[0]')
            ->type($this->faker->lastName(), 'spouse_middle_name[0]')

            ->type($this->faker->lastName(), 'dependent_last_name[0]')
            ->type($this->faker->firstName(), 'dependent_first_name[0]')
            ->type($this->faker->lastName(), 'dependent_middle_name[0]')
            ->type('brother', 'dependent_relationship[0]')

            ->type($this->faker->lastName(), 'dependent_last_name[1]')
            ->type($this->faker->firstName(), 'dependent_first_name[1]')
            ->type($this->faker->lastName(), 'dependent_middle_name[1]')
            ->type('sister', 'dependent_relationship[1]')

            ->type(str_random(5), 'time_card')
            ->type('Engineer', 'position')
            ->type(Carbon::now(), 'date_hired')
            ->type(Carbon::now(5000), 'date_end')
            ->type(780, 'rate')
            ->type(1000, 'allowance')
            ->type(2, 'number_of_memo')
            ->type(1000, 'remarks')
            ->press('Save')
            ->assertResponseOk();
        });
    }
}
