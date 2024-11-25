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
        Schema::create('wastes', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->date('document_date');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->unsignedBigInteger('bbout_id');
            $table->foreign('bbout_id')->references('id')->on('items');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('item_uofm');
            $table->decimal('total_quantity', 12, 4);
            $table->decimal('item_amount', 12, 4);
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
        Schema::dropIfExists('wastes');
    }
};
