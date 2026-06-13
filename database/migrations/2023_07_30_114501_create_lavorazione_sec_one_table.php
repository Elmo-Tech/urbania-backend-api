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
        Schema::create('lavorazione_sec_one', function (Blueprint $table) {

            $table->id();
            $table->text('description')->nullable()->default("");
            $table->string('years')->nullable()->default("");
            $table->string('years_values')->nullable()->default("");
            $table->integer('lavorazione_sec_one_parameter_id')->nullable()->default(0);

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
        Schema::dropIfExists('lavorazione_sec_one');
    }
};
