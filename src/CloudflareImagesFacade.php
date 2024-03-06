<?php
namespace MarchioriNeto\CloudflareImagesLaravel\Facades;

use Illuminate\Support\Facades\Facade;

class CloudflareImagesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cloudflare-images';
    }

}
