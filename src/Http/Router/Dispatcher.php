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
 * @version    0.9.2
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares
 * @link       http://antaresproject.io
 */

namespace Antares\Modules\Api\Http\Router;

use Illuminate\Contracts\Container\Container;
use Antares\Modules\Api\Http\Response;
use Illuminate\Routing\Route;

class Dispatcher
{

    /**
     *
     * @var Container
     */
    protected $app;

    /**
     *
     * @var Response
     */
    protected $response;

    /**
     * 
     * @param Container $app
     */
    public function __construct(Container $app, Response $response)
    {
        $this->app      = $app;
        $this->response = $response;
    }

    /**
     * 
     * @param Route $route
     * @return mixed | null
     */
    public function handle(Route $route)
    {
        $controller = array_get($route->getAction(), 'controller');
        if ($controller) {
            $parameters = $route->parameters();
            $response   = $this->app->call($controller, $parameters);
            return $this->response->handle($response);
        }
    }

}
