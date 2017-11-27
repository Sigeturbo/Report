<?php

namespace SigeTurbo\Report;

use Illuminate\Support\ServiceProvider;

class ReportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/report.php' => config_path('report.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //Config
        $this->mergeConfigFrom( __DIR__.'/Config/report.php', 'report');
        $this->app["report"] = $this->app->singleton('report',function ($app) {
            return new Report;
        });
    }
    
}
