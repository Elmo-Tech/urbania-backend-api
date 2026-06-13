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
        Schema::create('calenders', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable()->default("");
            $table->text('description')->nullable()->default("");
            $table->string('color')->nullable()->default("");

            /*$table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade');

            $table->unsignedBigInteger('ticket_clients_id')->nullable();
            $table->foreign('ticket_clients_id')->references('id')->on('ticket_clients')->onUpdate('cascade');*/


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
        Schema::dropIfExists('calenders');
    }
};
