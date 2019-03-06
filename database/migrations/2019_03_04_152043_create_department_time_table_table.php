<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentTimeTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_time_tables', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('department_id');
            $table->dateTime('timeIn')->nullable();
            $table->dateTime('timeOut')->nullable();
            $table->float('break')->nullable();
            $table->date('startDate');
            $table->date('endDate')->nullable();

            $table->foreign('department_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_time_table');
    }
}
