<?php

namespace Weareframework\ApiProductImporter\Actions\Products;

use Illuminate\Support\Facades\Log;
use Weareframework\ApiProductImporter\Library\CheckData\FieldIsSet;
use Weareframework\ApiProductImporter\Models\ApiProduct;

class ImportApiProductAction
{
    use FieldIsSet;
    private $product;

    public static function perform($product)
    {
        return (new static($product))->handle();
    }

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
