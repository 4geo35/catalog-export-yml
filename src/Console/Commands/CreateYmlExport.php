<?php

namespace GIS\CatalogExportYml\Console\Commands;

use Illuminate\Console\Command;

class CreateYmlExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:catalog-yml';

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
        $this->info("Hello there");
    }
}
