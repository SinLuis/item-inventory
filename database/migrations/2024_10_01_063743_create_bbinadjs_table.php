<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bbinadjs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bbin_id')->unsigned();
            $table->foreign('bbin_id')->references('id')->on('bbins');
            $table->integer('qty_before');
            $table->integer('qty_after');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('user_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bbinadj');
    }
};