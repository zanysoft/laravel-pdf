<?php

namespace ZanySoft\LaravelPDF;

use Illuminate\Support\ServiceProvider;

class PdfServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->runningInConsole()) {
            $this->registerResources();
        }

        $this->mergeConfigFrom(
            __DIR__.'/../config/pdf.php', 'pdf'
        );

        $this->app->bind('mpdf.wrapper', function ($app) {
            return new PDF($app['config']['pdf']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['mpdf.wrapper'];
    }

    /**
     * Register currency resources.
     *
     * @return void
     */
    public function registerResources()
    {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__.'/../config/pdf.php' => config_path('pdf.php'),
            ], 'config');
        }
    }

    /**
     * Check if package is running under Lumen app
     *
     * @return bool
     */
    protected function isLumen()
    {
        return str_contains($this->app->version(), 'Lumen') === true;
    }
}

?>