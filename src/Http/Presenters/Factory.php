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

namespace Antares\Modules\Api\Http\Presenters;

use Antares\Modules\Api\Contracts\AdapterContract;
use Illuminate\Container\Container;
use Illuminate\View\View;

class Factory
{

    /**
     *
     * @var Container
     */
    protected $app;

    /**
     *
     * @var array
     */
    protected $adapters;

    /**
     *
     * @var array
     */
    protected $maps;

    /**
     * 
     * @param Container $app
     * @param array $config
     */
    public function __construct(Container $app, array $config)
    {
        $this->app      = $app;
        $this->adapters = array_get($config, 'adapters', []);
        $this->maps     = array_get($config, 'maps', []);
    }

    /**
     * 
     * @param View $input
     * @return mixed
     */
    public function getPreparedData($input)
    {
        $data = ($input instanceof View) ? $input->getData() : $input;
        if (is_array($data)) {
            $return = [];
            foreach ($data as $key => $variable) {
                if ($transformed = $this->getTransformedVariable($variable)) {
                    $variable = $transformed;
                }
                array_set($return, $key, $variable);
            }
            return $return;
        } else if ($transformed = $this->getTransformedVariable($data)) {
            return $transformed;
        }

        return $data;
    }

    /**
     * 
     * @param mixed $variable
     * @return mixed
     */
    protected function getTransformedVariable($variable)
    {
        $adapter = $this->getAdapterForClass($variable);


        if ($adapter) {
            return $this->createAdapter($adapter)->transform($variable);
        }
    }

    /**
     * 
     * @param mixed $data
     * @return string | null
     */
    protected function getAdapterForClass($data)
    {
        foreach ($this->maps as $class => $type) {
            if ($data instanceof $class AND $adapter = array_get($this->adapters, $type)) {
                return $adapter;
            }
        }
    }

    /**
     * Create an instance of Adapter based of given class name.
     * 
     * @param string $adapterClassName
     * @return AdapterContract
     */
    protected function createAdapter($adapterClassName)
    {
        return $this->app->make($adapterClassName);
    }

}
