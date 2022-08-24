<?php

namespace Weareframework\ApiProductImporter\Jobs\Pull;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;
use Statamic\Facades\Collection as CollectionFacade;
use Statamic\Facades\Entry;
use Statamic\Facades\File;
use Statamic\Facades\Stache;
use Statamic\Support\Arr;
use Weareframework\ApiProductImporter\Library\CheckData\FieldIsSet;
use Weareframework\ApiProductImporter\Models\ApiProduct;
use Weareframework\ProductImporter\Jobs\Imports\ImportConfigurableFwkProductToStatamic;
use Weareframework\ProductImporter\Models\FwkProduct;

class ImportApiProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use FieldIsSet;
    private $product;

    public function __construct(
        $product
    ) {
        $this->product = $product;
    }

    public function handle()
    {
        try {
//            $item = $this->cleanData($item);
            $sku = ($this->fieldIsSet($this->product, 'sku')) ? $this->product['sku'] : null;
            unset($this->product['sku']);
            unset($this->product['id']);
            unset($this->product['0']);
            if (is_null($sku)) {
                return false;
            }

            $fwkProduct = ApiProduct::updateOrCreate([
                'sku' => $sku
            ], $this->product);

            return true;
        }catch(\Exception $e) {
            Log::info('Their was an issue for row SKU: ' . $sku);
            Log::info($e->getMessage());
            Log::info($e->getLine());
            Log::info(json_encode($this->product));
            Log::info($e->getTraceAsString());
            Log::info('-------');
        }
        return false;
    }
}
