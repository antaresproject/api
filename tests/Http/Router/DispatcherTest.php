<?php

/**
 * Part of the Antares package.
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
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\Api\Tests\Http\Router;

use Mockery as m;
use Illuminate\Contracts\Container\Container;
use Dingo\Api\Provider\LaravelServiceProvider;
use Antares\Modules\Api\Http\Router\Dispatcher;
use Antares\Testing\TestCase;
use Antares\Modules\Api\Http\Response;
use Illuminate\Routing\Route;

class DispatcherTest extends TestCase
{

    /**
     *
     * @var Mockery
     */
    protected $container;

    /**
     *
     * @var Mockery
     */
    protected $response;

    public function setUp()
    {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        $this->addProvider(LaravelServiceProvider::class);

        parent::setUp();

        $this->container = m::mock(Container::class);
        $this->response  = m::mock(Response::class);
    }

    /**
     * 
     * @return Dispatcher
     */
    protected function getDispatcher()
    {
        return new Dispatcher($this->container, $this->response);
    }

    public function testHandleWithoutControllerAction()
    {
        $route = m::mock(Route::class)
                ->shouldReceive('getAction')
                ->once()
                ->andReturnNull()
                ->getMock();

        $response = $this->getDispatcher()->handle($route);

        $this->assertNull($response);
    }

    public function testHandleWithControllerAction()
    {
        $action       = ['controller' => 'TestController@action'];
        $parameters   = ['id' => 1];
        $responseData = [];

        $route = m::mock(Route::class)
                ->shouldReceive('getAction')
                ->once()
                ->andReturn($action)
                ->shouldReceive('parameters')
                ->once()
                ->andReturn($parameters)
                ->getMock();

        $this->container
                ->shouldReceive('call')
                ->with($action['controller'], $parameters)
                ->once()
                ->andReturn($responseData)
                ->getMock();

        $this->response
                ->shouldReceive('handle')
                ->once()
                ->with($responseData)
                ->andReturn($responseData)
                ->getMock();

        $response = $this->getDispatcher()->handle($route);

        $this->assertEquals($responseData, $response);
    }

}
