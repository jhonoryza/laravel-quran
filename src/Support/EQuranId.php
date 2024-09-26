<?php

namespace Jhonoryza\LaravelQuran\Support;

use Illuminate\Support\Facades\Http;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;

class EQuranId implements QuranInterface
{
    public function getListSurah(): array
    {
        $json = Http::baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('api/surat')
            ->throw()
            ->json();

        if (empty($json ?? [])) {
            throw new \Exception('Quran data not found');
        }

        $qurans = [];

        foreach ($json as $item) {
            $qurans[] = [
                'external_id'     => $item['nomor'],
                'arabic'          => $item['nama'],
                'latin'           => $item['nama_latin'],
                'transliteration' => $item['nama_latin'],
                'translation'     => $item['arti'],
                'num_ayah'        => $item['jumlah_ayat'],
                'page'            => 0,
                'location'        => $item['tempat_turun'] ?? '',
            ];
        }

        return $qurans;
    }

    public function getListVerses(int $surahId): array
    {
        $json = Http::baseUrl(config('quran.base_uri'))
            ->connectTimeout(config('quran.timeout'))
            ->timeout(config('quran.timeout'))
            ->get('api/surat/' . $surahId)
            ->throw()
            ->json();
            
        if (empty($json['ayat'] ?? [])) {
            throw new \Exception('Verse data not found');
        }
        $verses = [];

        foreach ($json['ayat'] as $item) {
            $verses[] = [
                'quran_id'    => $surahId,
                'ayah'        => $item['nomor'],
                'page'        => 0,
                'juz'         => 0,
                'arabic'      => $item['ar'],
                'kitabah'     => $item['ar'],
                'latin'       => $item['tr'],
                'translation' => $item['idn'],
                'audio_url' => $this->getAudioUrl($surahId, $item['nomor']),
            ];
        }

        return $verses;

    }

    public function getAudioUrl(int $surahId, int $ayah): string
    {
        $file = sprintf('%03d%03d', $surahId, $ayah);

        return config('quran.audio_base_uri') . $file . '.mp3';
    }
}
