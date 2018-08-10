<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManhoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manhours', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('employee_id')->nullable();
            $table->unsignedInteger('department')->nullable();
            $table->unsignedInteger('outlier')->nullable();
            $table->string('employeeName');
            $table->string('timeCard');
            $table->date('recordDate');
            $table->time('timeIn')->nullable();
            $table->time('timeOut')->nullable();
            $table->string('remarks');
            $table->boolean('authorized')->nullable();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('department')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('outlier')->references('id')->on('categories')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manhour');
    }
}
