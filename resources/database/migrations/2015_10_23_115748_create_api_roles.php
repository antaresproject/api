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
use Illuminate\Database\Schema\Blueprint;

class CreateApiRoles extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('tbl_api_roles', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->index('role_id');
            $table->tinyInteger('enabled')->default(0);
        });
        Schema::create('tbl_api_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('whitelist', 255)->nullable();
            $table->tinyInteger('enabled')->default(0);
        });
        Schema::create('tbl_api_public_private_hashes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->string('public_key', 255);
        });
        Schema::table('tbl_api_roles', function(Blueprint $table) {
            $table->foreign('role_id', 'tbl_api_roles_ibfk_1')->references('id')->on('tbl_roles')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_api_users', function(Blueprint $table) {
            $table->foreign('user_id', 'tbl_api_users_ibfk_1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
        Schema::table('tbl_api_public_private_hashes', function(Blueprint $table) {
            $table->foreign('user_id', 'tbl_api_pp_hashes_ibfk_1')->references('id')->on('tbl_users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('tbl_api_roles');
        Schema::dropIfExists('tbl_api_users');
        Schema::dropIfExists('tbl_api_public_private_hashes');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

}
