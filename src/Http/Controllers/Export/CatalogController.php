<?php

namespace GIS\CatalogExportYml\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use GIS\CatalogExportYml\Facades\YmlActions;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class CatalogController extends Controller
{
    public function yml(): Response|ResponseFactory
    {
        $xml = YmlActions::getXMLContent();
        if (!$xml) { abort(404); }
        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
