<?php

use Cloudflare\API\Endpoints\WorkerScripts;

class WorkerScriptsTest extends TestCase
{
    public function testListScripts()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listWorkerScripts.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts'));

        $scripts = new WorkerScripts($mock);
        $result = $scripts->listScripts('023e105f4ecef8ad9ca31a8372d0c353');

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
    }

    public function testGetScript()
    {
        $scriptContent = 'addEventListener("fetch", event => { event.respondWith(new Response("Hello world")) })';

        // Create a mock response with JavaScript content
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], $scriptContent);

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($mockResponse);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'));

        $scripts = new WorkerScripts($mock);
        $result = $scripts->getScript('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker');

        $this->assertEquals($scriptContent, $result);
        $this->assertIsString($result);
    }

    public function testUploadScript()
    {
        // Create a mock response with success status
        $responseBody = json_encode(['success' => true]);
        $response = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $scriptContent = 'addEventListener("fetch", event => { event.respondWith(new Response("Hello world")) })';

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'),
                $this->equalTo($scriptContent),
                $this->equalTo([
                    'Content-Type' => 'application/javascript'
                ])
            );

        $scripts = new WorkerScripts($mock);
        $result = $scripts->uploadScript('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', $scriptContent);

        $this->assertTrue($result);
    }

    public function testUploadScriptWithMetadata()
    {
        // Create a mock response with success status
        $responseBody = json_encode(['success' => true]);
        $response = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

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

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'),
                $this->equalTo($scriptContent),
                $this->equalTo([
                    'Content-Type' => 'application/javascript',
                    'Metadata' => json_encode($metadata)
                ])
            );

        $scripts = new WorkerScripts($mock);
        $result = $scripts->uploadScript('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', $scriptContent, $metadata);

        $this->assertTrue($result);
    }

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
                    'usage_model' => 'bundled'
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

        $metadataWithMainModule = array_merge(
            [
                'type' => 'esm',
                'main_module' => 'worker.js',
                'compatibility_date' => date('Y-m-d'),
                'usage_model' => 'bundled'
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
                'contents' => json_encode($metadataWithMainModule),
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

    public function testDeleteScript()
    {
        // Create a mock response with success status
        $responseBody = json_encode(['success' => true]);
        $response = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'));

        $scripts = new WorkerScripts($mock);
        $result = $scripts->deleteScript('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker');

        $this->assertTrue($result);
    }


    public function testGetScriptBindings()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/getScriptBindings.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/dispatch/namespaces/my-namespace/scripts/my-worker/bindings'));

        $scripts = new WorkerScripts($mock);
        $result = $scripts->getScriptBindings('023e105f4ecef8ad9ca31a8372d0c353', 'my-namespace', 'my-worker');

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
        $this->assertCount(3, $result['result']);
        $this->assertEquals('MY_KV', $result['result'][0]['name']);
        $this->assertEquals('kv_namespace', $result['result'][0]['type']);
        $this->assertEquals('MY_ENV_VAR', $result['result'][1]['name']);
        $this->assertEquals('plain_text', $result['result'][1]['type']);
        $this->assertEquals('MY_SECRET', $result['result'][2]['name']);
        $this->assertEquals('secret_text', $result['result'][2]['type']);
    }
}
