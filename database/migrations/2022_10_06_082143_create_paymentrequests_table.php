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
        Schema::create('paymentrequests', function (Blueprint $table) {
            $table->id();
            $table->string('requestor_id', 10);
            $table->string('benefactor_id', 10);
            $table->string('amount', 10);
            $table->string('request_title', 200);
            $table->string('request_description', 1000);
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
        Schema::dropIfExists('paymentrequests');
    }
};