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
        Schema::create('qurans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id')->index();
            $table->string('arabic');
            $table->string('latin');
            $table->string('transliteration');
            $table->string('translation');
            $table->unsignedInteger('num_ayah');
            $table->unsignedInteger('page');
            $table->string('location');
            $table->timestamps();
        });
        Schema::create('quran_verses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quran_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('ayah');
            $table->unsignedInteger('page');
            $table->unsignedInteger('juz');
            $table->text('arabic');
            $table->text('kitabah');
            $table->text('latin');
            $table->text('translation');
            $table->string('audio_url');

            $table->unique(['quran_id', 'ayah']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quran_verses');
        Schema::dropIfExists('qurans');
    }
};
