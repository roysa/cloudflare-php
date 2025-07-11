<?php

use Cloudflare\API\Endpoints\WorkerAssets;

class WorkerAssetsTest extends TestCase
{
    public function testListAssets()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listWorkerAssets.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker/assets'));

        $assets = new WorkerAssets($mock);
        $result = $assets->listAssets('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker');

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
    }

    public function testUploadAssets()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/uploadWorkerAssets.json');

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $assets = [
            [
                'binding' => 'ASSET_1',
                'name' => 'sample.txt',
                'content_type' => 'text/plain',
                'content' => 'Sample content'
            ],
            [
                'binding' => 'ASSET_2',
                'name' => 'image.png',
                'content_type' => 'image/png',
                'content' => 'binary data here'
            ]
        ];

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker/assets'),
                $this->equalTo([
                    'assets' => $assets
                ])
            );

        $assetsClient = new WorkerAssets($mock);
        $result = $assetsClient->uploadAssets('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', $assets);

        $this->assertTrue($result);
    }

    public function testDeleteAsset()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteWorkerAsset.json');

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('accounts/023e105f4ecef8ad9ca31a8372d0c353/workers/scripts/my-worker/assets/sample.txt'));

        $assets = new WorkerAssets($mock);
        $result = $assets->deleteAsset('023e105f4ecef8ad9ca31a8372d0c353', 'my-worker', 'sample.txt');

        $this->assertTrue($result);
    }
}
