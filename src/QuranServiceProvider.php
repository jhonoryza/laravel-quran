<?php

namespace Jhonoryza\LaravelQuran;

use Illuminate\Support\ServiceProvider;
use Jhonoryza\LaravelQuran\Console\Command\QuranSyncCommand;
use Jhonoryza\LaravelQuran\Support\Concerns\QuranInterface;
use Jhonoryza\LaravelQuran\Support\QuranKemenag;

class QuranServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->commands([
            QuranSyncCommand::class,
        ]);

        if ($this->app->runningInConsole()) {

            $this->mergeConfigFrom(__DIR__.'/../config/quran.php', 'quran');

            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

            if (config('quran.source') == 'kemenag') {
                $this->app->bind(QuranInterface::class, QuranKemenag::class);
            } else {
                throw new \Exception('Quran source kemenag only supported');
            }
        }

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/quran.php' => config_path('quran.php'),
        ], 'quran-config');
    }
}
