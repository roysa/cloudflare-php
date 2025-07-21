<?php

use Cloudflare\API\Endpoints\WorkerScripts;

class WorkerScriptsMultipartTest extends TestCase
{
    public function testUploadScriptMultipart()
    {
        // Create a mock response with success status
        $responseBody = json_encode(['success' => true]);
        $response = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('putMultipart')->willReturn($response);

        $scriptContent = 'addEventListener("fetch", event => { event.respondWith(new Response("Hello world")) })';

        $expectedMultipart = [
            [
                'name' => 'file',
                'contents' => $scriptContent,
                'filename' => 'worker.js',
                'headers' => [
                    'Content-Type' => 'application/javascript+module'
                ]
            ],
            [
                'name' => 'metadata',
                'contents' => json_encode([
                    'type' => 'esm',
                    'main_module' => 'worker.js',
                    'compatibility_date' => date('Y-m-d'),
                    'usage_model' => 'bundled',
                ]),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Worker-Module-Type' => 'esm'
                ]
            ]
        ];

        $mock->expects($this->once())
            ->method('putMultipart')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'),
                $this->equalTo($expectedMultipart)
            );

        $scripts = new WorkerScripts($mock);
        $result = $scripts->uploadScriptMultipart('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', $scriptContent);

        $this->assertTrue($result);
    }

    public function testUploadScriptMultipartWithMetadata()
    {
        // Create a mock response with success status
        $responseBody = json_encode(['success' => true]);
        $response = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('putMultipart')->willReturn($response);

        $scriptContent = 'addEventListener("fetch", event => { event.respondWith(new Response("Hello world")) })';
        $metadata = [
            'bindings' => [
                [
                    'type' => 'kv_namespace',
                    'name' => 'MY_KV',
                    'namespace_id' => '0f2ac74b498b48028cb68387c421e279'
                ]
            ]
        ];

        $metadataWithDefaults = array_merge(
            [
                'type' => 'esm',
                'main_module' => 'worker.js',
                'compatibility_date' => date('Y-m-d'),
                'usage_model' => 'bundled',
            ],
            $metadata
        );

        $expectedMultipart = [
            [
                'name' => 'file',
                'contents' => $scriptContent,
                'filename' => 'worker.js',
                'headers' => [
                    'Content-Type' => 'application/javascript+module'
                ]
            ],
            [
                'name' => 'metadata',
                'contents' => json_encode($metadataWithDefaults),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Worker-Module-Type' => 'esm'
                ]
            ]
        ];

        $mock->expects($this->once())
            ->method('putMultipart')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'),
                $this->equalTo($expectedMultipart)
            );

        $scripts = new WorkerScripts($mock);
        $result = $scripts->uploadScriptMultipart('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', $scriptContent, $metadata);

        $this->assertTrue($result);
    }
}
