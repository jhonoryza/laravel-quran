<?php

namespace Jhonoryza\LaravelQuran\Console\Command;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Jhonoryza\LaravelQuran\Models\Quran;
use Jhonoryza\LaravelQuran\Models\QuranVerse;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\multisearch;

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
        $this->truncateTable();

        $this->info('using driver : ' . config('quran.source'));

        $this->info('Syncing quran data...');

        $this->syncSurah($quran);
        $this->syncAyah($quran);

        $this->info('Quran data synced');
    }

    protected function truncateTable(): void
    {
        if (confirm('want to truncate table quran_verses and qurans ?', false)) {
            Schema::disableForeignKeyConstraints();
            QuranVerse::query()->truncate();
            Quran::query()->truncate();
            Schema::enableForeignKeyConstraints();

            $this->info('truncated table quran_verses and qurans');
        }
    }

    protected function getPreferences(): array
    {
        $externalIds = multisearch(
            label: 'want to select multiple surah ?',
            options: fn () => Quran::query()
                ->pluck('external_id', 'external_id')->toArray(),
            hint: '114',
            placeholder: 'empty for select all'
        );

        return [
            'external_ids' => ! empty($externalIds) ? $externalIds : [],
        ];
    }

    protected function syncSurah(QuranInterface $quran): void
    {
        if (! confirm('want to sync surah to qurans table ?', false)) {
            return;
        }
        $this->info('Syncing quran surah...');
        try {
            $qurans = $quran->getListSurah();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());

            return;
        }
        $this->info('fetched ' . count($qurans) . ' surah');
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

    protected function syncAyah(QuranInterface $quran): void
    {
        [
            'external_ids' => $externalIds
        ] = $this->getPreferences();

        $this->info('Syncing quran ayah...');

        $surahList = Quran::query()
            ->when(
                ! empty($externalIds),
                fn ($query) => $query->whereIn('external_id', $externalIds)
            )
            ->get();

        $allAyah = collect();
        $fails   = collect();

        // get all ayah from api
        if ($allAyah->isEmpty()) {
            foreach ($surahList as $surah) {
                try {
                    $ayahList = $quran->getListVerses($surah->external_id);

                } catch (\Exception $exception) {
                    $this->error($exception->getMessage());
                    $fails->push($surah->external_id);

                    continue;
                }

                foreach ($ayahList as $ayah) {
                    $allAyah->push($ayah);
                }

                $this->info('Quran ayah surah: ' . $surah->external_id . ' collected');
                sleep(1);
            }
        }

        // print all skipped ayah
        if ($fails->isNotEmpty()) {
            foreach ($fails as $fail) {
                $this->info('Quran ayah surah: ' . $fail . ' skipped');
            }
        }

        // save all ayah to database
        if ($allAyah->isNotEmpty()) {
            $allAyah->chunk(10)->each(function ($items) {
                DB::transaction(function () use ($items) {
                    QuranVerse::query()->upsert(
                        $items->toArray(),
                        ['quran_id', 'ayah'],
                        ['page', 'juz', 'arabic', 'kitabah', 'latin', 'translation']
                    );
                });
            });
        }
    }
}
