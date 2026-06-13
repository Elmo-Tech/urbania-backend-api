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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('cf')->nullable();
            $table->string('p_iva')->nullable();
            $table->string('ragione_sociale')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('number')->unique();
            $table->string('delegated_firstname')->nullable();
            $table->string('delegated_lastname')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('date');
            $table->integer('duration');
            $table->string('status')->default(0);
            $table->unsignedBigInteger('parameter_id')->nullable();
            $table->foreign('parameter_id')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade');
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
        Schema::dropIfExists('reservations');
    }
};
