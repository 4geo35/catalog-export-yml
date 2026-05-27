<?php

use Illuminate\Support\Facades\Route;
use GIS\CatalogExportYml\Http\Controllers\Export\CatalogController;

Route::middleware(["web"])
    ->as("export.")
    ->group(function () {
        Route::prefix(config("catalog-export-yml.ymlPrefix"))
            ->as("catalog.")
            ->group(function () {
                $controllerClass = config("catalog-export-yml.customExportCatalogController") ?? CatalogController::class;
                Route::get("/yml", [$controllerClass, "yml"])->name("yml");
            });
    });
