<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeOfferingSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_offering_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('offering_id');
            $table->dateTime('effective_date');
            $table->dateTime('execute_date');
            $table->string('remark');
            $table->boolean('completed')->default(false);
            $table->integer('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_offering_schedules');
    }
}
