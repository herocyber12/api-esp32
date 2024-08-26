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
        Schema::create('configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('use_timer')->nullable();
            $table->time('timer_start')->nullable();
            $table->time('timer_end')->nullable();
            $table->boolean('use_limit_rp')->nullable();
            $table->string('reach_limit_rp',255)->nullable();
            $table->date('setup_new_month_start')->nullable();
            $table->date('setup_new_month_end')->nullable();
            $table->string('jenis_tegangan',25)->nullable();
            $table->string('no_hp_target',15)->nullable();
            
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
        Schema::dropIfExists('configs');
    }
};
