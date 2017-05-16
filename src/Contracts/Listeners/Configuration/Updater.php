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

namespace Antares\Modules\Api\Contracts\Listeners\Configuration;

interface Updater
{

    /**
     * Response when updating auth driver failed.
     *
     * @param array $errors
     * @return mixed
     */
    public function updateAuthFailed(array $errors);

    /**
     * Response when updating auth driver succeed.
     *
     * @return mixed
     */
    public function authUpdated();
}
