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
	 * @param \DateTime $expires_at
	 * @return string
	 * @throws \Exception
	 */
	public function getSignedUrl(string $uuid, \DateTime $expires_at): string
	{
		if (!$this->key) {
			throw new \Exception('A key must be provided in the constructor.');
		}

		$expiry = $expires_at->getTimestamp();
		$url = $this->delivery_url . "${uuid}?exp=$expiry";

		$to_sign = \Str::replace(['https://imagedelivery.net', 'http://imagedelivery.net'], '', $url);
		$signature = hash_hmac('sha256', $to_sign, $this->key);

		return $url . "&sig=$signature";
	}

	/**
	 * @param $file
	 * @param bool $private
	 * @return \stdClass
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function upload($file, bool $private = false): \stdClass
	{
		$guzzle = new Client([
			'headers' => [
				'Authorization' => 'Bearer ' . $this->token,
				'Content-Type'  => 'multipart/form-data',
			],
		]);

		$url = sprintf('https://api.cloudflare.com/client/v4/accounts/%s/%s', $this->account_id, 'images/v1');

		if (is_string($file)) {
			$file_payload = [
				'filename' => basename($file),
				'name'     => 'file',
				'contents' => file_get_contents($file),
			];
		}

		if ($file instanceof UploadedFile) {
			$file_payload = [
				'filename' => $file->getClientOriginalName(),
				'name'     => 'file',
				'contents' => file_get_contents($file),
			];
		}

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
