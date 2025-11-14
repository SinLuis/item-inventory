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
        Schema::create('wips', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->date('document_date');
            $table->unsignedBigInteger('hp_id')->unsigned();
            $table->foreign('hp_id')->references('id')->on('hpins');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->unsignedBigInteger('wip_id');
            $table->foreign('wip_id')->references('id')->on('items');
            $table->string('wip_code');
            $table->string('wip_description');
            $table->string('wip_uofm');
            $table->double('wip_quantity');
            $table->double('wip_quantity_remaining');
            $table->unsignedBigInteger('fg_id');
            $table->foreign('fg_id')->references('id')->on('items');
            $table->string('fg_code');
            $table->string('fg_description');
            $table->unsignedBigInteger('fg_uofm_id');
            $table->string('fg_uofm_description');
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
        Schema::dropIfExists('wips');
    }
};
