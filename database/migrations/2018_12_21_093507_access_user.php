<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AccessUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('status')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * 
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('access_users');
    }
}
