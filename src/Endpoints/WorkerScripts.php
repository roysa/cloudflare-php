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
     * Bind a KV Namespace to a Worker Script
     *
     * @param string $accountId Account ID
     * @param string $scriptName Name of the script
     * @param string $namespaceId KV Namespace ID
     * @param string $bindingName Name to use for the binding in the script
     * @return bool
     */
    public function bindKVNamespace(string $accountId, string $scriptName, string $namespaceId, string $bindingName): bool
    {
        // First, get the current script to preserve its content
        $scriptContent = $this->getScript($accountId, $scriptName);

        // Create metadata with KV namespace binding
        $metadata = [
            'bindings' => [
                [
                    'type' => 'kv_namespace',
                    'name' => $bindingName,
                    'namespace_id' => $namespaceId
                ]
            ]
        ];

        // Upload the script with the new metadata
        return $this->uploadScript($accountId, $scriptName, $scriptContent, $metadata);
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

    /**
     * Get Script Bindings
     *
     * @param string $accountId Account ID
     * @param string $namespaceName Namespace name
     * @param string $scriptName Name of the script
     * @return array
     */
    public function getScriptBindings(string $accountId, string $namespaceName, string $scriptName): array
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/workers/dispatch/namespaces/' . $namespaceName . '/scripts/' . $scriptName . '/bindings');
        return json_decode($response->getBody(), true);
    }
}
