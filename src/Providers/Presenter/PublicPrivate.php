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

namespace Antares\Api\Providers\Presenter;

use Antares\Api\Providers\Auth\PublicPrivate as PublicPrivateAuth;
use Antares\Api\Providers\PublicPrivate as PublicPrivateProvider;
use Antares\Api\Contracts\AuthProviderPresenterContract;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Api\Model\User;

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
        $fieldset->control('placeholder', '')
                ->field(function() {
                    return '<p>' . $this->provider->getDescription() . '</p>';
                });
        $fieldset->control('input:text', 'public_key')
                ->value($this->auth->publicKey($user))
                ->label($this->provider->getPublicKeyLabel())
                ->attributes([
                    'readonly' => 'readonly'
                ])
                ->wrapper(['class' => 'w50p'])
                ->help($this->provider->getPublicKeyDescription())
                ->help($this->provider->getResetLink());


        $fieldset->control('input:text', 'private_key')
                ->value($this->auth->privateKey())
                ->label($this->provider->getPrivateKeyLabel())
                ->attributes([
                    'readonly' => 'readonly'
                ])
                ->wrapper(['class' => 'w50p'])
                ->inlineHelp($this->provider->getPrivateKeyDescription());
    }

}
