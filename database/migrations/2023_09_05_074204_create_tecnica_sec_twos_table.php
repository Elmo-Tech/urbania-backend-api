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
        Schema::create('tecnica_sec_twos', function (Blueprint $table) {
            $table->id();
            $table->string('note_sec_one')->nullable()->default("");
            $table->string('tipologia')->nullable()->default("");
            $table->string('data_apozione')->nullable()->default("");
            $table->string('data_approvazione')->nullable()->default("");
            $table->string('pubblicazione_burl')->nullable()->default("");
            $table->string('note')->nullable()->default("");

            $table->unsignedBigInteger('tecnica_main_data_id')->nullable();
            $table->foreign('tecnica_main_data_id')->references('id')->on('tecnica_main_data')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('tecnica_sec_twos');
    }
};
