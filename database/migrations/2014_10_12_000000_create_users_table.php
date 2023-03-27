<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('external_provider')->nullable();
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->text('avatar')->nullable();
            $table->string('language')->nullable();
            $table->boolean('studies')->nullable();
            $table->string('education_institute')->nullable();
            $table->string('career')->nullable();
            $table->boolean('works')->nullable();
            $table->string('works_at')->nullable();
            $table->boolean('active')->default(true);

            $table->boolean('ch_admin')->nullable();
            $table->integer('pending_match_invitation')->nullable();
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
        Schema::dropIfExists('users');
    }
}
