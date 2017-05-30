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
use Antares\Modules\Api\Providers\Basic as BasicProvider;
use Antares\Contracts\Html\Form\Fieldset;
use Antares\Modules\Api\Model\User;

class Basic implements AuthProviderPresenterContract
{

    /**
     * Instance of BasicProvider
     * 
     * @var BasicProvider
     */
    protected $provider;

    /**
     * Constructor
     * 
     * @param BasicProvider $provider
     */
    public function __construct(BasicProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * {@inheritdoc}
     */
    public function fieldset(Fieldset $fieldset, User $user)
    {
        $fieldset->legend($this->provider->getLegend());
        return $fieldset->control('placeholder', 'custom_field')
                        ->field(function() {
                            return view('antares/api::admin.partials._basic_auth', ['documentation' => $this->provider->getDocumentationLink(), 'description' => $this->provider->getDescription()]);
                        });
    }

}
