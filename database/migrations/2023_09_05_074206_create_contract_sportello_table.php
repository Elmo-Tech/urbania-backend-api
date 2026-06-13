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
        Schema::create('contract_sportello', function (Blueprint $table) {
            $table->id();
            $table->date('data_ins')->nullable();
            $table->string('note')->nullable()->default("");
            $table->string('n_one')->nullable()->default("");

            $table->unsignedBigInteger('lavorazione_main_data_id')->nullable();
            $table->foreign('lavorazione_main_data_id')->references('id')->on('lavorazione_main_data')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('worker_id')->nullable();
            $table->foreign('worker_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('tipologia_sportello')->nullable();
            $table->foreign('tipologia_sportello')->references('id')->on('parameter_values')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('contract_sportello');
    }
};
