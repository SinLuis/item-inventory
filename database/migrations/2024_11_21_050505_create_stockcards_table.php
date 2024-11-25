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
        Schema::create('stockcards', function (Blueprint $table) {
            $table->id();
            $table->string('pib_number');
            $table->string('seri_number');
            $table->date('document_date');
            $table->integer('transaction_id'); //id BBIN / BBOUT / HPIN / DLL
            $table->string('transaction_description'); //Bahan Baku Masuk / Bahan Baku Keluar
            $table->integer('transaction_type'); // 1 : Masuk, 2: Keluar
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->decimal('total_quantity', 12, 4);
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
        Schema::dropIfExists('stockcards');
    }
};
