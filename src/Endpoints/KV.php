<?php

namespace Cloudflare\API\Endpoints;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Traits\BodyAccessorTrait;

class KV implements API
{
    use BodyAccessorTrait;

    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * List KV Namespaces
     *
     * @param string $accountId Account ID
     * @param int $page Page number of paginated results
     * @param int $perPage Number of namespaces per page
     * @param string $order Order direction
     * @param string $direction Direction of ordering
     * @return array
     */
    public function listNamespaces(string $accountId, int $page = 1, int $perPage = 20, string $order = 'title', string $direction = 'asc'): array
    {
        $query = [
            'page' => $page,
            'per_page' => $perPage,
            'order' => $order,
            'direction' => $direction
        ];

        $response = $this->adapter->get('accounts/' . $accountId . '/storage/kv/namespaces', $query);
        return json_decode($response->getBody(), true);
    }

    /**
     * Create a KV Namespace
     *
     * @param string $accountId Account ID
     * @param string $title Title of the namespace
     * @return array
     */
    public function createNamespace(string $accountId, string $title): array
    {
        $options = [
            'title' => $title
        ];

        $response = $this->adapter->post('accounts/' . $accountId . '/storage/kv/namespaces', $options);
        return json_decode($response->getBody(), true);
    }

    /**
     * Get a KV Namespace
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @return array
     */
    public function getNamespace(string $accountId, string $namespaceId): array
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId);
        return json_decode($response->getBody(), true);
    }

    /**
     * Delete a KV Namespace
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @return bool
     */
    public function deleteNamespace(string $accountId, string $namespaceId): bool
    {
        $response = $this->adapter->delete('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId);
        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }

    /**
     * Rename a KV Namespace
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param string $title New title for the namespace
     * @return bool
     */
    public function renameNamespace(string $accountId, string $namespaceId, string $title): bool
    {
        $options = [
            'title' => $title
        ];

        $response = $this->adapter->put('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId, $options);
        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }

    /**
     * List KV Keys in a Namespace
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param string $prefix Prefix to filter keys
     * @param string $cursor Cursor for pagination
     * @param int $limit Maximum number of keys to return
     * @return array
     */
    public function listKeys(string $accountId, string $namespaceId, string $prefix = null, string $cursor = null, int $limit = null): array
    {
        $query = [];

        if ($prefix !== null) {
            $query['prefix'] = $prefix;
        }

        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }

        if ($limit !== null) {
            $query['limit'] = $limit;
        }

        $response = $this->adapter->get('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/keys', $query);
        return json_decode($response->getBody(), true);
    }

    /**
     * Read Key-Value Pair
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param string $key Key to read
     * @return string
     */
    public function readKey(string $accountId, string $namespaceId, string $key): string
    {
        $response = $this->adapter->get('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/values/' . $key);
        return (string)$response->getBody();
    }

    /**
     * Write Key-Value Pair
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param string $key Key to write
     * @param mixed $value Value to write
     * @param int $expiration Optional expiration in seconds from now
     * @param int $expirationTtl Optional expiration TTL in seconds
     * @return bool
     */
    public function writeKey(string $accountId, string $namespaceId, string $key, mixed $value, int $expiration = null, int $expirationTtl = null): bool
    {
        $headers = [];

        if ($expiration !== null) {
            $headers['Expiration'] = $expiration;
        }

        if ($expirationTtl !== null) {
            $headers['Expiration-TTL'] = $expirationTtl;
        }

        $response = $this->adapter->put(
            'accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/values/' . $key,
            $value,
            $headers);

        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }

    /**
     * Write Multiple Key-Value Pairs
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param array $kvPairs Array of key-value pairs with optional expiration
     * @return bool
     */
    public function writeMultipleKeys(string $accountId, string $namespaceId, array $kvPairs): bool
    {
        $response = $this->adapter->put('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/bulk', $kvPairs);
        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }

    /**
     * Delete Key-Value Pair
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param string $key Key to delete
     * @return bool
     */
    public function deleteKey(string $accountId, string $namespaceId, string $key): bool
    {
        $response = $this->adapter->delete('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/values/' . $key);
        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }

    /**
     * Delete Multiple Key-Value Pairs
     *
     * @param string $accountId Account ID
     * @param string $namespaceId Namespace ID
     * @param array $keys Array of keys to delete
     * @return bool
     */
    public function deleteMultipleKeys(string $accountId, string $namespaceId, array $keys): bool
    {
        $options = [
            'keys' => $keys
        ];

        $response = $this->adapter->delete('accounts/' . $accountId . '/storage/kv/namespaces/' . $namespaceId . '/bulk', $options);
        $body = json_decode($response->getBody(), true);
        return $body['success'];
    }
}
