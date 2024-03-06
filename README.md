# CloudflareImagesLaravel
Provides access to Cloudflare Images service for Laravel.

[![Latest Stable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Total Downloads](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/downloads)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![Latest Unstable Version](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/v/unstable)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![License](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/license)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel) [![PHP Version Require](http://poser.pugx.org/alexbuckham/cloudflare-images-laravel/require/php)](https://packagist.org/packages/alexbuckham/cloudflare-images-laravel)

## Table of contents

* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)

## Installation

To get the latest version of `CloudflareImagesLaravel`, simply require the project using [Composer](https://getcomposer.org):

```
composer require marchiorineto/cloudflare-images-laravel
```

Or manually update the `require` block of `composer.json` and run `composer update`.

```json
{
    "require": {
        "marchiorineto/cloudflare-images-laravel": "^0.0.1"
    }
}
```

## Configuration
Set environment variables:

- `CLOUDFLARE_IMAGES_ACCOUNT` - Cloudflare account ID
- `CLOUDFLARE_API_TOKEN` - Cloudflare API token
- `CLOUDFLARE_IMAGES_KEY` - Create a CF images key under the Images section of your Cloudflare account
- `CLOUDFLARE_IMAGES_DELIVERY_URL` - Copy the images delivery base url from the Cloudflare images dashboard

## Usage
Create a variant

```php
use MarchioriNeto\CloudflareImagesLaravel\CloudflareImages;
use MarchioriNeto\CloudflareImagesLaravel\ImageVariant;

$variant = new ImageVariant('tiny');
$variant->fit('contain')
    ->width(50)
	->height(50)
	->metaData('keep');
	
$cfImages = new CloudflareImages();
$cfImages->createVariant($variant);
```

Upload an image
```php
use MarchioriNeto\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages();
// Pass either a file path or a file resource as the first parameter.
// If you want the image to be private (always require signed urls), pass true as the second parameter.
$cfImages->upload('/path/to/image.jpg', true);
```

Generate a signed URL
```php
use MarchioriNeto\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages();
$cfImages->getSignedUrl('image-uuid', new DateTime('+1 day'));
```

Overriding configuration

You can override the environment variables by passing new properties to the `CloudflareImages` constructor.
```php
use MarchioriNeto\CloudflareImagesLaravel\CloudflareImages;

$cfImages = new CloudflareImages('CLOUDFLARE_IMAGES_ACCOUNT', 'CF_IMAGES_CLOUDFLARE_API_TOKEN', 'CLOUDFLARE_IMAGES_KEY', 'CLOUDFLARE_IMAGES_DELIVERY_URL');
```

## Based on

work from [https://github.com/alexbuckham/CloudflareImagesLaravel](https://github.com/alexbuckham/CloudflareImagesLaravel)