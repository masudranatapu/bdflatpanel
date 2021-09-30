<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    protected $table = 'SA_USER_GROUP';
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';
    public function role() {
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }


}
