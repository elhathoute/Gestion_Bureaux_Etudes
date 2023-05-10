<?php

$user_id = $_SESSION['user_id'];
$role_id = getUserRoleId($user_id);
$role = new Role();
$role = $role->getRolePerms($role_id);

?>