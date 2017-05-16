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

namespace Antares\Modules\Api\Listener;

use Antares\Modules\Api\Services\AuthProviderService;
use Antares\Modules\Api\Model\ApiPublicPrivate;
use Antares\Model\Component;

class PublicDriverListener
{

    /**
     * Handles input
     * 
     * @param array $input
     * @param AuthProviderService $authProviderService
     */
    public function handle(array $input = [], AuthProviderService $authProviderService)
    {

        $component = Component::findOneByName('api');
        $key       = 'auth_drivers.public';
        $enabled   = array_get($component->options, $key);
        $new       = array_get($input, 'drivers.public');
        foreach ($new as $key => $value) {
            if (isset($enabled[$key]) or ! $value) {
                continue;
            }
            ApiPublicPrivate::query()->whereHas('user', function($query) use($key) {
                $query->area($key);
            })->delete();
        }
    }

}
