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

namespace Antares\Modules\Api\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Antares\Model\Role;
use Antares\Model\User;

class ApiRoles extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var String
     */
    protected $table = 'tbl_api_roles';

    /**
     * fillable attributes
     *
     * @var array 
     */
    protected $fillable = ['role_id', 'enabled'];

    /**
     * whether table has times columns
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * belongs to relation to brands table
     * 
     * @return Eloquent
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id', 'role_id');
    }

    /**
     * whether user role has enabled api support
     * 
     * @param User $user
     * @return boolean
     */
    public function isApiEnabledForUser(User $user)
    {
        $roles = $user->roles->pluck('id')->toArray();
        return !count($roles) ? false : $this->newQuery()->whereIn('role_id', array_values($roles))->where('enabled', 1)->get()->count() > 0;
    }

}
