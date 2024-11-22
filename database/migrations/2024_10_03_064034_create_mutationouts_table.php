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
        Schema::create('mutationouts', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->date('document_date');
            $table->unsignedBigInteger('bbin_id')->unsigned();
            $table->foreign('bbin_id')->references('id')->on('bbins');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('item_uofm');
            $table->unsignedBigInteger('storagesout_id')->nullable();
            $table->foreign('storagesout_id')->references('id')->on('storages');
            $table->string('storagesout_desc');
            $table->unsignedBigInteger('storagesin_id')->nullable();
            $table->foreign('storagesin_id')->references('id')->on('storages');
            $table->integer('move_quantity');
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('mutationouts');
    }
};
