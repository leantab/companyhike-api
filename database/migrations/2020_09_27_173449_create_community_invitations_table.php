<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id');
            $table->foreign('community_id')->references('id')->on('communities')->onDelete('cascade');
            $table->string('name');
            $table->string('lastname');
            $table->string('email');
            $table->boolean('accepted')->nullable();
            $table->dateTime('accepted_at')->nullable();
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
        Schema::dropIfExists('community_invitations');
    }
}
