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
        Schema::create('bbinadjs', function (Blueprint $table) {
            $table->id();
            $table->integer('document_id');
            $table->string('document_code');
            $table->date('document_date');
            $table->unsignedBigInteger('bbin_id')->unsigned();
            $table->foreign('bbin_id')->references('id')->on('bbins');
            $table->string('pib_number');
            $table->string('seri_number');
            $table->decimal('qty_before', 12, 4);
            $table->decimal('qty_after', 12, 4);
            $table->string('notes');
            $table->string('adjust_date');
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
        Schema::dropIfExists('bbinadj');
    }
};
