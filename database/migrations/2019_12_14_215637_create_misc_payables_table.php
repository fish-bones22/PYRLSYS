<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMiscPayablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('misc_payables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('employee_id')->nullable();
            $table->unsignedInteger('department_id')->nullable();
            $table->string('timeCard')->nullable();
            $table->string('employeeName')->nullable();
            $table->date('recordDate')->nullable();
            $table->float('amount')->nullable();
            $table->string('key')->nullable();
            $table->string('displayName')->nullable();
            $table->string('details')->nullable();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('misc_payables');
    }
}
