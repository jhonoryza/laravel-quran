<?php

namespace Jhonoryza\LaravelQuran\Support\Concerns;

interface QuranInterface
{
    public function getListSurah(): array;

    public function getListVerses(int $surahId): array;
}
