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




namespace Antares\Api\Tests\Http\Middleware;

use Mockery as m;
use Antares\Api\Http\Middleware\ApiMiddleware;
use Illuminate\Contracts\Container\Container;
use Dingo\Api\Provider\LaravelServiceProvider;
use Dingo\Api\Http\Request as ApiRequest;
use Antares\Api\Http\Router\Dispatcher;
use Antares\Testing\TestCase;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Antares\Extension\Factory as ExtensionFactory;
use ReflectionClass;
use Illuminate\Http\JsonResponse;

class ApiMiddlewareTest extends TestCase {
    
    /**
     *
     * @var Mockery
     */
    protected $container;
    
    /**
     *
     * @var Mockery
     */
    protected $dispatcher;
    
    /**
     *
     * @var Mockery
     */
    protected $router;
    
    /**
     *
     * @var Mockery
     */
    protected $responseFactory;
    
    public function setUp() {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        $this->addProvider(LaravelServiceProvider::class);
        
        parent::setUp();
        
        $this->container        = m::mock(Container::class);
        $this->dispatcher       = m::mock(Dispatcher::class);
        $this->router           = m::mock(Router::class);
        $this->responseFactory  = $this->app->make(ResponseFactory::class);
    }
    
    public function tearDown() {
        parent::tearDown();
        m::close();
    }
    
    /**
     * 
     * @return ApiMiddleware
     */
    protected function getApiMiddleware() {
        return new ApiMiddleware($this->container, $this->dispatcher, $this->router, $this->responseFactory);
    }
    
    public function testCanHandleAsApiExtensionEnabled() {
        $reflectionClass = new ReflectionClass(ApiMiddleware::class);
        $method = $reflectionClass->getMethod('canHandle');
        $method->setAccessible(true);
        
        $extensionFactory = m::mock(ExtensionFactory::class)
                ->shouldReceive('isActive')
                ->with('api')
                ->andReturn(true)
                ->getMock();
        
        $this->container
                ->shouldReceive('make')
                ->with('antares.extension')
                ->andReturn($extensionFactory)
                ->getMock();
        
        $request = m::mock(ApiRequest::class);
        $result = $method->invoke($this->getApiMiddleware(), $request);
        
        $this->assertTrue($result);
        
        $request = m::mock(Request::class);
        $result = $method->invoke($this->getApiMiddleware(), $request);
        
        $this->assertFalse($result);
    }
    
    public function testCanHandleAsApiExtensionDisabled() {
        $reflectionClass = new ReflectionClass(ApiMiddleware::class);
        $method = $reflectionClass->getMethod('canHandle');
        $method->setAccessible(true);
        
        $extensionFactory = m::mock(ExtensionFactory::class)
                ->shouldReceive('isActive')
                ->with('api')
                ->andReturn(false)
                ->getMock();
        
        $this->container
                ->shouldReceive('make')
                ->with('antares.extension')
                ->andReturn($extensionFactory)
                ->getMock();
        
        $request = m::mock(ApiRequest::class);
        $result = $method->invoke($this->getApiMiddleware(), $request);
        
        $this->assertFalse($result);
        
        $request = m::mock(Request::class);
        $result = $method->invoke($this->getApiMiddleware(), $request);
        
        $this->assertFalse($result);
    }
    
    public function testReturnResponseAsNotJsonRequest() {
        $reflectionClass = new ReflectionClass(ApiMiddleware::class);
        $method = $reflectionClass->getMethod('returnResponse');
        $method->setAccessible(true);
        
        $data       = ['test-data'];
        $request    = m::mock(Request::class)
                ->shouldReceive('isJson')
                ->andReturn(false)
                ->shouldReceive('wantsJson')
                ->andReturn(false)
                ->getMock();
        
        $result = $method->invoke($this->getApiMiddleware(), $request, $data);
        
        $this->assertEquals($data, $result);
    }
    
    public function testReturnResponseAsJsonRequest() {
        $reflectionClass = new ReflectionClass(ApiMiddleware::class);
        $method = $reflectionClass->getMethod('returnResponse');
        $method->setAccessible(true);
        
        $data           = ['test-data'];
        $statusCode     = 200;
        
        $request = m::mock(Request::class)
                ->shouldReceive('isJson')
                ->andReturn(true)
                ->shouldReceive('wantsJson')
                ->andReturn(false)
                ->getMock();
        
        $result = $method->invoke($this->getApiMiddleware(), $request, $data);
        
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals($data, $result->getData());
    }
    
    public function testReturnResponseAsJsonRequestByResponseObject() {
        $reflectionClass = new ReflectionClass(ApiMiddleware::class);
        $method = $reflectionClass->getMethod('returnResponse');
        $method->setAccessible(true);
        
        $statusCode     = 302;
        $data           = new Response(['test'], $statusCode);
        
        $request = m::mock(Request::class)
                ->shouldReceive('isJson')
                ->andReturn(true)
                ->shouldReceive('wantsJson')
                ->andReturn(false)
                ->getMock();
        
        $result = $method->invoke($this->getApiMiddleware(), $request, $data);
        
        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals(['test'], $result->getData());
    }
    
}
