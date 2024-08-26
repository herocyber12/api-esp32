<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('relay_control', function (Blueprint $table) {
            $table->id();
            $table->enum('state', ['on', 'off']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('relay_control');
    }
};
