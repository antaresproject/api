<?php

use Antares\Acl\RoleActionList;
use Antares\Model\Role;
use Antares\Acl\Action;

$actions = [
    new Action('api.configuration.*', 'Configuration'),
    new Action('api.*', 'Can Use Api'),
];

$permissions = new RoleActionList;
$permissions->add(Role::admin()->name, $actions);

return $permissions;