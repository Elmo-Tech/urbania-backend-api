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
        Schema::create('lavorazione_main_data', function (Blueprint $table) {
            $table->id();
            $table->string('note_sec_one')->nullable()->default("");
            $table->string('note_sec_two')->nullable()->default("");
            $table->string('imposta')->nullable()->default("");
            $table->string('note')->nullable()->default("");
            $table->string('n_avvisi')->nullable()->default("");
            $table->string('importa')->nullable()->default("");
            $table->string('anno_ennissone')->nullable()->default("");
            $table->string('anno_accertamento')->nullable()->default("");

            $table->unsignedBigInteger('contract_service_id')->nullable();
            $table->foreign('contract_service_id')->references('id')->on('contracts_services')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::dropIfExists('lavorazione_main_data');
    }
};
