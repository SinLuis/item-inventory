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
        Schema::create('hpins', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->date('document_date');
            $table->unsignedBigInteger('bbout_id')->unsigned();
            $table->foreign('bbout_id')->references('id')->on('bbouts');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('item_uofm');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->string('produce_quantity');
            $table->integer('quantity_remaining');
            $table->string('sub_quantity')->nullable();
            $table->unsignedBigInteger('storages_id');
            $table->foreign('storages_id')->references('id')->on('storages');
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
        Schema::dropIfExists('hpins');
    }
};
