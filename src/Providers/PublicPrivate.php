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

namespace Antares\Modules\Api\Providers;

use Antares\Modules\Api\Providers\Presenter\PublicPrivate as PublicPrivatePresenter;
use Antares\Modules\Api\Providers\Auth\PublicPrivate as PublicPrivateAuth;
use Antares\Modules\Api\Contracts\AuthProviderPresenterContract;
use Illuminate\Contracts\Container\Container;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Dingo\Api\Auth as DingoAuth;

class PublicPrivate extends AuthProvider
{

    /**
     * Basic constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        parent::__construct($container, 'public', 'Public / Private Keys');
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
        return $this->container->make(PublicPrivatePresenter::class);
    }

    /**
     * Auth provider getter
     *  
     * @return PublicPrivateAuth
     */
    protected function getAuth()
    {
        return new PublicPrivateAuth(app(Auth::class));
    }

    /**
     * Extends the Auth by the provider.
     */
    public function registerAuth()
    {
        $this->container->make(DingoAuth\Auth::class)->extend('custom', function() {
            return $this->getAuth();
        });
    }

    /**
     * Public key label getter
     * 
     * @return String
     */
    public function getPublicKeyLabel()
    {
        return trans('antares/api::labels.public_key');
    }

    /**
     * Private key label getter
     * 
     * @return String
     */
    public function getPrivateKeyLabel()
    {
        return trans('antares/api::labels.private_key');
    }

    /**
     * Public key description getter
     * 
     * @return String
     */
    public function getPublicKeyDescription()
    {
        return trans('antares/api::global.public_key_description');
    }

    /**
     * Private key description getter
     * 
     * @return String
     */
    public function getPrivateKeyDescription()
    {
        return trans('antares/api::global.private_key_description');
    }

    /**
     * Reset link getter
     * 
     * @return String
     */
    public function getResetLink()
    {
        return app('html')->link(handles('antares::api/user/reset', ['provider' => $this->name]), trans('Reset public key'), [
                    'class'            => "triggerable confirm",
                    'data-title'       => trans("Are you sure?"),
                    'data-description' => trans('Do you really want to reset api public key?'),
                ])->get();
    }

    /**
     * Resets private api key
     * 
     * @return String
     */
    public function reset()
    {
        return $this->getAuth()->publicKey(user(), true);
    }

    /**
     * Provider description getter
     * 
     * @return String
     */
    public function getDescription()
    {
        return 'A keyed-hash message authentication code (HMAC) is a specific type of message authentication code (MAC) involving a cryptographic hash function and a secret cryptographic key. It may be used to simultaneously verify both the data integrity and the authentication of a message, as with any MAC. Any cryptographic hash function, such as MD5 or SHA-1, may be used in the calculation of an HMAC; the resulting MAC algorithm is termed HMAC-MD5 or HMAC-SHA1 accordingly. The cryptographic strength of the HMAC depends upon the cryptographic strength of the underlying hash function, the size of its hash output, and on the size and quality of the key.&nbsp;<span class="label-basic label-basic--success" >STRONG SECURITY</span>';
    }

}
