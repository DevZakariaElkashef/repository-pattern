<?php

namespace ZakariaElkashef\RepositoryPattern;

use Illuminate\Support\ServiceProvider;


class RepositoryPatternServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ZakariaElkashef\RepositoryPattern\Commands\MakeRepository::class,
            ]);
        }
    }
    
    public function register()
    {
        
    }
    
}