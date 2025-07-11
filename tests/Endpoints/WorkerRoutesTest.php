<?php

use Cloudflare\API\Endpoints\WorkerRoutes;

class WorkerRoutesTest extends TestCase
{
    public function testListRoutes()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/listWorkerRoutes.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/workers/routes'));

        $routes = new WorkerRoutes($mock);
        $result = $routes->listRoutes('023e105f4ecef8ad9ca31a8372d0c353');

        $this->assertArrayHasKey('result', $result);
        $this->assertIsArray($result['result']);
    }

    public function testCreateRoute()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/createWorkerRoute.json');

        $mock = $this->getAdapterMock();
        $mock->method('post')->willReturn($response);

        $pattern = '*example.com/api/*';
        $script = 'my-worker';

        $mock->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/workers/routes'),
                $this->equalTo([
                    'pattern' => $pattern,
                    'script' => $script
                ])
            );

        $routes = new WorkerRoutes($mock);
        $result = $routes->createRoute('023e105f4ecef8ad9ca31a8372d0c353', $pattern, $script);

        $this->assertTrue($result);
    }

    public function testGetRoute()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/getWorkerRoute.json');

        $mock = $this->getAdapterMock();
        $mock->method('get')->willReturn($response);

        $mock->expects($this->once())
            ->method('get')
            ->with($this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/workers/routes/9a7806061c88ada191ed06f989cc3dac'));

        $routes = new WorkerRoutes($mock);
        $result = $routes->getRoute('023e105f4ecef8ad9ca31a8372d0c353', '9a7806061c88ada191ed06f989cc3dac');

        $this->assertArrayHasKey('result', $result);
    }

    public function testUpdateRoute()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/updateWorkerRoute.json');

        $mock = $this->getAdapterMock();
        $mock->method('put')->willReturn($response);

        $pattern = '*example.com/updated/*';
        $script = 'updated-worker';

        $mock->expects($this->once())
            ->method('put')
            ->with(
                $this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/workers/routes/9a7806061c88ada191ed06f989cc3dac'),
                $this->equalTo([
                    'pattern' => $pattern,
                    'script' => $script
                ])
            );

        $routes = new WorkerRoutes($mock);
        $result = $routes->updateRoute('023e105f4ecef8ad9ca31a8372d0c353', '9a7806061c88ada191ed06f989cc3dac', $pattern, $script);

        $this->assertTrue($result);
    }

    public function testDeleteRoute()
    {
        $response = $this->getPsr7JsonResponseForFixture('Endpoints/deleteWorkerRoute.json');

        $mock = $this->getAdapterMock();
        $mock->method('delete')->willReturn($response);

        $mock->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('zones/023e105f4ecef8ad9ca31a8372d0c353/workers/routes/9a7806061c88ada191ed06f989cc3dac'));

        $routes = new WorkerRoutes($mock);
        $result = $routes->deleteRoute('023e105f4ecef8ad9ca31a8372d0c353', '9a7806061c88ada191ed06f989cc3dac');

        $this->assertTrue($result);
    }
}
