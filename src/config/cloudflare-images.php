<?php

return [
	'account_id'   => env('CLOUDFLARE_IMAGES_ACCOUNT', null),
	'account_hash' => env('CLOUDFLARE_IMAGES_ACCOUNT_HASH', null),
	'custom_domain' => env('CLOUDFLARE_IMAGES_CUSTOM_DOMAIN', null),
	'token'        => env('CLOUDFLARE_API_TOKEN', null),
	'key'          => env('CLOUDFLARE_IMAGES_KEY', null),
	'delivery_url' => env('CLOUDFLARE_IMAGES_DELIVERY_URL', null),
	'variants' => [
		// 'small'           => [
		// 	'width'         => 400,
		// 	'height'        => 400,
		// 	'always_public' => false,
		// 	'blur'          => null,
		// 	'metadata'          => false,
		// ],
	]
];
