<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained();
            $table->foreignId('user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->integer('max_users')->nullable();
            $table->integer('used_users')->default(0);
            $table->integer('max_games')->nullable();
            $table->integer('used_games')->default(0);
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
        Schema::dropIfExists('licenses');
    }
};
