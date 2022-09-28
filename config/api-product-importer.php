<?php

return [
    'configurable' => [
        'color', 'size',
    ],
    'file' => 'api_product_importer',
    /*
     * The database connection redirect should use to store errors
     * by default this is the included 'redirect' connection
     * that uses an sqlite database in storage.
     */
    'connection' => 'api_product_importer',

    /**
     * The name of the assets container where images should be downloaded.
     */
    'assets_container' => env('API_PRODUCT_IMPORTER_ASSETS_CONTAINER', 'assets'),
    'resize_pixels' => env('API_IMPORTER_RESIZE_PIXELS', 1000),
    'image_quality_save' => env('API_IMPORTER_EXTENSION_QAULITY_SAVE', 50),
    'extension_save' => env('API_IMPORTER_EXTENSION_SAVE_AS', 'none'),

    /*
     * Enable downloading of featured image. The default is 'true'.
     */
    'download_images' => true,

    /*
     * Whether to skip download of an image if it already exist. The default is 'false'.
     */
    'skip_existing_images' => false,

    /*
     * Enable image overwriting. When set to false, a new image are created with a timestamp suffix, if the image already exists. The default is 'false'.
     */
    'overwrite_images' => false,

    /*
     * Customize where on filesystem the api products are being stored
     * Useful when using a non-conventional setup where data should
     * not be inside the usual content/api-products folder
     */
    'api_product_importer_store' => base_path('content/api-products'),

    'settings-blueprint' => [
        'name' => [
            'display' => 'General',
            'fields' => [
                'api_product_importer_section_title' => [
                    'type' => 'section',
                    'display' => 'API Route settings',
                    'instructions' => 'Information as to where the API data is coming from'
                ],
                'api_product_importer_products_all_route' => [
                    'type' => 'text',
                    'character_limit' => '500',
                    'display' => 'API Route - All Products',
                    'instructions' => 'The URL of the API route',
                    'validate' => 'required|string|url',
                ],
                'api_product_importer_products_all_method' => [
                    'type' => 'select',
                    'display' => 'Route Method Type for all',
                    'instructions' => 'What method type?',
                    'validate' => 'required|string',
                    'options' => [
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PATCH' => 'PATCH',
                        'PUT' => 'PUT',
                    ]
                ],
                'api_product_importer_products_single_route' => [
                    'type' => 'text',
                    'character_limit' => '500',
                    'display' => 'API Route - Single Product',
                    'instructions' => 'The URL of the API route for a single product. You can mimic variables with curly braces e.g. /single/{id}',
                    'validate' => 'required|string',
                ],
                'api_product_importer_products_single_method' => [
                    'type' => 'select',
                    'display' => 'Route Method Type for single',
                    'instructions' => 'What method type?',
                    'validate' => 'required|string',
                    'options' => [
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PATCH' => 'PATCH',
                        'PUT' => 'PUT',
                    ]
                ],
            ],
        ]
    ]
];
