<?php

namespace Jhonoryza\LaravelQuran\Support;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;

class QuranKemenag implements QuranInterface
{
    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function getListSurah(): array
    {
        $json = Http::baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('quran-surah')
            ->throw()
            ->json();

        if (empty($json['data'] ?? [])) {
            throw new \Exception('Quran data not found');
        }

        $qurans = [];

        foreach ($json['data'] as $item) {
            $qurans[] = [
                'external_id' => $item['id'],
                'arabic' => $item['arabic'],
                'latin' => $item['latin'],
                'transliteration' => $item['transliteration'],
                'translation' => $item['translation'],
                'num_ayah' => $item['num_ayah'],
                'page' => $item['page'],
                'location' => $item['location'] ?? '',
            ];
        }

        return $qurans;
    }

    /**
     * @throws RequestException
     * @throws \Exception
     */
    public function getListVerses(int $surahId): array
    {
        $json = Http::baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('quran-ayah', [
                'start' => 0,
                'limit' => 300, // largest 286 in albaqarah
                'surah' => $surahId,
            ])
            ->throw()
            ->json();

        if (empty($json['data'] ?? [])) {
            throw new \Exception('Verse data not found');
        }

        $verses = [];

        foreach ($json['data'] as $item) {
            $verses[] = [
                'quran_id' => $surahId,
                'ayah' => $item['ayah'],
                'page' => $item['page'],
                'juz' => $item['juz'],
                'arabic' => $item['arabic'],
                'kitabah' => $item['kitabah'],
                'latin' => $item['latin'],
                'translation' => $item['translation'],
            ];
        }

        return $verses;
    }
}
