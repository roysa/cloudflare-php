<?php

namespace Cloudflare\API\Endpoints;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Traits\BodyAccessorTrait;

class WorkerAssets implements API
{
    use BodyAccessorTrait;

    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * List all Worker Assets for a script
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @return array
     */
    public function listAssets(string $accountId, string $scriptName): array
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/workers/scripts/' . $scriptName . '/assets');
        return json_decode($response->getBody(), true);
    }

    /**
     * Upload Assets for a Worker
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @param array $assets Array of assets with keys: binding (string), name (string), content_type (string), content (string|resource)
     * @return bool
     */
    public function uploadAssets(string $accountId, string $scriptName, array $assets): bool
    {
        $options = [
            'assets' => $assets
        ];

        $response = $this->adapter->put('accounts/' . $accountId . '/workers/scripts/' . $scriptName . '/assets', $options);

        $body = json_decode($response->getBody(), true);

        return $body['success'];
    }

    /**
     * Delete an Asset from a Worker
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @param string $assetName Name of the asset to delete
     * @return bool
     */
    public function deleteAsset(string $accountId, string $scriptName, string $assetName): bool
    {
        $response = $this->adapter->delete('accounts/' . $accountId . '/workers/scripts/' . $scriptName . '/assets/' . $assetName);

        $body = json_decode($response->getBody(), true);

        return $body['success'];
    }
}
