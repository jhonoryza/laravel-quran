<?php

namespace Jhonoryza\LaravelQuran\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Jhonoryza\LaravelQuran\Models\Quran;
use Jhonoryza\LaravelQuran\Models\QuranVerse;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;

use function Laravel\Prompts\suggest;

class QuranSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quran:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync quran data';

    /**
     * Execute the console command.
     */
    public function handle(QuranInterface $quran): void
    {
        $this->info('Syncing quran data...');

        [
            'surah_transliteration' => $surahTransliteration
        ] = $this->getPreferences();
        $this->syncSurah($quran);
        $this->syncAyah($quran, $surahTransliteration);

        $this->info('Quran data synced');
    }

    protected function getPreferences(): array
    {
        $listSurah = Quran::query()->pluck('external_id', 'external_id');
        if ($listSurah->isNotEmpty()) {
            $surahTransliteration = suggest(
                label: 'want to select Surah ?',
                options: $listSurah,
            );
        }

        return [
            'surah_transliteration' => $surahTransliteration ?? '',
        ];
    }

    protected function syncSurah(QuranInterface $quran): void
    {
        $this->info('Syncing quran surah...');
        try {
            $qurans = $quran->getListSurah();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return;
        }
        $this->info('fetched '.count($qurans).' surah');
        DB::transaction(function () use ($qurans) {
            Quran::query()->upsert($qurans,
                ['external_id'],
                [
                    'arabic',
                    'latin',
                    'transliteration',
                    'translation',
                    'num_ayah',
                    'page',
                    'location',
                ]
            );
        });
        $this->info('Quran surah synced');
    }

    protected function syncAyah(QuranInterface $quran, string $surahTransliteration): void
    {
        $this->info('Syncing quran ayah...');
        $surahList = Quran::query()
            ->when(
                ! empty($surahTransliteration),
                fn ($query) => $query->where('transliteration', $surahTransliteration)
            )
            ->get();
        foreach ($surahList as $surah) {

            try {
                $ayahList = $quran->getListVerses($surah->external_id);

            } catch (\Exception $exception) {
                $this->error($exception->getMessage());

                continue;
            }

            DB::transaction(function () use ($ayahList) {
                QuranVerse::query()->upsert(
                    $ayahList,
                    ['quran_id', 'ayah'],
                    ['page', 'juz', 'arabic', 'kitabah', 'latin', 'translation']
                );
            });

            $this->info('Quran ayah surah: '.$surah->external_id.' synced');
        }
    }
}
