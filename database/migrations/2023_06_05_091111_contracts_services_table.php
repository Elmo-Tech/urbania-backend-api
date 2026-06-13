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
        Schema::create('contracts_services', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('payment_id')->nullable()->default(0);
            $table->text('account_number')->nullable()->default("");
            $table->integer('carico_id')->nullable()->default(0);
            $table->string('note')->nullable()->default("");

            $table->unsignedBigInteger('service_id')->default(0);
            /*$table->foreign('service_id')->references('id')->on('parameter_values')->onUpdate('cascade');*/

            $table->unsignedBigInteger('contract_id');
            $table->foreign('contract_id')->references('id')->on('contracts')->onUpdate('cascade');

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
        Schema::dropIfExists('contacts_services');
    }
};
