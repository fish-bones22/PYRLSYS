<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Contracts\IEmployeeService;
use App\Entities\EmployeeEntity;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmployeeTest extends TestCase
{
    private $employeeService;

    // public function __construct(IEmployeeService $employeeService) {
    //     $this->employeeService = $employeeService;
    // }

    public function testEmployeeService() {



    }

    private function generateTestDetails() {

        $entity = array();
        // Civil status
        $entity['civilstatus'] = [
            'key' => 'civilstatus',
            'value' => 'Single',
            'displayName' => 'Civil Status'
        ];

        // Spouse
        $entity['spouse'] = array();
        for ($i = 0; $i < 1; $i++) {

            $entity['spouse'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => 0,
                    'value' => $this->faker->lastName(),
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => 0,
                    'value' => $this->faker->firstName(),
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => 0,
                    'value' => $this->faker->lastName(),
                    'displayName' => 'Middle Name'
                ]
            ];
        }

        // Dependent
        $entity['dependent'] = array();
        for($i = 0; $i < 2; $i++) {
            $entity['dependent'][] = [
                'lastname' => [
                    'key' => 'lastname',
                    'grouping' => $i,
                    'value' => $this->faker->lastName(),
                    'displayName' => 'Last Name'
                ],
                'firstname' => [
                    'key' => 'firstname',
                    'grouping' => $i,
                    'value' => $this->faker->firstName(),
                    'displayName' => 'First Name'
                ],
                'middlename' => [
                    'key' => 'middlename',
                    'grouping' => $i,
                    'value' => $this->faker->lastName(),
                    'displayName' => 'Middle Name'
                ],
                'relationship' => [
                    'key' => 'relationship',
                    'grouping' => $i,
                    'value' => 'Sister',
                    'displayName' => 'Relationship'
                ]
            ];
        }

        // Time card
        $entity['timecard'] = [
            'key' => 'timecard',
            'value' => str_random(5),
            'displayName' => 'Time Card'
        ];

        // Position
        $entity['position'] = [
            'key' => 'position',
            'value' => 'Engineer',
            'displayName' => 'Position'
        ];

        // Date hired
        $entity['datehired'] = [
            'key' => 'datehired',
            'value' => Carbon::now(),
            'displayName' => 'Date Hired'
        ];

        // Date End
        $entity['dateend'] = [
            'key' => 'dateend',
            'value' => Carbon::now(11000),
            'displayName' => 'Date End'
        ];

        // Date hired
        $entity['rate'] = [
            'key' => 'rate',
            'value' => 1000,
            'displayName' => 'Hourly Rate'
        ];

        // Allowance
        $entity['allowance'] = [
            'key' => 'allowance',
            'value' => 1000,
            'displayName' => 'Allowance'
        ];

        // Number of Memo
        $entity['numberofmemo'] = [
            'key' => 'numberofmemo',
            'value' => 2,
            'displayName' => 'Number of Memo'
        ];

        // Remarks
        $entity['remarks'] = [
            'key' => 'remarks',
            'value' => $this->faker->word(),
            'displayName' => 'Remarks'
        ];
        return $entity;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
