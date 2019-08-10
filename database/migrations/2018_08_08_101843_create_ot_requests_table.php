<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ot_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->date('otDate');
            $table->unsignedInteger('employee_id')->nullable();
            $table->string('employeeName');
            $table->unsignedInteger('department')->nullable();
            $table->double('allowedHours');
            $table->time('startTime');
            $table->time('endTime');
            $table->string('reason')->nullable();
            $table->boolean('approval')->nullable();
            $table->string('otType')->default('rot');

            $table->foreign('department')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ot_requests');
    }
}
