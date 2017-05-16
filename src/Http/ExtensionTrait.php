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

namespace Antares\Modules\Api\Http;

use Illuminate\Contracts\Container\Container;
use Illuminate\Routing\Router;
use Antares\Modules\Api\Http\Router\Adapter as RouterAdapter;

trait ExtensionTrait
{

    /**
     * 
     * @param Router $router
     */
    protected function registerApiRoutes(Container $container, Router $router)
    {
        if (!$container->make('antares.extension')->isActive('api')) {
            return false;
        }

        /* @var $routerAdapter RouterAdapter */
        $routerAdapter = $container->make(RouterAdapter::class);
        $routes        = [];

        foreach ($router->getRoutes()->getRoutes() as $route) {
            $routeActionName = $route->getActionName();

            if (starts_with($routeActionName, $this->namespace)) {
                $routes[] = $route;
            }
        }

        $routerAdapter->adaptRoutes($routes, $this->namespace);
    }

}
