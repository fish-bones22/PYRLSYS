<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEmploymentHistoriesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_histories', function(Blueprint $table) {
            $table->unsignedInteger('employmenttype')->nullable();
            $table->unsignedInteger('status')->nullable();
            $table->unsignedInteger('paymenttype')->nullable();
            $table->unsignedInteger('paymentmode')->nullable();
            $table->decimal('rate');
            $table->decimal('allowance')->nullable();
            $table->time('timein');
            $table->time('timeout');

            $table->foreign('employmenttype')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('status')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('paymenttype')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('paymentmode')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
