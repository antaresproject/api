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

namespace Antares\Modules\Api\Http\Router;

use Dingo\Api\Routing\Router as ApiRouter;
use Illuminate\Support\Facades\Request;
use Illuminate\Routing\Route;

class Adapter
{

    /**
     *
     * @var ApiRouter
     */
    protected $apiRouter;

    /**
     *
     * @var ControllerFinder
     */
    protected $finder;

    /**
     *
     * @var string
     */
    protected $version;

    /**
     * constructing
     * 
     * @param ApiRouter $apiRouter
     * @param \Antares\Modules\Api\Http\Router\ControllerFinder $finder
     */
    public function __construct(ApiRouter $apiRouter, ControllerFinder $finder)
    {
        $this->apiRouter = $apiRouter;
        $this->finder    = $finder;
        $this->version   = Request::segment(2);
    }

    /**
     * 
     * @return ApiRouter
     */
    public function getApiRouter()
    {
        return $this->apiRouter;
    }

    /**
     * 
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * 
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * 
     * @param array $routes
     */
    public function adaptRoutes(array $routes, $namespace = null)
    {
        foreach ($routes as $route) {
            $this->adaptRoute($route, $namespace);
        }
    }

    /**
     * 
     * @param Route $route
     */
    public function adaptRoute(Route $route, $namespace = null)
    {

        $middleware = array_merge(['api.throttle', 'api.auth',], array_get($route->getAction(), 'middleware', []));
        $attributes = array_merge(compact('middleware', 'namespace'), config('api.throttling', []));


        $this->apiRouter->version(config('api.available_versions'), $attributes, function(ApiRouter $api) use($namespace, $route) {
            $targetAction = $this->getRouteTargetAction($route);
            $uri          = !in_array($this->version, config('api.available_versions')) ? $route->uri() : ($this->version . '/' . $route->uri());
            $api->addRoute($route->methods(), $uri, $targetAction);
        });
    }

    /**
     * 
     * @param Route $route
     * @return array
     */
    protected function getRouteTargetAction(Route $route)
    {

        $actionParams = $route->getAction();
        $controller   = $actionParams['controller'];
        $namespace    = $actionParams['namespace'];
        $as           = array_get($actionParams, 'as');
        $action       = substr($controller, strlen($namespace) + 1);
        $targetNs     = $this->getTargetNamespace($namespace, $action);

        $targetAction = [
            'uses'       => $action,
            'controller' => $targetNs . '\\' . $action,
            'middleware' => 'api',
        ];

        if ($as) {
            $targetAction['as'] = 'api.' . $as;
        }

        return $targetAction;
    }

    /**
     * 
     * @param string $namespace
     * @param string $action
     * @return string
     */
    protected function getTargetNamespace($namespace, $action)
    {
        $versioned = $namespace . '\Api\\' . strtoupper($this->version);
        list($controller, $method) = explode('@', $action);
        $classPath = $versioned . '\\' . $controller;

        if ($this->finder->isActionExists($classPath, $method)) {
            return $versioned;
        }

        return $namespace;
    }

}
