<?php

namespace AlexBuckham\CloudflareImagesLaravel;

use GuzzleHttp\Client;

class CloudflareImages
{

    private ?string $account_id;
    private ?string $token;
    private ?string $key;

    public function __construct($account_id = null, $token = null, $key = null)
    {
        $this->account_id = $account_id ?: config('cloudflare-images.account_id');
        $this->token = $token ?: config('cloudflare-images.token');
        $this->key = $key ?: config('cloudflare-images.key');
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
     * @param string $url
     * @param \DateTime $expires_at
     * @return string
     * @throws \Exception
     */
    public function getSignedUrl(string $url, \DateTime $expires_at): string
    {
        if (!$this->key) {
            throw new \Exception('A key must be provided in the constructor.');
        }

        $expiry = $expires_at->getTimestamp();
        $url .= "?exp=$expiry";

        $to_sign = \Str::replace(['https://imagedelivery.net', 'http://imagedelivery.net'], '', $url);
        $signature = hash_hmac('sha256', $to_sign, $this->key);

        return $url . "&sig=$signature";
    }

    /**
     * @param $id
     * @param array $options
     * @param bool $never_private
     * @return mixed
     */
    public function createVariant($id, array $options = [], bool $never_private = false)
    {
        return $this->makeCall('POST', 'images/v1/variants', [
            'json' => [
                'id'                     => $id,
                'options'                => $options,
                'neverRequireSignedURLs' => $never_private,
            ],
        ]);
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

        $response = $guzzle->request('POST', $url, [
            'multipart' => [
                [
                    'filename' => basename($file),
                    'name'     => 'file',
                    'contents' => file_get_contents($file),
                ],

                [
                    'name'     => 'requireSignedURLs',
                    'contents' => $private ? 'true' : 'false',
                ],
            ],
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
