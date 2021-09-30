<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'SA_PERMISSION_GROUP_DTL';

    // public $timestamps 		= false;
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';

    public function group() {
        return $this->belongsTo('App\Models\PermissionGroup', 'F_PERMISSION_GROUP_NO', 'PK_NO');
    }
}
