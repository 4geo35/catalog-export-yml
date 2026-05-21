<?php

namespace GIS\CatalogExportYml\Facades;

use GIS\CatalogExportYml\Helpers\YmlActionsManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void generateNewFile(string $fileName = null)
 *
 * @see YmlActionsManager
 */
class YmlActions extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "catalog-export-yml-actions";
    }
}
