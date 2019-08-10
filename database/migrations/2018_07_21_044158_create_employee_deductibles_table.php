<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeDeductiblesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deductibles', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('key', 50);
            $table->unsignedInteger('employee_id');
            $table->string('value');

            $table->foreign('key')->references('key')->on('deductibles')->onDelete('cascade');
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
        Schema::dropIfExists('employee_deductibles');
    }
}
