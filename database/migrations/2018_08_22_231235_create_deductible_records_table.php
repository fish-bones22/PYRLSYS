<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeductibleRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductible_records', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('employee_id')->nullable();
            $table->string('employeeName');
            $table->string('identifier')->nullable();
            $table->string('identifierDetails')->nullable();
            $table->unsignedInteger('deductible_id')->nullable();
            $table->date('recordDate');
            $table->string('details')->nullable();
            $table->decimal('amount');
            $table->decimal('subamount')->nullable();
            $table->string('remarks')->nullable();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('deductible_id')->references('id')->on('deductibles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deductibles_records');
    }
}
