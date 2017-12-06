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

namespace Antares\Modules\Api\Providers\Presenter;

use Antares\Modules\Api\Providers\Auth\PublicPrivate as PublicPrivateAuth;
use Antares\Modules\Api\Providers\PublicPrivate as PublicPrivateProvider;
use Antares\Modules\Api\Contracts\AuthProviderPresenterContract;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Modules\Api\Model\User;

class PublicPrivate implements AuthProviderPresenterContract
{

    /**
     * Instance of PublicPrivateProvider
     * 
     * @var PublicPrivateProvider
     */
    protected $provider;

    /**
     * Instance of PublicPrivateAuth
     * 
     * @var PublicPrivateAuth
     */
    protected $auth;

    /**
     * Constructor
     * 
     * @param PublicPrivateProvider $provider
     */
    public function __construct(PublicPrivateProvider $provider)
    {
        $this->provider = $provider;
        $this->auth     = new PublicPrivateAuth(app(Auth::class));
    }

    /**
     * {@inheritdoc}
     */
    public function fieldset(Fieldset $fieldset, User $user)
    {
        $fieldset->legend($this->provider->getLegend());
        $fieldset->control('raw', '')
                ->field(function() {
                    return view('antares/api::admin.partials._public_private_auth', ['description' => $this->provider->getDescription()]);
                });
        $fieldset->control('input:text', 'public_key')
                ->value($this->auth->publicKey($user))
                ->label($this->provider->getPublicKeyLabel())
                ->attributes([
                    'readonly' => 'readonly'
                ])
                ->wrapper(['class' => 'col-mb-16 col-18 col-dt-12 col-ld-12'])
                ->help($this->provider->getPublicKeyDescription())
                ->help($this->provider->getResetLink());


        $fieldset->control('input:text', 'private_key')
                ->value($this->auth->privateKey())
                ->label($this->provider->getPrivateKeyLabel())
                ->attributes([
                    'readonly' => 'readonly'
                ])
                ->wrapper(['class' => 'col-mb-16 col-18 col-dt-12 col-ld-12'])
                ->inlineHelp($this->provider->getPrivateKeyDescription());
    }

}
