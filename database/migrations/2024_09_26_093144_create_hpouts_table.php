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
        Schema::create('hpouts', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->date('document_date');
            $table->string('sj_number');
            $table->date('sj_date');
            $table->unsignedBigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->unsignedBigInteger('hpin')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedBigInteger('country_id')->unsigned();
            $table->foreign('hpin')->references('id')->on('hpins');
            $table->string('item_id');
            $table->string('item_description');
            $table->string('item_longdescription');
            $table->string('item_uofm');
            $table->string('no_pib');
            $table->string('seri_number');
            $table->integer('total_quantity');
            $table->unsignedBigInteger('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->integer('item_amount');
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
        Schema::dropIfExists('hpouts');
    }
};
