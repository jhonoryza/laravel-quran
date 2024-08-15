<?php

namespace Jhonoryza\LaravelQuran\Support;

use Illuminate\Support\Facades\Http;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;

class QuranKemenagOfficial implements QuranInterface
{
    public function getListSurah(): array
    {
        $json = Http::withHeaders([
            'Authorization' => config('quran.token'),
            'user'          => config('quran.username'),
        ])
            ->baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('surah/local/1/114')
            ->throw()
            ->json();

        if (empty($json['data'] ?? [])) {
            throw new \Exception('Quran data not found');
        }

        $qurans = [];

        foreach ($json['data'] as $item) {
            $qurans[] = [
                'external_id'     => $item['id'],
                'arabic'          => $item['arabic'],
                'latin'           => $item['nama'],
                'transliteration' => $item['nama'],
                'translation'     => $item['arti'],
                'num_ayah'        => $item['jmlAyat'],
                'page'            => 0,
                'location'        => $item['kategori'] ?? '',
            ];
        }

        return $qurans;
    }

    public function getListVerses(int $surahId): array
    {
        $json = Http::withHeaders([
            'Authorization' => config('quran.token'),
            'user'          => config('quran.username'),
        ])
            ->baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('ayat/local/' . $surahId)
            ->throw()
            ->json();

        if (empty($json['data'] ?? [])) {
            throw new \Exception('Verse data not found');
        }

        $verses = [];

        foreach ($json['data'] as $item) {
            $verses[] = [
                'quran_id'    => $surahId,
                'ayah'        => $item['ayat'],
                'page'        => $item['halaman'],
                'juz'         => $item['juz'],
                'arabic'      => $item['teks_msi_usmani'],
                'kitabah'     => $item['teks_gundul'],
                'latin'       => $item['teks'],
                'translation' => $item['terjemah'],
                'audio_url'   => $this->getAudioUrl($surahId, $item['ayat']),
            ];
        }

        return $verses;

    }

    public function getAudioUrl(int $surahId, int $ayah): string
    {
        $file = sprintf('%03d%03d', $surahId, $ayah);

        return config('quran.audio_base_uri') . $file . '.m4a';
    }
}
