<?php

namespace ZakariaElkashef\RepositoryPattern;

use Illuminate\Support\ServiceProvider;
use ZakariaElkashef\RepositoryPattern\Console\Commands\MakeRepository;

class RepositoryPatternServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepository::class,
            ]);
        }
    }

    public function register()
    {
        // Register any additional bindings or services here
    }
}