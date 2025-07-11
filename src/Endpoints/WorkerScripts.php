<?php

namespace Cloudflare\API\Endpoints;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Traits\BodyAccessorTrait;

class WorkerScripts implements API
{
    use BodyAccessorTrait;

    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * List all Worker Scripts
     *
     * @param string $accountId Account ID
     * @return array
     */
    public function listScripts(string $accountId): array
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/workers/scripts');
        return json_decode($response->getBody(), true);
    }

    /**
     * Get a Worker Script
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @return string
     */
    public function getScript(string $accountId, string $scriptName): string
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/workers/scripts/' . $scriptName);
        return (string)$response->getBody();
    }

    /**
     * Upload a Worker Script
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @param string $script Content of the Worker script
     * @param array $metadata Optional metadata for the script
     * @return bool
     */
    public function uploadScript(string $accountId, string $scriptName, string $script, array $metadata = []): bool
    {
        $headers = [
            'Content-Type' => 'application/javascript'
        ];

        $metadataJson = '';
        if (!empty($metadata)) {
            $metadataJson = json_encode($metadata);
            $headers['Metadata'] = $metadataJson;
        }

        $response = $this->adapter->put('accounts/' . $accountId . '/workers/scripts/' . $scriptName, $script, $headers);

        $body = json_decode($response->getBody(), true);

        return $body['success'];
    }

    /**
     * Delete a Worker Script
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @return bool
     */
    public function deleteScript(string $accountId, string $scriptName): bool
    {
        $response = $this->adapter->delete('accounts/' . $accountId . '/workers/scripts/' . $scriptName);

        $body = json_decode($response->getBody(), true);

        return $body['success'];
    }
}
