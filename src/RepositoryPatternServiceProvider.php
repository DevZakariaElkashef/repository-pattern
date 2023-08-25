<?php

namespace ZakariaElkashef\RepositoryPattern;

use Illuminate\Support\ServiceProvider;
use ZakariaElkashef\RepositoryPattern\Commands\MakeRepository;

class RepositoryPatternServiceProvider extends ServiceProvider
{
    
    public function register()
    {
        
    }
    
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeRepository::class,
            ]);
        }
    }
}