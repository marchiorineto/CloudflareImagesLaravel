<?php

return [
	'account_id'   => env('CF_IMAGES_ACCOUNT_ID', null),
	'account_hash' => env('CF_IMAGES_ACCOUNT_HASH', null),
	'custom_domain' => env('CF_IMAGES_CUSTOM_DOMAIN', null),
	'token'        => env('CF_IMAGES_TOKEN', null),
	'key'          => env('CF_IMAGES_KEY', null),
	'delivery_url' => env('CF_IMAGES_DELIVERY_URL', null),
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
