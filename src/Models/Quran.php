<?php

namespace Jhonoryza\LaravelQuran\Models;

use Illuminate\Database\Eloquent\Model;

class Quran extends Model
{
    protected $fillable = [
        'external_id',
        'arabic',
        'latin',
        'transliteration',
        'translation',
        'num_ayah',
        'page',
        'location',
    ];

    protected $casts = [
        'external_id' => 'integer',
        'num_ayah' => 'integer',
        'page' => 'integer',
    ];
}
