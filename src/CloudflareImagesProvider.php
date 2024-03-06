<?php

namespace MarchioriNeto\CloudflareImagesLaravel;

use Illuminate\Support\ServiceProvider;

class CloudflareImagesProvider extends ServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/cloudflare-images.php' => config_path('cloudflare-images.php'),
        ]);

    }

    public function register()
    {
        $this->app->singleton(CloudflareImages::class, function() {
            return new CloudflareImages();
        });
    }

}
