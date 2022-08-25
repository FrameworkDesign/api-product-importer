<?php

namespace Weareframework\ApiProductImporter\Fieldtypes;

use Illuminate\Support\Facades\Log;
use Statamic\Exceptions\AssetContainerNotFoundException;
use Statamic\Facades\Asset;
use Statamic\Facades\AssetContainer;
use Statamic\Facades\GraphQL;
use Statamic\Fields\Fields as BlueprintFields;
use Statamic\Fields\Fieldtype;



class ApiProductDataImporter extends Fieldtype
{
    protected $categories = ['special'];
    protected $defaultValue = null;
    protected $selectableInForms = true;

    protected function configFieldItems(): array
    {
        return [];
    }

    /*
     * how to load your data in
     */
    public function preProcess($values)
    {
        if (is_null($values) || empty($values)) {
            return null;
        }

        return $values;
    }

    public function process($data)
    {
        return $data;
    }

    protected function fields()
    {
        return new BlueprintFields([]);
    }

    public function preload()
    {
        return [
            'default' => $this->defaultValue(),
        ];
    }


    public function preProcessIndex($data)
    {
        return [];
    }


}
