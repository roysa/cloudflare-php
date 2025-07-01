<?php

namespace Cloudflare\API\Endpoints;

use Cloudflare\API\Adapter\Adapter;
use Cloudflare\API\Traits\BodyAccessorTrait;

class WorkerRoutes implements API
{
    use BodyAccessorTrait;

    private $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * List Worker Routes
     *
     * @param string $zoneId Zone ID
     * @return array
     */
    public function listRoutes(string $zoneId): array
    {
        $response = $this->adapter->get('zones/' . $zoneId . '/workers/routes');
        return $this->getBody($response);
    }

    /**
     * Create a Worker Route
     *
     * @param string $zoneId Zone ID
     * @param string $pattern Route pattern to match
     * @param string $scriptName Script to execute when the route is matched
     * @return bool
     */
    public function createRoute(string $zoneId, string $pattern, string $scriptName): bool
    {
        $options = [
            'pattern' => $pattern,
            'script' => $scriptName
        ];

        $response = $this->adapter->post('zones/' . $zoneId . '/workers/routes', $options);

        $body = $this->getBody($response);

        return $body['success'];
    }

    /**
     * Get a Worker Route
     *
     * @param string $zoneId Zone ID
     * @param string $routeId Route ID
     * @return array
     */
    public function getRoute(string $zoneId, string $routeId): array
    {
        $response = $this->adapter->get('zones/' . $zoneId . '/workers/routes/' . $routeId);
        return $this->getBody($response);
    }

    /**
     * Update a Worker Route
     *
     * @param string $zoneId Zone ID
     * @param string $routeId Route ID
     * @param string $pattern Route pattern to match
     * @param string $scriptName Script to execute when the route is matched
     * @return bool
     */
    public function updateRoute(string $zoneId, string $routeId, string $pattern, string $scriptName): bool
    {
        $options = [
            'pattern' => $pattern,
            'script' => $scriptName
        ];

        $response = $this->adapter->put('zones/' . $zoneId . '/workers/routes/' . $routeId, $options);

        $body = $this->getBody($response);

        return $body['success'];
    }

    /**
     * Delete a Worker Route
     *
     * @param string $zoneId Zone ID
     * @param string $routeId Route ID
     * @return bool
     */
    public function deleteRoute(string $zoneId, string $routeId): bool
    {
        $response = $this->adapter->delete('zones/' . $zoneId . '/workers/routes/' . $routeId);

        $body = $this->getBody($response);

        return $body['success'];
    }
}
