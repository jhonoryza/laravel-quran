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
        Schema::table('qurans', function (Blueprint $table) {
            $table->dropIndex(['external_id']);
            $table->unique(['external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qurans', function (Blueprint $table) {
            $table->dropUnique(['external_id']);
            $table->index(['external_id']);
        });
    }
};