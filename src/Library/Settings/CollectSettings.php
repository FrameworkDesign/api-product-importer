<?php

namespace Weareframework\ApiProductImporter\Library\Settings;


use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Site;
use Weareframework\ApiProductImporter\Library\Files\File;

class CollectSettings
{
    protected $file;
    protected $blueprint;
    protected $fields;
    protected $values;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function handle()
    {
        $this->getSettings();

        return $this;
    }

    public function getSettings()
    {
        $this->setLocale();

        $this->blueprint = Blueprint::makeFromSections(config('statamic.api-product-importer.settings-blueprint'));
        $fields = $this->blueprint->fields();

        $this->values = $this->file->read(false);

        $fields = $fields->addValues($this->values);

        $this->fields = $fields->preProcess();
    }

    public function updateValues($values)
    {
        $this->file->write($values);
    }

    private function setLocale()
    {
        $this->file->setLocale(
            session('statamic.cp.selected-site') ?
                Site::get(session('statamic.cp.selected-site'))->locale() :
                Site::current()->locale());
    }

    function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->$name();
        } else if(property_exists($this,$name)){
            // Getter/Setter not defined so return property if it exists
            return $this->$name;
        }
        return null;
    }
}
