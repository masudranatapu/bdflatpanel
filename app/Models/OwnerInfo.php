<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OwnerInfo extends Model
{
    protected $table = 'WEB_USER_INFO';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;
}
