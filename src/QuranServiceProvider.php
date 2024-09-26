<?php

namespace Jhonoryza\LaravelQuran;

use Illuminate\Support\ServiceProvider;
use Jhonoryza\LaravelQuran\Console\Command\QuranSyncCommand;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;
use Jhonoryza\LaravelQuran\Support\QuranKemenag;
use Jhonoryza\LaravelQuran\Support\EQuranId;
use Jhonoryza\LaravelQuran\Support\QuranKemenagOfficial;

class QuranServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            QuranSyncCommand::class,
        ]);

        if ($this->app->runningInConsole()) {

            $this->mergeConfigFrom(__DIR__ . '/../config/quran.php', 'quran');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            if (config('quran.source') == 'kemenag') {
                $this->app->bind(QuranInterface::class, QuranKemenag::class);
            } elseif (config('quran.source') == 'kemenag_official') {
                $this->app->bind(QuranInterface::class, QuranKemenagOfficial::class);
            } elseif (config('quran.source') == 'equran.id') {
                $this->app->bind(QuranInterface::class, EQuranId::class);
            } else {
                throw new \Exception('Tanzil not implemented yet');
            }
        }

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/quran.php' => config_path('quran.php'),
        ], 'quran-config');
    }
}
