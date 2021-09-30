<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'WEB_USER';
    protected $primaryKey = 'PK_NO';

    public function adminUser()
    {
        return $this->hasOne('App\DB\Admin\AdminUser');
    }

    public function attachRole($role)
    {
        if (is_object($role)) {
            $role = $role->getKey();
        }

        if (is_array($role)) {
            $role = $role['id'];
        }

        $this->roles()->attach($role);
    }

    public function user()
    {
        return $this->belongsTo('App\DB\Admin\Member','member_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\DB\Admin\Role','role_member','member_id','role_id');
    }
}
