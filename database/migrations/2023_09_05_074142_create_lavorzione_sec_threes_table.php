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
        Schema::create('lavorzione_sec_threes', function (Blueprint $table) {
            $table->id();
            $table->string('imposta')->nullable()->default("");
            $table->string('note')->nullable()->default("");
            $table->string('n_avvisi')->nullable()->default("");
            $table->string('importa')->nullable()->default("");
            $table->string('anno_ennissone')->nullable()->default("");
            $table->string('anno_accertamento')->nullable()->default("");

            $table->unsignedBigInteger('lavorazione_main_data_id')->nullable();
            $table->foreign('lavorazione_main_data_id')->references('id')->on('lavorazione_main_data')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade');
            $table->softDeletes();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lavorzione_sec_threes');
    }
};
