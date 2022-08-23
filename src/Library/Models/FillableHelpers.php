<?php

namespace Weareframework\ApiProductImporter\Library\Models;

use Illuminate\Support\Arr;

trait FillableHelpers
{
    public function getFillableWithout()
    {
        if (! property_exists($this, 'notForMapping')) {
            return $this->fillable;
        }

        $notForMapping = $this->notForMapping;
        $fields = Arr::where($this->fillable, function ($value, $key) use($notForMapping) {
            return !in_array($value, $notForMapping);
        });

        return array_values($fields);
    }

    public function getNotForMapping()
    {
        if (! property_exists($this, 'notForMapping')) {
            return $this->fillable;
        }

        return $this->notForMapping;
    }
}
