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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->date('end_date')->nullable();
            $table->date('notify_date')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);
            $table->integer('worker_id')->nullable()->default(0);
            $table->integer('closer_id')->nullable()->default(0);
            $table->integer('service_id')->nullable()->default(0);
            $table->integer('esito')->nullable()->default(0);
            $table->string('note')->nullable()->default("");
            $table->integer('connect_type_id')->nullable()->default(0);
            $table->text('description')->nullable()->default("");
            $table->date('status_date')->nullable();
            //$table->integer('company_id');

            $table->unsignedBigInteger('client_id');
            //$table->foreign('client_id')->references('id')->on('clients')->onUpdate('cascade');

            $table->unsignedBigInteger('ticket_client_id');
            //$table->foreign('ticket_client_id')->references('id')->on('ticket_clients')->onUpdate('cascade');

            $table->unsignedBigInteger('contract_id');
            //$table->foreign('contract_id')->references('id')->on('contracts')->onUpdate('cascade');

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
        Schema::dropIfExists('tickets');
    }
};
