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
            $table->unsignedBigInteger('hpin_id')->unsigned();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedBigInteger('country_id')->unsigned();
            $table->foreign('hpin_id')->references('id')->on('hpins');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->string('item_code');
            $table->string('item_description');
            $table->string('item_longdescription')->nullable();
            $table->string('item_uofm');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->decimal('total_quantity', 12, 4);
            $table->unsignedBigInteger('currency_id')->unsigned();
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->decimal('item_amount', 12, 4);
            $table->decimal('kurs', 12, 4);
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
