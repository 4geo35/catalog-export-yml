<?php

namespace GIS\CatalogExportYml;

use GIS\CatalogExportYml\Console\Commands\CreateYmlExport;
use GIS\CatalogExportYml\Helpers\YmlActionsManager;
use Illuminate\Support\ServiceProvider;

class CatalogExportYmlServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/catalog-export-yml.php', 'catalog-export-yml');
        $this->initFacades();
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateYmlExport::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__ . '/routes/export.php');
    }

    protected function initFacades(): void
    {
        $this->app->singleton("catalog-export-yml-actions", function () {
            $managerClass = config('catalog-export-yml.customYmlActionsManager') ?? YmlActionsManager::class;
            return new $managerClass();
        });
    }
}
