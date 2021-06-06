<?php

namespace   Aifst\Discount;

use Aifst\Discount\Contracts\DiscountOwner;
use Illuminate\Support\ServiceProvider;

/**
 * Class DiscountServiceProvider
 * @package Aifst\Discount
 */
class DiscountServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerPublishables();
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton('discounts', function ($app) {
            $owner_interface = config('discount.owner');
            try {
                $owner = app($owner_interface);
            } catch (\Exception $e) {
                $owner = null;
            }
            $builder = config('discount.builder');
            return new Discounts(
                new $builder,
                $owner
            );
        });
    }

    /**
     * Publish migrations and config
     */
    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__.'/../config/discount.php' => config_path('discount.php'),
        ], 'config');

        if (! class_exists('CreateDiscountsTable')) {
            $this->publishes([
                __DIR__.'/../database/migrations/create_discounts_table.php.stub' =>
                    database_path('migrations/'.date('Y_m_d_His', time()).'_create_discounts_table.php'),
            ], 'migrations');
        }
    }
}
