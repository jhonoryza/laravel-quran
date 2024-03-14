<?php

namespace Jhonoryza\LaravelQuran\Models;

use Illuminate\Database\Eloquent\Model;

class QuranVerse extends Model
{
    protected $fillable = [
        'quran_id',
        'ayah',
        'page',
        'juz',
        'arabic',
        'kitabah',
        'latin',
        'translation',
        'audio_url',
    ];

    protected $casts = [
        'quran_id' => 'integer',
        'ayah' => 'integer',
        'page' => 'integer',
        'juz' => 'integer',
    ];
}
