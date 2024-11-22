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
        Schema::create('subkontraks', function (Blueprint $table) {
            $table->id();
            $table->string('reff_number');
            $table->string('reff_date');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_description');
            $table->string('item_uofm');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->integer('total_quantity');
            $table->unsignedBigInteger('subkontrak_id')->nullable();
            $table->foreign('subkontrak_id')->references('id')->on('suppliers');
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
        Schema::dropIfExists('subkontraks');
    }
};
