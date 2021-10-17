<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auth extends Model
{
    protected $table = 'SA_USER';
    protected $primaryKey   = 'PK_NO';

    protected $fillable = [
            'EMAIL','PASSWORD'
    ];
    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'UPDATED_AT';
}
