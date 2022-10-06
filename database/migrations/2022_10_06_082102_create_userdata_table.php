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
        Schema::create('userdata', function (Blueprint $table) {
            $table->id();
            $table->string('title', 10);
            $table->string('first_name', 50);
            $table->string('middle_name', 50);
            $table->string('last_name', 50);
            $table->string('email_address', 100);
            $table->string('phone_number', 20);
            $table->string('user_role', 10);
            $table->string('id_number', 10);
            $table->string('date_of_birth', 20);
            $table->string('gender', 10);
            $table->string('password', 1000);
            $table->string('app_version', 10);
            $table->string('referral_code', 100);
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
        Schema::dropIfExists('userdata');
    }
};