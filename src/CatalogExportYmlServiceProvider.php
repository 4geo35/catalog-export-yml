<?php

namespace GIS\CatalogExportYml;

use GIS\CatalogExportYml\Console\Commands\CreateYmlExport;
use Illuminate\Support\ServiceProvider;

class CatalogExportYmlServiceProvider extends ServiceProvider
{
    public function register(): void
    {}

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateYmlExport::class,
            ]);
        }
    }
}
