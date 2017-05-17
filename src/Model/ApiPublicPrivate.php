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

namespace Antares\Modules\Api\Model;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Antares\Model\User;

class ApiPublicPrivate extends Eloquent
{

    /**
     * The database table used by the model.
     *
     * @var String
     */
    protected $table = 'tbl_api_public_private_hashes';

    /**
     * fillable attributes
     *
     * @var array 
     */
    protected $fillable = ['user_id', 'public_key'];

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

}
