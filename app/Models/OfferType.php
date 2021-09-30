<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferType extends Model
{
    protected $table 		= 'SLS_BUNDLE_TYPE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'NAME'
    ];

}
