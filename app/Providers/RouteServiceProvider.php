<?php

namespace App\Providers;

use App\Bill;
use App\Category;
use App\Client;
use App\Material;
use App\Product;
use App\ProfitMargin;
use App\Project;
use App\ProjectSite;
use App\Quotation;
use App\Summary;
use App\Unit;
use App\Tax;
use App\UnitConversion;
use App\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();

        Route::model('category',Category::class);
        Route::model('summary',Summary::class);
        Route::model('unit',Unit::class);
        Route::model('tax',Tax::class);
        Route::model('material',Material::class);
        Route::model('profit_margin',ProfitMargin::class);
        Route::model('product',Product::class);
        Route::model('user',User::class);
        Route::model('client',Client::class);
        Route::model('project_site',ProjectSite::class);
        Route::model('bill',Bill::class);
        Route::model('unit_conversion',UnitConversion::class);
        Route::model('project',Project::class);
        Route::model('quotation',Quotation::class);
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->namespace($this->namespace)
             ->group(base_path('routes/api.php'));
    }
}
