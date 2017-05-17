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

namespace Antares\Modules\Api\Contracts;

use Antares\Contracts\Html\Form\Fieldset;
use Antares\Modules\Api\Model\User;

interface AuthProviderPresenterContract
{

    /**
     * Setup fieldset form.
     *
     * @param Fieldset $fieldset
     * @param User $user
     * @return mixed
     */
    public function fieldset(Fieldset $fieldset, User $user);
}
