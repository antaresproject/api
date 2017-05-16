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

namespace Antares\Modules\Api\Tests\Responses;

use Mockery as m;
use Antares\Testing\TestCase;
use Illuminate\Support\ViewErrorBag;
use Antares\Modules\Api\Responses\ValidationError;
use Antares\Modules\Api\Contracts\ResponseContract;
use Illuminate\Http\Response;

class ValidationErrorTest extends TestCase
{

    /**
     *
     * @var Mockery
     */
    protected $errorBag;

    public function setUp()
    {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);

        parent::setUp();

        $this->errorBag = m::mock(ViewErrorBag::class)
                ->shouldReceive('messages')
                ->once()
                ->andReturn([
            'name' => 'The name field is required.',
            'test' => 'The test field is required.',
        ]);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testInstance()
    {
        $message = new ValidationError($this->errorBag->getMock());

        $this->assertInstanceOf(ResponseContract::class, $message);
    }

    public function testResponseInstance()
    {
        $message  = new ValidationError($this->errorBag->getMock());
        $response = $message->response();

        $this->assertInstanceOf(Response::class, $response);
    }

    public function testResponse()
    {
        $message  = new ValidationError($this->errorBag->getMock());
        $response = $message->response();

        $expectedContent = [
            'type'   => 'validation error',
            'fields' => [
                'name' => 'The name field is required.',
                'test' => 'The test field is required.'
            ],
        ];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expectedContent, $response->getOriginalContent());
    }

}
