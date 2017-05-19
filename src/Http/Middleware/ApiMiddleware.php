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

namespace Antares\Modules\Api\Http\Middleware;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Container\Container;
use Dingo\Api\Http\Request as ApiRequest;
use Antares\Modules\Api\Http\Router\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Closure;

class ApiMiddleware
{

    /**
     *
     * @var Container
     */
    protected $app;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     *
     * @var Router;
     */
    protected $router;

    /**
     *
     * @var ResponseFactory
     */
    protected $response;

    /**
     * 
     * @param Container $app
     * @param Dispatcher $dispatcher
     * @param Router $router
     * @param ResponseFactory $response
     */
    public function __construct(Container $app, Dispatcher $dispatcher, Router $router, ResponseFactory $response)
    {
        $this->app        = $app;
        $this->dispatcher = $dispatcher;
        $this->router     = $router;
        $this->response   = $response;
    }

    /**
     * 
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->canHandle($request)) {
            if (!$this->isGloballyEnabled()) {
                return $this->response->json(['message' => 'API is not supported.'], 403);
            }

            if (!$this->isAuthorized()) {
                return $this->response->json(['message' => 'API is not supported for unauthorized users.'], 403);
            }


            $route = $request->route();
            $data  = $this->dispatcher->handle($route);
            return $this->returnResponse($request, $data);
        }

        return $next($request);
    }

    /**
     * whether api is globally enabled
     * 
     * @return boolean
     */
    protected function isGloballyEnabled()
    {
        return config('api.enabled');
    }

    /**
     * Check if can handle API request based on extension.
     * 
     * @param Request $request
     * @return boolean
     */
    protected function canHandle(Request $request)
    {
        $isApiActive = $this->app->make('antares.extension')->isActive('antaresproject/module-api');

        return $isApiActive AND $request instanceof ApiRequest;
    }

    /**
     * Check if action is authorized.
     *
     * @return boolean
     */
    protected function isAuthorized()
    {
        return true;
        return $this->app->make('antares.acl')->make('antares/api')->can('can-use-api');
    }

    /**
     * Returns a response as JSON.
     * 
     * @param Request $request
     * @param mixed $data
     * @return Response
     */
    protected function returnResponse(Request $request, $data)
    {
        if ($request->isJson() OR $request->wantsJson()) {
            $statusCode = ($data instanceof Response) ? $data->getStatusCode() : 200;
            $content    = ($data instanceof Response) ? $data->getOriginalContent() : $data;

            return $this->response->json($content, $statusCode);
        }

        return $this->response->json(['Invalid Content Type'], 406);
    }

}
