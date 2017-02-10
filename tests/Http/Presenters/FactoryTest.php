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
use Antares\Testing\TestCase;
use Antares\Api\Http\Presenters\Factory;
use Illuminate\View\View;
use ReflectionClass;

class FactoryTest extends TestCase {
    
    /**
     *
     * @var Factory
     */
    protected $factory;
    
    public function setUp() {
        $this->addProvider(\Antares\Area\AreaServiceProvider::class);
        
        parent::setUp();
        
        $config         = config('antares/api::config', []);
        $this->factory  = new Factory($this->app, $config);
    }
    
    public function tearDown() {
        parent::tearDown();
        m::close();
    }
    
    protected function getDatatablesMock() {
        return m::mock(\Antares\Datatables\Html\Builder::class);
    }
    
    protected function getFormMock() {
        return m::mock(\Antares\Html\Form\FormBuilder::class);
    }
    
    public function testGetAdapterForClass() {
        $reflectionClass = new ReflectionClass(Factory::class);
        $method = $reflectionClass->getMethod('getAdapterForClass');
        $method->setAccessible(true);
        
        $datatables = $this->getDatatablesMock();
        $form       = $this->getFormMock();
        
        $adapter = $method->invoke($this->factory, $datatables);
        $this->assertEquals(\Antares\Api\Adapters\DatatablesAdapter::class, $adapter);
        
        $adapter = $method->invoke($this->factory, $form);
        $this->assertEquals(\Antares\Api\Adapters\FormAdapter::class, $adapter);
    }
    
    public function testCreateAdapter() {
        $reflectionClass = new ReflectionClass(Factory::class);
        $method = $reflectionClass->getMethod('createAdapter');
        $method->setAccessible(true);
        
        $adapters = [
            \Antares\Api\Adapters\DatatablesAdapter::class,
            \Antares\Api\Adapters\FormAdapter::class,
        ];
        
        foreach($adapters as $adapter) {
            $createdAdapter = $method->invoke($this->factory, $adapter);
            
            $this->assertInstanceOf($adapter, $createdAdapter);
        }
    }
    
    public function testGetPreparedDataWithoutView() {
        $input          = ['variable' => 'test'];
        $preparedInput  = $this->factory->getPreparedData($input);
        
        $this->assertEquals($input, $preparedInput);
    }
    
    public function testGetPreparedDataWithView() {
        $input = ['variable' => 'test'];
        
        $view = m::mock(View::class)
                ->shouldReceive('getData')
                ->once()
                ->andReturn($input)
                ->getMock();
        
        $preparedInput = $this->factory->getPreparedData($view);
        
        $this->assertEquals($input, $preparedInput);
    }
    
    public function testDetatablesAdapter() {
        $response = ['test'];
        
        $datatables = $this->getDatatablesMock()
                ->shouldReceive('getRawData')
                ->once()
                ->andReturn($response)
                ->getMock();
        
        $input = ['variable' => $datatables];
        
        $preparedInput = $this->factory->getPreparedData($input);
        
        $this->assertEquals($response, $preparedInput);
    }
    
    public function testFormAdapter() {
        $control = [
                "name" => "name",
                "value" => "ooo",
                "label" => "Name",
                "options" => [],
                "checked" => false,
                "type" => "input:text",
            ];
        
        $validField = m::mock(Antares\Html\Form\Field::class)
                ->shouldReceive('get')
                ->with('type')
                ->once()
                ->andReturn('input:text')
                ->shouldReceive('getAttributes')
                ->once()
                ->andReturn($control)
                ->getMock();
        
        $invalidField = m::mock(Antares\Html\Form\Field::class)
                ->shouldReceive('get')
                ->with('type')
                ->once()
                ->andReturn('input:hidden')
                ->getMock();
        
        $fieldset = m::mock(\Antares\Html\Form\Fieldset::class)
                ->shouldReceive('getName')
                ->once()
                ->andReturn('Fieldset Name')
                ->shouldReceive('controls')
                ->andReturn([$validField, $invalidField])
                ->getMock();
        
        $formRaw = [
            'name'  => 'form name',
            'rules' => [
                'name' => ['required'],
            ],
            'fieldsets' => [$fieldset],
        ];
        
        $form = $this->getFormMock()
                ->shouldReceive('getRawResponse')
                ->once()
                ->andReturn($formRaw)
                ->getMock();
        
        $input = ['variable' => $form];
        
        $expectedData = [
            'form' => [
                'name'      => 'form name',
                'rules'     => [
                    'name' => ['required'],
                ],
                'fieldsets' => [
                    [
                        'name'      => 'Fieldset Name',
                        'controls'  => [$control],
                    ]
                ],
            ],
        ];
        
        $preparedInput = $this->factory->getPreparedData($input);
        
        $this->assertEquals($expectedData, $preparedInput);
    }
    
}
