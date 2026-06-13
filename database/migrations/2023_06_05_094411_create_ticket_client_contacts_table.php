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
        Schema::create('ticket_client_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->nullable()->default("");;
            $table->string('email')->nullable()->default("");

            $table->unsignedBigInteger('ticket_client_id')->default(0);
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
        Schema::dropIfExists('ticket_client_phones');
    }
};
