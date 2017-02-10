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




use Illuminate\Database\Migrations\Migration;
use Antares\Acl\Database\Migration as AclMigration;
use Antares\Api\ApiServiceProvider;

class ImportApiAcl extends Migration
{

    /**
     * @var AclMigration
     */
    protected $aclMigration;

    public function __construct() {
        $this->aclMigration = new AclMigration(app(), 'api');
    }

    public function up() {
        $this->aclMigration->up( ApiServiceProvider::acl() );
    }

    public function down() {
        $this->aclMigration->down();
    }

}
