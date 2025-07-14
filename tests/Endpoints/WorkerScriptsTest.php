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

    public function testBindKVNamespace()
    {
        // Script content to be preserved
        $scriptContent = 'addEventListener("fetch", event => { event.respondWith(new Response("Hello world")) })';

        // Mock for getScript
        $mockGetResponse = new \GuzzleHttp\Psr7\Response(200, [], $scriptContent);

        // Mock for uploadScript
        $responseBody = json_encode(['success' => true]);
        $mockPutResponse = new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], $responseBody);

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($mockGetResponse);
        $mock->method('put')->willReturn($mockPutResponse);

        // Expect get to be called to retrieve the script
        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'));

        // Expect put to be called with the script and metadata
        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker'),
                $this->equalTo($scriptContent),
                $this->equalTo([
                    'Content-Type' => 'application/javascript',
                    'Metadata' => json_encode([
                        'bindings' => [
                            [
                                'type' => 'kv_namespace',
                                'name' => 'MY_KV',
                                'namespace_id' => '0f2ac74b498b48028cb68387c421e279'
                            ]
                        ]
                    ])
                ])
            );

        $scripts = new WorkerScripts($mock);
        $result = $scripts->bindKVNamespace('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', '0f2ac74b498b48028cb68387c421e279', 'MY_KV');

        $this->assertTrue($result);
    }
}
