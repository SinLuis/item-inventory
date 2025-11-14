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
            $table->unsignedBigInteger('bbout_id')->unsigned()->nullable();
            $table->foreign('bbout_id')->references('id')->on('bbouts');
            $table->unsignedBigInteger('wips_id')->nullable();
            $table->unsignedBigInteger('fg_id');
            $table->foreign('fg_id')->references('id')->on('items');
            $table->string('fg_code');
            $table->string('fg_description');
            $table->string('fg_uofm');
            $table->double('fg_quantity');
            $table->double('fg_quantity_remaining');
            $table->unsignedBigInteger('wip_id')->nullable();
            $table->foreign('wip_id')->references('id')->on('items');
            $table->string('wip_code')->nullable();
            $table->string('wip_description')->nullable();
            $table->string('wip_uofm')->nullable();
            $table->double('wip_quantity')->nullable();
            $table->double('wip_quantity_remaining')->nullable();
            $table->string('pib_number');
            $table->string('seri_number');
            $table->double('sub_quantity')->nullable();
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
