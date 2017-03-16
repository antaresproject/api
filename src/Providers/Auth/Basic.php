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

namespace Antares\Api\Providers\Auth;

use Antares\Auth\AuthManager as BaseAuthManager;
use Dingo\Api\Http\Request;
use Antares\Api\Autoban;

class Basic extends BaseAuthManager
{

    /**
     * Api basic login attempt
     * 
     * @param String $identifier
     * @return \Illuminate\Http\Response
     */
    public function onceBasic($identifier)
    {
        $response = parent::onceBasic($identifier);
//        if (!$this->isValid($response)) {
//            app(Autoban::class)->delay();
//        }
        return $response;
    }

    /**
     * Verify request and response status code
     * 
     * @param \Illuminate\Http\Response $response
     * @return boolean
     */
    protected function isValid($response)
    {
        if (is_null($response)) {
            return true;
        }
        if (app('request') instanceof Request) {
            return $response->getStatusCode() !== 401;
        }
        return true;
    }

}
