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




namespace Antares\Api\Providers;

use Antares\Api\Contracts\AuthProviderPresenterContract;
use Antares\Api\Providers\Presenter\JwtPresenter;
use Illuminate\Contracts\Container\Container;
use Dingo\Api\Auth as DingoAuth;

class Jwt extends AuthProvider
{

    /**
     * Basic constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container, 'jwt', 'JSON Web Token');
    }

    /**
     * @return bool
     */
    public function isGlobalConfigurable()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function isConfigurablePerUser()
    {
        return true;
    }

    /**
     * @return AuthProviderPresenterContract | null
     */
    public function getPresenter()
    {
        return $this->container->make(JwtPresenter::class);
    }

    /**
     * Extends the Auth by the provider.
     */
    public function registerAuth()
    {
        $this->container->make(DingoAuth\Auth::class)->extend('jwt', function($app) {
            return new DingoAuth\Provider\JWT($app[\Antares\Api\Providers\Auth\JWT::class]);
        });
    }

    /**
     * JWT key description getter
     * 
     * @return String
     */
    public function getJWTKeyDescription()
    {
        return trans('antares/api::global.jwt_key_description');
    }

    /**
     * Reset link getter
     * 
     * @return String
     */
    public function getResetLink()
    {
        return app('html')->link(handles('antares::api/user/reset', ['provider' => $this->name]), trans('Reset JSON Web Token'), [
                    'class'            => "triggerable confirm",
                    'data-title'       => trans("Are you sure?"),
                    'data-description' => trans('Do you really want to reset json web token?'),
                ])->get();
    }

    /**
     * Label getter
     * 
     * @return String
     */
    public function getLabel()
    {
        return trans('antares/api::labels.jwt_token_label');
    }

    /**
     * Legend getter
     * 
     * @return String
     */
    public function getLegend()
    {
        return $this->label;
    }

    /**
     * Provider description getter
     * 
     * @return String
     */
    public function getDescription()
    {
        return 'JSON Web Token (JWT) is an open standard (RFC 7519) that defines a compact and self-contained way for securely transmitting information between parties as a JSON object. This information can be verified and trusted because it is digitally signed. JWTs can be signed using a secret (with the HMAC algorithm) or a public/private key pair using RSA.&nbsp;<span class="label-basic label-basic--success">RECOMMENDED</span>';
    }

}
