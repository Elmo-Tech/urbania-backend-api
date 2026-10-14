<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['client_outer_tickets', 'tickets', 'reservations', 'events'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->tinyInteger('privacy_accept')->default(0);
            });
        }
    }

    public function down(): void
    {
        foreach (['client_outer_tickets', 'tickets', 'reservations', 'events'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('privacy_accept');
            });
        }
    }
};
