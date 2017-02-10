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




namespace Antares\Api\Tests\Responses;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Contracts\Support\MessageBag;
use Antares\Api\Responses\Message;
use Antares\Api\Contracts\ResponseContract;
use Illuminate\Http\Response;

class MessageTest extends TestCase {
    
    /**
     *
     * @var Mockery
     */
    protected $messageBag;
    
    public function setUp() {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        
        parent::setUp();
        
        $this->messageBag = m::mock(MessageBag::class)
                ->shouldReceive('keys')
                ->once()
                ->andReturn(['success'])
                ->shouldReceive('messages')
                ->once()
                ->andReturn([
                    'success' => 'Response text',
                ]);
    }
    
    public function tearDown() {
        parent::tearDown();
        m::close();
    }
    
    public function testInstance() {
        $message = new Message($this->messageBag->getMock());
        
        $this->assertInstanceOf(ResponseContract::class, $message);
    }
    
    public function testResponseInstance() {
        $message    = new Message($this->messageBag->getMock());
        $response   = $message->response();
        
        $this->assertInstanceOf(Response::class, $response);
    }
    
    public function testDifferentStatusCode() {
        $message    = new Message($this->messageBag->getMock(), 404);
        $response   = $message->response();
        
        $this->assertEquals(404, $response->getStatusCode());
    }
    
    public function testResponse() {
        $message    = new Message($this->messageBag->getMock());
        $response   = $message->response();
        
        $expectedContent = [
            'type'      => 'message',
            'statuses'  => [
                'success',
            ],
            'messages'  => [
                'success' => 'Response text',
            ],
        ];
        
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedContent, $response->getOriginalContent());
    }
}
