<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupRole extends Model
{
    protected $table = 'SA_USER_GROUP_ROLE';
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';
}
