<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeePayTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_pay_table', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->double('rate');
            $table->double('allowance');
            $table->string('rateBasis')->nullable();
            $table->unsignedInteger('employee_id');
            $table->date('startDate')->nullable();
            $table->date('endDate')->nullable();
            $table->unsignedInteger('paymentmode')->nullable();

            $table->foreign('paymentmode')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_pay');
    }
}
