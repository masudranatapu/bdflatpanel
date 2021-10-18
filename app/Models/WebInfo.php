<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebInfo extends Model
{
    protected $table        = 'WEB_SETTINGS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'META_TITLE', 'META_DESC','LOGO_PATH','FAV_PATH'
    ];





}
