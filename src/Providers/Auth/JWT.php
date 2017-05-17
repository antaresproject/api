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

namespace Antares\Modules\Api\Providers\Auth;

use Tymon\JWTAuth\JWTAuth as BaseJWTAuth;
use Dingo\Api\Http\Request;
use Antares\Modules\Api\Autoban;
use Exception;

class JWT extends BaseJWTAuth
{

    /**
     * {@inheritdoc}
     */
    public function authenticate($token = false)
    {
        $autoban = app(Autoban::class);
        try {
            $authenticated = parent::authenticate($token);
        } catch (Exception $ex) {
            if (!$this->isValid()) {
                $autoban->delay();
            }
            throw $ex;
        }
        $autoban->setUser($authenticated)->checkEnabledInArea('jwt')->checkWhiteList();

        return $authenticated;
    }

    /**
     * Verify request is an instance of Dingo
     * 
     * @param \Illuminate\Http\Response $response
     * @return boolean
     */
    protected function isValid()
    {
        return !app('request') instanceof Request;
    }

}
