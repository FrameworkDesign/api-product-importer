<?php

namespace Weareframework\ApiProductImporter;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Statamic\Statamic;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Weareframework\ApiProductImporter\Commands\Import\PullProductsFromApi;
use Weareframework\ApiProductImporter\Fieldtypes\ApiProductDataImporter;

class ServiceProvider extends AddonServiceProvider
{
    protected $scripts = [
        __DIR__.'/../resources/dist/js/cp.js',
    ];

    protected $routes = [
        'cp'  => __DIR__.'/../routes/cp.php',
    ];

    protected $fieldtypes = [
        ApiProductDataImporter::class
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/api-product-importer.php', 'statamic.api-product-importer');

        $this->publishes([
            __DIR__.'/../config/api-product-importer.php' => config_path('statamic/api-product-importer.php'),
        ], 'api-product-importer');
    }

    public function bootAddon()
    {
        Queue::after(function (JobProcessed $event) {
            // if memory_get_usage = 12256 then (12256 / 1000) = 12.256, i.e. ~12 bytes per job
            Log::info('[Memory] After running job  => '.memory_get_usage());
        });

        $this->commands([
            PullProductsFromApi::class,
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'api-product-importer');

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateApiProductsTable')) {
                $this->publishes([
                    __DIR__ . '/../database/migrations/create_api_products_table.php.stub' => database_path('migrations/2022_01_25_163625_create_api_products_table.php'),
                ], 'migrations');
            }
        }

        $this->bootAddonNav()->bootDatabase()->bootPermissions();
    }

    protected function bootAddonNav()
    {
        Nav::extend(function ($nav) {
            $items = [];

            $items['Dashboard'] = cp_route('weareframework.api-product-importer.dashboard.index');
            $items['Settings'] = cp_route('weareframework.api-product-importer.settings.index');

            $nav->tools('Product Importer - API')
                ->route('weareframework.api-product-importer.dashboard.index')
                ->icon('<svg class="fill-current" width="126" height="24" viewBox="0 0 133 24" xmlns="http://www.w3.org/2000/svg"><g fill="currentColor" fill-rule="evenodd"><path d="M13.714 0L0 7.98v3.591l13.714-7.98V0zM0 13.99v3.581l13.714-7.988V6zM0 20.336V24l6.857-4.05v-3.664zM26.367 20.169h-2.572a.212.212 0 0 1-.212-.213V5.046c0-.117.095-.213.212-.213h8.646c.116 0 .21.096.21.214v2.465a.212.212 0 0 1-.21.213h-5.652a.212.212 0 0 0-.212.214v3.21c0 .117.095.213.212.213h5.478c.117 0 .211.095.211.213v2.465a.212.212 0 0 1-.21.214h-5.48a.212.212 0 0 0-.21.213v5.49a.212.212 0 0 1-.211.212M40.627 9.215v2.703c0 .123-.103.22-.226.212-1.411-.083-3.071.62-3.071 2.803v5.023a.212.212 0 0 1-.212.213h-2.376a.212.212 0 0 1-.211-.213V9.428c0-.118.094-.213.211-.213h2.376c.117 0 .212.095.212.213v1.334c0 .075.1.098.133.03.59-1.194 1.75-1.732 2.943-1.79a.211.211 0 0 1 .221.213M50.56 14.692c0-1.84-1.28-3.09-3.016-3.09-1.714 0-2.994 1.25-2.994 3.09 0 1.84 1.28 3.089 2.994 3.089 1.735 0 3.015-1.249 3.015-3.09m2.799-5.263v10.527a.212.212 0 0 1-.211.214H50.77a.212.212 0 0 1-.212-.214v-.884a.07.07 0 0 0-.123-.046c-.779.898-1.91 1.45-3.413 1.45-2.886 0-5.272-2.519-5.272-5.783 0-3.264 2.386-5.784 5.272-5.784 1.503 0 2.634.552 3.413 1.451a.07.07 0 0 0 .123-.047v-.884c0-.118.095-.213.212-.213h2.376c.117 0 .211.095.211.213M71.723 13.421v6.535a.212.212 0 0 1-.21.213h-2.377a.212.212 0 0 1-.211-.213v-6.338c0-1.292-.651-2.08-1.823-2.08-1.236 0-2.018.832-2.018 2.475v5.943a.212.212 0 0 1-.21.213h-2.377a.212.212 0 0 1-.211-.213v-6.338c0-1.292-.651-2.08-1.823-2.08-1.193 0-2.039.832-2.039 2.475v5.943a.212.212 0 0 1-.211.213h-2.376a.212.212 0 0 1-.212-.213V9.428c0-.118.095-.213.212-.213h2.376c.116 0 .211.095.211.213v.727a.07.07 0 0 0 .127.042c.618-.812 1.592-1.289 2.889-1.289 1.304 0 2.283.536 2.895 1.488a.07.07 0 0 0 .116.003c.676-.934 1.74-1.49 3.172-1.49 2.473 0 4.1 1.774 4.1 4.512M81.623 13.662c.045 0 .08-.043.069-.088-.371-1.513-1.52-2.125-2.67-2.125-1.304 0-2.246.615-2.682 1.7-.054.135-.1.276-.139.425a.071.071 0 0 0 .069.088h5.353zm1.874 4.703a.215.215 0 0 1 .039.32c-1.022 1.146-2.488 1.79-4.297 1.79-3.645 0-5.945-2.519-5.945-5.783 0-3.264 2.322-5.784 5.728-5.784 3.21 0 5.489 2.563 5.489 5.784 0 .349-.03.667-.08.98a.212.212 0 0 1-.208.18h-7.906a.071.071 0 0 0-.068.092c.416 1.38 1.59 1.968 3.033 1.968.892 0 1.638-.31 2.14-.775a.21.21 0 0 1 .261-.02l1.814 1.248zM100.902 9.4l-3.344 10.62a.212.212 0 0 1-.202.149H95a.211.211 0 0 1-.202-.152l-1.883-6.262a.07.07 0 0 0-.135 0l-1.883 6.262a.211.211 0 0 1-.202.152h-2.358a.212.212 0 0 1-.201-.149L84.79 9.4a.142.142 0 0 1 .135-.185h2.619c.095 0 .178.064.203.156l1.736 6.33a.07.07 0 0 0 .135.002l1.86-6.335a.211.211 0 0 1 .202-.153h2.332c.093 0 .176.062.202.153l1.86 6.356a.07.07 0 0 0 .135-.001l1.736-6.352a.212.212 0 0 1 .204-.156h2.619c.095 0 .163.093.134.185M110.257 14.692c0-1.775-1.28-3.023-2.95-3.023-1.65 0-2.93 1.248-2.93 3.023 0 1.774 1.28 3.023 2.93 3.023 1.67 0 2.95-1.249 2.95-3.023m-8.678 0c0-3.264 2.538-5.784 5.727-5.784 3.19 0 5.75 2.52 5.75 5.784s-2.56 5.784-5.75 5.784c-3.189 0-5.727-2.52-5.727-5.784M121.261 9.215v2.703c0 .123-.104.22-.226.212-1.411-.083-3.072.62-3.072 2.803v5.023a.212.212 0 0 1-.21.213h-2.377a.212.212 0 0 1-.211-.213V9.428c0-.118.094-.213.21-.213h2.377c.117 0 .211.095.211.213v1.334c0 .075.1.098.133.03.59-1.194 1.75-1.732 2.944-1.79a.211.211 0 0 1 .221.213M129.507 20.089l-3.76-4.735c-.042-.053-.126-.023-.126.044v4.558a.212.212 0 0 1-.21.213h-2.377a.212.212 0 0 1-.211-.213V5.046c0-.117.094-.213.211-.213h2.376c.117 0 .211.096.211.214v8.781a.07.07 0 0 0 .126.044l3.543-4.575a.21.21 0 0 1 .166-.082h2.942c.119 0 .184.139.11.232l-4.175 5.18 4.303 5.31a.142.142 0 0 1-.109.232h-2.856a.21.21 0 0 1-.164-.08"/></g></svg>')
                ->can('view api-product-importer')
                ->children($items);
        });

        return $this;
    }

    protected function bootDatabase()
    {
        File::ensureDirectoryExists(storage_path('api-product-importer'));

        $sqlitePath = storage_path('api-product-importer/api_products.sqlite');

        if (! file_exists($sqlitePath)) {
            File::put($sqlitePath, '');
        }

        app('config')->set('database.connections.api_product_importer', [
            'driver' => 'sqlite',
            'database' => $sqlitePath,
        ]);

        if (! Schema::connection(config('statamic.api-product-importer.connection', 'api_product_importer'))->hasTable('api_products')) {
            $defaultConnection = DB::getDefaultConnection();

            DB::setDefaultConnection(config('statamic.api-product-importer.connection', 'api_product_importer'));

            require_once(__DIR__ . '/../database/migrations/create_api_products_table.php.stub');

            (new \CreateApiProductsTable())->up();

            DB::setDefaultConnection($defaultConnection);
        }

        return $this;
    }

    protected function bootPermissions()
    {
        Permission::group('api-product-importer', 'Api Product Importer', function () {
            Permission::register('view api-product-importer', function ($permission) {
                $permission->children([
                    Permission::make('edit api-product-importer')->children([
                        Permission::make('create api-product-importer'),
                        Permission::make('delete api-product-importer'),
                    ]),
                ]);
            });
        });

        return $this;
    }
}
