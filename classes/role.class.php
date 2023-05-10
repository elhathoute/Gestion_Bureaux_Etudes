<?php


class Role{
    public $permissions;
    public function __construct()
    {
        $this->permissions=array();
    }

    // return a role object with associated permissions

    public static function getRolePerms($roleid){
        $cnx = new mysqli(DATABASE_HOST,DATABASE_USER, DATABASE_PASS,DATABASE_NAME);
        if(mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
            exit();
        }

        $role = new Role();
        $query = "SELECT t2.perm_desc FROM `role_perm` as t1
                  JOIN `permissions` as t2 ON t1.perm_id = t2.id
                  WHERE t1.role_id = $roleid";
        $res = mysqli_query($cnx,$query);
        while($row = mysqli_fetch_assoc($res)){
            $role->permissions[$row['perm_desc']] = true;
        }
        return $role;
    }

    //check if the permission is set

    public function hasPerm($permission){
        return isset($this->permissions[$permission]);
    }

    public function get_perms(){
        return $this->permissions;
    }
}





?>