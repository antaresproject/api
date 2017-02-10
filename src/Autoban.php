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




namespace Antares\Api;

use Antares\Api\Services\AuthProviderService;
use Antares\Api\Model\ApiUsers;
use Antares\Model\User;
use Exception;

class Autoban
{

    /**
     * User instance
     *
     * @var User 
     */
    protected $user;

    /**
     * AuthProviderService instance
     *
     * @var AuthProviderService 
     */
    protected $authProviderService;

    /**
     * Construct
     * 
     * @param AuthProviderService $authProviderService
     */
    public function __construct(AuthProviderService $authProviderService)
    {
        $this->authProviderService = $authProviderService;
    }

    /**
     * Delays response when error login
     * 
     * @return $this
     */
    public function delay()
    {
        sleep(config('api.failed_login_delay', config('antares/api::failed_login_delay', 3)));
        return $this;
    }

    /**
     * User setter
     * 
     * @param User $user
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Whether requested ip address is whitelisted
     * 
     * @return $this
     * @throws Exception
     */
    public function checkEnabledInArea($driver)
    {
        $area = $this->user->getArea();
        if (!$this->authProviderService->isDriverEnabledForArea($driver, $area)) {
            throw new Exception('Api is not supported for user area.');
        }
        return $this;
    }

    /**
     * Whether requested ip address is whitelisted
     * 
     * @return $this
     * @throws Exception
     */
    public function checkWhiteList()
    {
        $api = ApiUsers::query()->where(['user_id' => $this->user->id])->first();
        if (is_null($api)) {
            throw new Exception('Api is not configured for requested user.');
        }
        if (!$api->enabled) {
            throw new Exception('Api is not enabled for requested user.');
        }
        $enabledWhitelistAreas = config('api.whitelist_required_areas', config('antares/api::whitelist_required_areas'));
        if (empty($enabledWhitelistAreas)) {
            return $this;
        }
        if (!is_null($api) && strlen($api->whitelist) && in_array($this->user->getArea(), $enabledWhitelistAreas)) {
            if ($api->whitelist !== request()->ip()) {
                throw new Exception('Ip address is not whitelisted.');
            }
        }
        return $this;
    }

}
