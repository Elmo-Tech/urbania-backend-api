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
        Schema::create('ticket_client_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('address')->nullable()->default("");
            $table->string('city')->nullable()->default("");
            $table->string('state')->nullable()->default("");
            $table->string('postal_code')->nullable()->default("");
            $table->string('address_type_id')->nullable()->default(0);
            
            $table->unsignedBigInteger('ticket_client_id');
            //$table->foreign('ticket_client_id')->references('id')->on('ticket_clients')->onUpdate('cascade');

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
        Schema::dropIfExists('ticket_client_addresses');
    }
};
