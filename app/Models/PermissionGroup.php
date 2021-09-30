<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $table = 'SA_PERMISSION_GROUP';
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';

    public function permissions() {
        return $this->hasMany('App\Models\Permission','F_PERMISSION_GROUP_NO','PK_NO');
    }
}
