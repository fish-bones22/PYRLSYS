<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateManhoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manhours', function(Blueprint $table) {
            $table->time('timeIn')->nullable()->change();
            $table->time('timeOut')->nullable()->change();
        });

        Schema::table('ot_requests', function(Blueprint $table) {
            $table->string('otType')->default('rot');
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
