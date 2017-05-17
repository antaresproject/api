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

namespace Antares\Modules\Api\Http\Router;

class ControllerFinder
{

    /**
     * 
     * @param string $classPath
     * @param string $method
     * @return boolean
     */
    public function isActionExists($classPath, $method)
    {
        return class_exists($classPath) AND method_exists($classPath, $method);
    }

}
