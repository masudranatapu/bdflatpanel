<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'SA_ROLE';
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';

    public function members()
    {
        return $this->belongsToMany('App\Models\Auth','role_member', 'role_id', 'auth_id');
    }

    public function permission()
    {
        return $this->hasOne('App\Models\RolePermission','F_ROLE_NO','PK_NO');
    }
}
