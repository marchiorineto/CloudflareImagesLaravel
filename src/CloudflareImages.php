<?php

namespace AlexBuckham\CloudflareImagesLaravel;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;

class CloudflareImages
{

	private ?string $account_id;
	private ?string $token;
	private ?string $key;
	private ?string $delivery_url;

	public function __construct($account_id = null, $token = null, $key = null, $delivery_url = null)
	{
		$this->account_id = $account_id ?: config('cloudflare-images.account_id');
		$this->token = $token ?: config('cloudflare-images.token');
		$this->key = $key ?: config('cloudflare-images.key');
		$this->delivery_url = $delivery_url ?: config('cloudflare-images.delivery_url');
	}

	/**
	 * @param ImageVariant $variant
	 * @return mixed
	 */
	public function createVariant(ImageVariant $variant)
	{
		return $this->makeCall('POST', 'images/v1/variants', [
			'json' => [
				'id'                     => $variant->id,
				'options'                => $variant->getOptions(),
				'neverRequireSignedURLs' => $variant->alwaysPublic,
			],
		]);
	}

	/**
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function generateUploadUrl(bool $private = false): \stdClass
	{
		return $this->makeCall('POST', 'images/v1/direct_upload', [
			'json' => [
				'requireSignedUrls' => $private,
			],
		]);
	}

	/**
	 * @param string $uuid
	 * @param string $variant
	 * @param \DateTime|null $expires_at
	 * @return string
	 * @throws \Exception
	 */
	public function getSignedUrl(string $uuid, string $variant, \DateTime $expires_at = null): string
	{
		if (!$this->key) {
			throw new \Exception('A key must be provided in the constructor.');
		}

		if (!in_array($variant, array_keys(config('cloudflare-images.variants')))) {
			throw new \Exception('Variant not found.');
		}

		$expiry = $expires_at ? $expires_at->getTimestamp() : now()->addDay()->timestamp;
		$to_sign = '/' . config('cloudflare-images.account_hash') . "/{$uuid}/{$variant}?exp=$expiry";

		$signature = hash_hmac('sha256', $to_sign, $this->key);

		$base_url = config('cloudflare-images.custom_domain') ? config('cloudflare-images.custom_domain') . '/cdn-cgi/imagedelivery' : 'imagedelivery.net';

		return 'https://' . $base_url . $to_sign . "&sig=$signature";
	}

	/**
	 * @param $file
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function upload($file, $filename = null, bool $private = false): \stdClass
	{
		$guzzle = new Client([
			'headers' => [
				'Authorization' => 'Bearer ' . $this->token,
				'Content-Type'  => 'multipart/form-data',
			],
		]);

		$url = sprintf('https://api.cloudflare.com/client/v4/accounts/%s/%s', $this->account_id, 'images/v1');

		$file_payload = [
			'filename' => $filename,
			'name'     => 'file',
			'contents' => $file,
		];

		$payload = [
			$file_payload, [
				'name'     => 'requireSignedURLs',
				'contents' => $private ? 'true' : 'false',
			],
		];

		$response = $guzzle->request('POST', $url, [
			'multipart' => $payload,
		]);

		return json_decode($response->getBody()->getContents())->result;
	}

	/**
	 * @param $file
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function uploadFromRequest(UploadedFile $file, bool $private = false): \stdClass
	{
		$this->upload(file_get_contents($file), $file->getClientOriginalName(), $private);
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array $data
	 * @return mixed
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	private function makeCall(string $method, string $url, $data = [])
	{
		$guzzle = new Client([
			'headers' => [
				'Authorization' => 'Bearer ' . $this->token,
				'Content-Type'  => 'application/json',
			],
		]);

		$url = sprintf('https://api.cloudflare.com/client/v4/accounts/%s/%s', $this->account_id, $url);

		$response = $guzzle->request($method, $url, $data);

		return json_decode($response->getBody()->getContents())->result;
	}

}
