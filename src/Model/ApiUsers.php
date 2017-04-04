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

namespace Antares\Api\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ApiUsers extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var String
     */
    protected $table = 'tbl_api_users';

    /**
     * fillable attributes
     *
     * @var array 
     */
    protected $fillable = ['user_id', 'whitelist', 'enabled'];

    /**
     * whether table has times columns
     * 
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Belongs to relation to users table
     * 
     * @return Eloquent
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Whether user has enabled api support
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

}
