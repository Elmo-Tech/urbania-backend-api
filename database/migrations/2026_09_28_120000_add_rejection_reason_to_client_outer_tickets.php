<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_outer_tickets', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable();
        });

        // SQLite already stores boolean columns as integers and supports all three values.
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            Schema::table('client_outer_tickets', function (Blueprint $table) {
                $table->unsignedTinyInteger('accept_status')->nullable()->default(0)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('client_outer_tickets', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

        // Keep the numeric status column so rollback never turns rejected tickets into accepted ones.
    }
};
