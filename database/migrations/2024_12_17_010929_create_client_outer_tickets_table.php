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
        Schema::create('client_outer_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('cf')->nullable();
            $table->string('p_iva')->nullable();
            $table->string('ragione_sociale')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('number')->unique();
            $table->string('delegated_firstname')->nullable();
            $table->string('delegated_lastname')->nullable();
            $table->string('delegated_phone')->nullable();
            $table->text('message')->nullable();
            $table->timestamp('date');
            $table->string('anno');
            $table->string('status')->default(0);
            $table->date('notify_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('esito')->nullable();
            $table->text('note')->nullable();
            $table->boolean('accept_status')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->foreign('service_id')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('istanza_parameter_id')->nullable();
            $table->foreign('istanza_parameter_id')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade');
            $table->unsignedBigInteger('delegated_role_id')->nullable();
            $table->foreign('ticket_client_id')->references('id')->on('ticket_clients')->onUpdate('cascade');
            $table->unsignedBigInteger('ticket_client_id')->nullable();
            $table->foreign('contract_id')->references('id')->on('contracts')->onUpdate('cascade');
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->foreign('contract_two_id')->references('id')->on('contracts')->onUpdate('cascade');
            $table->unsignedBigInteger('contract_two_id')->nullable();
            $table->foreign('connect_type_id')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('connect_type_id')->nullable();
            $table->foreign('segnalazione')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('segnalazione')->nullable();
            $table->foreign('urgenza')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('urgenza')->nullable();
            $table->foreign('delegated_role_id')->references('id')->on('parameter_values')->onUpdate('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_outer_tickets');
    }
};
