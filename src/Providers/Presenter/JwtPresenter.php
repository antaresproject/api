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

use Antares\Modules\Api\Contracts\AuthProviderPresenterContract;
use Antares\Modules\Api\Providers\Jwt as JwtProvider;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Modules\Api\Model\User;
use Tymon\JWTAuth\JWTAuth;

class JwtPresenter implements AuthProviderPresenterContract
{

    /**
     * @var JwtProvider
     */
    protected $jwtProvider;

    /**
     * @var JWTAuth
     */
    protected $jwtAuth;

    /**
     * JwtPresenter constructor.
     * @param JwtProvider $jwtProvider
     * @param JWTAuth $jwtAuth
     */
    public function __construct(JwtProvider $jwtProvider, JWTAuth $jwtAuth)
    {
        $this->jwtProvider = $jwtProvider;
        $this->jwtAuth     = $jwtAuth;
    }

    /**
     * {@inheritdoc}
     */
    public function fieldset(Fieldset $fieldset, User $user)
    {
        $token = $this->jwtAuth->fromUser($user);
        $fieldset->legend($this->jwtProvider->getLegend());
        $fieldset->control('raw', '')
                ->field(function() {
                    return view('antares/api::admin.partials._jwt_auth', ['description' => $this->jwtProvider->getDescription()]);
                });
        $links         = [$this->jwtProvider->getResetLink()];
        if (strlen($documentation = $this->jwtProvider->getDocumentationLink()) > 0) {
            array_push($links, $documentation);
        }
        $fieldset->control('textarea', 'token')
                ->value($token)
                ->label($this->jwtProvider->getLabel())
                ->attributes([
                    'rows'     => 4,
                    'readonly' => 'readonly',
                ])
                ->wrapper(['class' => 'col-mb-16 col-18 col-dt-12 col-ld-12'])
                ->inlineHelp($this->jwtProvider->getJWTKeyDescription())
                ->help(implode(',  ', $links));
    }

}
