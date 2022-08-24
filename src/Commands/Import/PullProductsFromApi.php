<?php

namespace Weareframework\ApiProductImporter\Commands\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Statamic\Console\RunsInPlease;
use Weareframework\ApiProductImporter\Jobs\Import\ImportApiProduct;
use Weareframework\ApiProductImporter\Library\Files\File;
use Weareframework\ApiProductImporter\Library\Settings\CollectSettings;


class PullProductsFromApi extends Command
{
    use RunsInPlease;

    protected $signature = 'api-product-importer:products:import';

    protected $description = 'Import via the API url the products into this system';

    protected $file;


    public function handle(File $file)
    {
        $this->file = $file;
        $this->info('Grabbing all products');
        $settings = (new CollectSettings($this->file))->handle();
        $response = Http::get($settings->values['api_product_importer_route']);

        if ($response->successful()) {
            // import the punks
            $importData = $response->json();
            if($importData['count'] > 0) {
                foreach($importData['product'] as $product) {
                    ImportApiProduct::dispatch($product);
                }
            }
        }

        $response->onError(function() {
            // deal with error
            $this->info('error!');
        });

        $this->info('Done!');
        return;
    }
}
