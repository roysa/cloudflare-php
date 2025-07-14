<?php

use Cloudflare\API\Endpoints\KV;

class KVTest extends TestCase
{
    public function testListNamespaces()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listKVNamespaces.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces'),
                $this->equalTo([
                    'page' => 1,
                    'per_page' => 20,
                    'order' => 'title',
                    'direction' => 'asc'
                ])
            );

        $kv = new KV($mock);
        $result = $kv->listNamespaces('023e105f4ecef8ad9ca31a8372d0c353');

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
    }

    public function testCreateNamespace()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/createKVNamespace.json');

        $mock = $this->getAdapterMock();
        $mock->method('post')->willReturn($response);

        $mock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces'),
                $this->equalTo([
                    'title' => 'My KV Namespace'
                ])
            );

        $kv = new KV($mock);
        $result = $kv->createNamespace('023e105f4ecef8ad9ca31a8372d0c353', 'My KV Namespace');

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('id', $result['result']);
        $this->assertArrayHasKey('title', $result['result']);
    }

    public function testGetNamespace()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/getKVNamespace.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279'));

        $kv = new KV($mock);
        $result = $kv->getNamespace('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279');

        $this->assertArrayHasKey('result', $result);
        $this->assertArrayHasKey('id', $result['result']);
        $this->assertArrayHasKey('title', $result['result']);
    }

    public function testDeleteNamespace()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteKVNamespace.json');

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279'));

        $kv = new KV($mock);
        $result = $kv->deleteNamespace('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279');

        $this->assertTrue($result);
    }

    public function testRenameNamespace()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/renameKVNamespace.json');

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279'),
                $this->equalTo([
                    'title' => 'New KV Namespace Title'
                ])
            );

        $kv = new KV($mock);
        $result = $kv->renameNamespace('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', 'New KV Namespace Title');

        $this->assertTrue($result);
    }

    public function testListKeys()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listKVKeys.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/keys'),
                $this->equalTo([
                    'prefix' => 'test',
                    'cursor' => 'cursor-value',
                    'limit' => 100
                ])
            );

        $kv = new KV($mock);
        $result = $kv->listKeys('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', 'test', 'cursor-value', 100);

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
    }

    public function testReadKey()
    {
        $value = 'test-value';
        $mockResponse = new \GuzzleHttp\Psr7\Response(200, [], $value);

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($mockResponse);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/values/test-key'));

        $kv = new KV($mock);
        $result = $kv->readKey('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', 'test-key');

        $this->assertEquals($value, $result);
    }

    public function testWriteKey()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/writeKVKey.json');

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/values/test-key'),
                $this->equalTo('test-value'),
                $this->equalTo([
                    'Expiration' => 3600,
                    'Expiration-TTL' => 300
                ])
            );

        $kv = new KV($mock);
        $result = $kv->writeKey('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', 'test-key', 'test-value', 3600, 300);

        $this->assertTrue($result);
    }

    public function testWriteMultipleKeys()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/writeMultipleKVKeys.json');

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $kvPairs = [
            [
                'key' => 'test-key-1',
                'value' => 'test-value-1',
                'expiration' => 3600
            ],
            [
                'key' => 'test-key-2',
                'value' => 'test-value-2',
                'expiration_ttl' => 300
            ]
        ];

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/bulk'),
                $this->equalTo($kvPairs)
            );

        $kv = new KV($mock);
        $result = $kv->writeMultipleKeys('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', $kvPairs);

        $this->assertTrue($result);
    }

    public function testDeleteKey()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteKVKey.json');

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/values/test-key'));

        $kv = new KV($mock);
        $result = $kv->deleteKey('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', 'test-key');

        $this->assertTrue($result);
    }

    public function testDeleteMultipleKeys()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteMultipleKVKeys.json');

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $keys = ['test-key-1', 'test-key-2'];

        $mock->expects($this->once())
            ->method('delete')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/kv/namespaces/0f2ac74b498b48028cb68387c421e279/bulk'),
                $this->equalTo(['keys' => $keys])
            );

        $kv = new KV($mock);
        $result = $kv->deleteMultipleKeys('023e105f4ecef8ad9ca31a8372d0c353', '0f2ac74b498b48028cb68387c421e279', $keys);

        $this->assertTrue($result);
    }
}