<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIp4addressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ip4addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('container_id')->nullable();
            $table->ipAddress('address');
            $table->foreign('container_id')->references('id')->on('containers')->onDelete('set null'); 
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
        Schema::dropIfExists('ip4addresses');
    }
}
