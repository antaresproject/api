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

namespace Antares\Modules\Api\Http;

use Illuminate\Contracts\Container\Container;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Antares\Modules\Api\Responses\ValidationError;
use Antares\Modules\Api\Responses\Message;
use Antares\Modules\Api\Http\Presenters\Factory as PresenterFactory;

class Response
{

    /**
     *
     * @var Container
     */
    protected $app;

    /**
     *
     * @var PresenterFactory
     */
    protected $presenterFactory;

    /**
     * 
     * @param Container $app
     */
    public function __construct(Container $app, PresenterFactory $presenterFactory)
    {
        $this->app              = $app;
        $this->presenterFactory = $presenterFactory;
    }

    /**
     * 
     * @param mixed $response
     * @return mixed
     */
    public function handle($response)
    {
        if ($response instanceof RedirectResponse) {
            return $this->handleRedirectResponse($response);
        }
        return $this->presenterFactory->getPreparedData($response);
    }

    /**
     * 
     * @param RedirectResponse $response
     * @return \Illuminate\Http\Response
     * @throws HttpException
     */
    protected function handleRedirectResponse(RedirectResponse $response)
    {
        $messages = $this->app->make('antares.messages');
        $errorBag = $messages->getSessionStore()->get('errors');

        if ($errorBag) {
            return (new ValidationError($errorBag))->response();
        }

        if ($messages->count()) {
            return (new Message($messages, $response->getStatusCode()))->response();
        }

        throw new HttpException(404, 'Not Found');
    }

}
