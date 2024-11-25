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
        Schema::create('bbouts', function (Blueprint $table) {
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
            $table->string('subkontrak_name')->nullable();
            $table->decimal('use_quantity', 12, 4)->nullable();
            $table->decimal('quantity_remaining', 12, 4);
            $table->decimal('sub_quantity', 12, 4)->nullable() ;
            $table->unsignedBigInteger('subkontrak_id')->nullable();
            $table->foreign('subkontrak_id')->references('id')->on('suppliers');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('fg_id');
            $table->foreign('fg_id')->references('id')->on('items');
            $table->string('fg_description');
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
        Schema::dropIfExists('bbouts');
    }
};
