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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uofm_id')->unsigned();
            $table->unsignedBigInteger('class_id')->unsigned();
            $table->string('code');
            $table->string('description');
            $table->string('long_description');
            // $table->string('class_id')->references('id')->on('class_items');
            // $table->string('uofm')->references('id')->on('uofms');

            
            $table->foreign('class_id')->references('id')->on('class_items');
            $table->foreign('uofm_id')->references('id')->on('uofms');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
