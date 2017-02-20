<?php

namespace ZanySoft\LaravelPDF;

use Illuminate\Support\ServiceProvider;

class PdfServiceProvider extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        //$this->registerPdfService();


        if ($this->app->runningInConsole()) {
            $this->registerResources();
        }

        $this->mergeConfigFrom(
            __DIR__ . '/../config/pdf.php', 'pdf'
        );

        $this->app->bind('pdf', function ($app) {
            return new PDF();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return array('mpdf.pdf');
    }

    /**
     * Register currency provider.
     *
     * @return void
     */
    public function registerPdfService() {
        $this->app->singleton('pdf', function ($app) {
            return new PDF($app);
        });
    }

    /**
     * Register currency resources.
     *
     * @return void
     */
    public function registerResources() {
        if ($this->isLumen() === false) {
            $this->publishes([
                __DIR__ . '/../config/pdf.php' => config_path('pdf.php'),
            ], 'config');
        }
    }

    /**
     * Check if package is running under Lumen app
     *
     * @return bool
     */
    protected function isLumen() {
        return str_contains($this->app->version(), 'Lumen') === true;
    }

}

?>