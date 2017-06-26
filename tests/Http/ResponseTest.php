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

namespace Antares\Modules\Api\Tests\Http;

use Mockery as m;
use Dingo\Api\Provider\LaravelServiceProvider;
use Antares\Testing\TestCase;
use Illuminate\Contracts\Container\Container;
use Antares\Modules\Api\Http\Presenters\Factory as PresenterFactory;
use Antares\Modules\Api\Http\Response as ApiResponse;
use Illuminate\Http\RedirectResponse;
use Antares\Messages\MessageBag;
use Illuminate\Session\Store as Session;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Http\Response;

class ResponseTest extends TestCase
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
    protected $presenterFactory;

    public function setUp()
    {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        $this->addProvider(LaravelServiceProvider::class);

        parent::setUp();

        $this->container        = m::mock(Container::class);
        $this->presenterFactory = m::mock(PresenterFactory::class);
    }

    /**
     * 
     * @return ApiResponse
     */
    protected function getResponse()
    {
        return new ApiResponse($this->container, $this->presenterFactory);
    }

    public function testHandleWithRedirectResponseToValidation()
    {
        $redirect = m::mock(RedirectResponse::class);

        $baseMessageBag = new MessageBag(['error' => 'test']);

        $errorBag = new ViewErrorBag();
        $errorBag->put('messages', $baseMessageBag);

        $session = m::mock(Session::class)
                ->shouldReceive('get')
                ->once()
                ->with('errors')
                ->andReturn($errorBag)
                ->getMock();

        $messages = m::mock(MessageBag::class)
                ->shouldReceive('getSessionStore')
                ->once()
                ->andReturn($session)
                ->getMock();

        $this->container
                ->shouldReceive('make')
                ->with('antares.messages')
                ->andReturn($messages)
                ->getMock();

        $this->presenterFactory
                ->shouldReceive('getPreparedData')
                ->never()
                ->getMock();

        $response = $this->getResponse()->handle($redirect);

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testHandleWithRedirectResponseToMessages()
    {
        $redirect = m::mock(RedirectResponse::class)
                ->shouldReceive('getStatusCode')
                ->once()
                ->andReturn(200)
                ->getMock();

        $messageBag = new MessageBag(['error' => 'test']);

        $session = m::mock(Session::class)
                ->shouldReceive('get')
                ->once()
                ->with('errors')
                ->andReturnNull()
                ->getMock()
                ->makePartial();

        $messageBag->setSessionStore($session);

        $this->container
                ->shouldReceive('make')
                ->with('antares.messages')
                ->andReturn($messageBag)
                ->getMock();

        $this->presenterFactory
                ->shouldReceive('getPreparedData')
                ->never()
                ->getMock();

        $response = $this->getResponse()->handle($redirect);

        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function testHandleWithRedirectResponseToException()
    {
        $redirect   = m::mock(RedirectResponse::class);
        $messageBag = new MessageBag();

        $session = m::mock(Session::class)
                ->shouldReceive('get')
                ->once()
                ->with('errors')
                ->andReturnNull()
                ->getMock()
                ->makePartial();

        $messageBag->setSessionStore($session);

        $this->container
                ->shouldReceive('make')
                ->with('antares.messages')
                ->andReturn($messageBag)
                ->getMock();

        $this->presenterFactory
                ->shouldReceive('getPreparedData')
                ->never()
                ->getMock();

        $this->getResponse()->handle($redirect);

        $this->setExpectedException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
    }

    public function testHandleWithoutRedirectResponse()
    {
        $data = ['test'];

        $this->presenterFactory
                ->shouldReceive('getPreparedData')
                ->once()
                ->with($data)
                ->andReturn($data)
                ->getMock();

        $response = $this->getResponse()->handle($data);

        $this->assertEquals($data, $response);
    }

}
