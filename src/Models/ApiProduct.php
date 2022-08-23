<?php

namespace Weareframework\ApiProductImporter\Models;

use Illuminate\Database\Eloquent\Model;
use Weareframework\ApiProductImporter\Casts\MultiSelectFieldCast;
use Weareframework\ApiProductImporter\Library\Models\FillableHelpers;

class ApiProduct extends Model
{
    use FillableHelpers;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // product key
        'type', // simple or configurable
        'is_duplicate',
        'configurable_on',
        'order',

        // product data
        'sku',
        'parent_sku',
        'model_no',
        'status',
        'name',
        'description',

        // seo
        'meta_title',
        'meta_description',
        'meta_keyword',

        // region
        'websites',

        // pricing
        'price',
        'price_gbp',
        'price_usd',
        'price_eur',
        'cost',

        // qty
        'qty',
        'notify_stock_qty',

        // promotion
        'special_price',
        'special_from_date',
        'special_to_date',

        // taxation
        'tax_class_id',

        // shipping
        'weight',
        'weight_type',
        'shipment_type',

        // product categorisation
        'gender',
        'product_type', // product_type
        'product_style', // product_style
        'product_fit', // product_fit
        'product_tech', // product_tech
        'product_line', // product_line
        'product_collection_year', // product_collection_year

        // color
        'color',
        'variant_colors',

        // sizing
        'size_uk',
        'size_fr',
        'size_intl',
        'product_lengths',

        'shoe_men_size_uk',
        'shoe_men_size_intl',
        'shoe_women_size_uk',
        'shoe_women_size_intl',

        'glove_men_size_uk',
        'glove_men_size_intl',
        'glove_women_size_uk',
        'glove_women_size_intl',

        // safety
        'ce_rating',
        'armour_pocket',
        'chest_armour',
        'back_armour',
        'elbow_knee_armour',
        'shoulder_armour',
        'hip_armour',

        // material
        'product_material',
        'product_lining',

        // key features
        'product_panels',
        'product_ventilation',
        'product_collar',
        'product_cuff',
        'product_fastening',
        'product_adjustment',
        'product_reinforcement',
        'product_shift_pad',
        'product_inner_pocket',
        'product_outer_pocket',
        'product_pocket',
        'product_detail',
        'shoe_sole',
        'product_finger',
        'product_knuckle',
        'product_palm',
        'product_other',

        // product imagery
        //        'product_image_primary',
        'product_image_primary_alt',
        //        'product_image_secondary',
        'product_image_secondary_alt',
        //        'product_gallery_images',
        'created_by_id',
        'updated_by_id',
    ];

    public $appends = [
        'edit',
        'colors'
    ];

    public $notForMapping = [];

    protected $casts = [
        'use_config_backorders' => 'boolean',
        'configurable_on' => 'json',
        'variant_colors' => MultiSelectFieldCast::class,
        'product_panels' => MultiSelectFieldCast::class,
        'product_ventilation' => MultiSelectFieldCast::class,
        'product_collar' => MultiSelectFieldCast::class,
        'product_cuff' => MultiSelectFieldCast::class,
        'product_fastening' => MultiSelectFieldCast::class,
        'product_adjustment' => MultiSelectFieldCast::class,
        'product_reinforcement' => MultiSelectFieldCast::class,
        'product_shift_pad' => MultiSelectFieldCast::class,
        'product_inner_pocket' => MultiSelectFieldCast::class,
        'product_outer_pocket' => MultiSelectFieldCast::class,
        'product_pocket' => MultiSelectFieldCast::class,
        'product_detail' => MultiSelectFieldCast::class,
        'shoe_sole' => MultiSelectFieldCast::class,
        'product_finger' => MultiSelectFieldCast::class,
        'product_knuckle' => MultiSelectFieldCast::class,
        'product_palm' => MultiSelectFieldCast::class,
        'product_other' => MultiSelectFieldCast::class,
    ];

    public static $rules = [];

    public static function booted()
    {
        static::creating(function ($product) {
            if ($product['type'] === 'configurable') {
                $product->configurable_on = config('statamic.framework-importer.configurable');
            }
        });

        static::updating(function ($product) {
            if ($product['type'] === 'configurable') {
                $product->configurable_on = config('statamic.framework-importer.configurable');
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'sku', 'parent_sku')->orderBy('order');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_sku', 'sku')->orderBy('order');
    }

    public function getEditAttribute()
    {
        return false;
    }

    public function getColorsAttribute()
    {
        return explode('_', $this->color);
    }

    public function getConnectionName()
    {
        return config('statamic.api-framework-importer.connection', 'api_product_importer');
    }
}
