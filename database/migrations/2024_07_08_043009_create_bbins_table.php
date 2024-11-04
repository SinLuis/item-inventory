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
        Schema::create('bbins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->foreign('document_id')->references('id')->on('documents');
            $table->string('document_number');
            $table->date('document_date');
            $table->string('seri_number');
            $table->string('reff_number');
            $table->date('reff_date');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_description');
            $table->string('item_longdescription');
            $table->string('item_uofm');
            $table->integer('total_container');
            $table->integer('total_quantity');
            $table->unsignedBigInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->integer('item_amount');
            $table->unsignedBigInteger('storages_id');
            $table->foreign('storages_id')->references('id')->on('storages');
            $table->unsignedBigInteger('subkontrak_id')->nullable();
            $table->foreign('subkontrak_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->unsignedBigInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');
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
        Schema::dropIfExists('bbins');
    }
};
