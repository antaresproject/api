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




return [
    'public_key_description'  => '*Validates the user is in system and compare hashes',
    'private_key_description' => '*Using a "private key" only known to the user (and the system), they create a hash based on the contents of the request',
    'jwt_key_description'     => '*JWT typically looks like the following: xxxxx.yyyyy.zzzzz',
    'auth_drivers_legend'     => 'Authentication drivers'
];
