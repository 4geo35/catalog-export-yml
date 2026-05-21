<?php

namespace GIS\CatalogExportYml\Console\Commands;

use GIS\CatalogExportYml\Facades\YmlActions;
use GIS\CatalogExportYml\Helpers\YMLDocument;
use Illuminate\Console\Command;

class CreateYmlExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:catalog-yml {--fileName=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate xml file for Yandex';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fileName = null;
        if ($this->hasOption('fileName')) {
            $fileName = $this->option('fileName');
        }
        YmlActions::generateNewFile($fileName);
    }
}
