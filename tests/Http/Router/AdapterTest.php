<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Api
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */

namespace Antares\Api\Tests\Http\Router;

use Mockery as m;
use Dingo\Api\Provider\LaravelServiceProvider;
use Antares\Api\Http\Router\Adapter;
use Antares\Testing\TestCase;
use Dingo\Api\Routing\Router as ApiRouter;
use Antares\Api\Http\Router\ControllerFinder;
use Illuminate\Routing\Route;
use ReflectionClass;

class AdapterTest extends TestCase
{

    /**
     *
     * @var ApiRouter
     */
    protected $apiRouter;

    /**
     *
     * @var Mockery
     */
    protected $controllerFinder;

    /**
     *
     * @var Mockery
     */
    protected $route;

    /**
     *
     * @var array
     */
    protected $config = ['version' => 'v1'];

    public function setUp()
    {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        $this->addProvider(LaravelServiceProvider::class);

        parent::setUp();

        $this->apiRouter        = $this->app->make(ApiRouter::class);
        $this->controllerFinder = m::mock(ControllerFinder::class)
                ->shouldReceive('isActionExists')
                ->andReturn(false)
                ->getMock();

        $this->route = m::mock(Route::class)
                ->shouldReceive('getMethods')
                ->andReturn(['HEAD', 'GET'])
                ->getMock();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * 
     * @return Adapter
     */
    protected function getAdapter()
    {
        $adapter = new Adapter($this->apiRouter, $this->controllerFinder, $this->config);
        $adapter->setVersion('v1');
        return $adapter;
    }

    public function testConfigVersionFallback()
    {
        $this->config = [];
        $adapter      = $this->getAdapter();
        $this->assertEquals('v1', $adapter->getVersion());
    }

    public function testSetVersion()
    {
        $adapter = $this->getAdapter();
        $this->assertEquals('v1', $adapter->getVersion());

        $adapter->setVersion('v2');
        $this->assertEquals('v2', $adapter->getVersion());
    }

    public function testGetApiRouter()
    {
        $adapter = $this->getAdapter();

        $this->assertInstanceOf(ApiRouter::class, $adapter->getApiRouter());
    }

    public function testGetRouteTargetAction()
    {
        $reflectionClass = new ReflectionClass(Adapter::class);
        $method          = $reflectionClass->getMethod('getRouteTargetAction');
        $method->setAccessible(true);

        $action = [
            'controller' => 'App\Http\Admin\TestController@index',
            'namespace'  => 'App\Http\Admin',
        ];

        $this->route
                ->shouldReceive('getAction')
                ->andReturn($action)
                ->getMock();

        $targetAction = $method->invoke($this->getAdapter(), $this->route);

        $expected = [
            'uses'       => 'TestController@index',
            'controller' => 'App\Http\Admin\TestController@index',
            'middleware' => 'api',
        ];

        $this->assertEquals($expected, $targetAction);
    }

    public function testGetRouteTargetActionWithAsParameter()
    {
        $reflectionClass = new ReflectionClass(Adapter::class);
        $method          = $reflectionClass->getMethod('getRouteTargetAction');
        $method->setAccessible(true);

        $action = [
            'controller' => 'App\Http\Admin\TestController@index',
            'namespace'  => 'App\Http\Admin',
            'as'         => 'admin.test.index',
        ];

        $this->route
                ->shouldReceive('getAction')
                ->andReturn($action)
                ->getMock();

        $targetAction = $method->invoke($this->getAdapter(), $this->route);

        $expected = [
            'as'         => 'api.admin.test.index',
            'uses'       => 'TestController@index',
            'controller' => 'App\Http\Admin\TestController@index',
            'middleware' => 'api',
        ];

        $this->assertEquals($expected, $targetAction);
    }

    public function testAdaptRoute()
    {
        $action = [
            'controller' => 'App\Http\Admin\TestController@index',
            'namespace'  => 'App\Http\Admin',
            'as'         => 'antares.test.index',
            'version'    => 'v1',
        ];

        $uri     = 'antares/test/index';
        $adapter = $this->getAdapter();

        $this->route
                ->shouldReceive('getAction')
                ->andReturn($action)
                ->shouldReceive('getUri')
                ->andReturn($uri)
                ->getMock();

        $adapter->adaptRoute($this->route);

        $this->assertCount(1, $adapter->getApiRouter()->getAdapterRoutes());
    }

    public function testAdaptRoutes()
    {
        $action = [
            'controller' => 'App\Http\Admin\TestController@index',
            'namespace'  => 'App\Http\Admin',
            'as'         => 'admin.test.index',
        ];

        $secondAction = [
            'controller' => 'App\Http\Admin\TestController@show',
            'namespace'  => 'App\Http\Admin',
            'as'         => 'admin.test.show',
        ];

        $uri       = 'admin/test/index';
        $secondUri = 'admin/test/show';
        $adapter   = $this->getAdapter();

        $this->route
                ->shouldReceive('getAction')
                ->andReturn($action)
                ->shouldReceive('getUri')
                ->andReturn($uri)
                ->getMock();

        $secondRoute = clone $this->route;
        $secondRoute
                ->shouldReceive('getAction')
                ->andReturn($secondAction)
                ->shouldReceive('getUri')
                ->andReturn($secondUri)
                ->getMock();

        $routes = [$this->route, $secondRoute];

        $adapter->adaptRoutes($routes);
    }

    public function testFindDedicatedController()
    {
        $reflectionClass = new ReflectionClass(Adapter::class);
        $method          = $reflectionClass->getMethod('getRouteTargetAction');
        $method->setAccessible(true);

        $action = [
            'controller' => 'App\Http\Admin\TestController@index',
            'namespace'  => 'App\Http\Admin',
        ];

        $this->route
                ->shouldReceive('getAction')
                ->andReturn($action)
                ->getMock();

        $this->controllerFinder = m::mock(ControllerFinder::class)
                ->shouldReceive('isActionExists')
                ->andReturn(true)
                ->getMock();

        $targetAction = $method->invoke($this->getAdapter(), $this->route);

        $expected = [
            'uses'       => 'TestController@index',
            'controller' => 'App\Http\Admin\Api\V1\TestController@index',
            'middleware' => 'api',
        ];

        $this->assertEquals($expected, $targetAction);
    }

}
