<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPrimary extends Model
{
    protected $table 		= 'SLS_BUNDLE_PRIMARY_SET';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'PRIMARY_SET_NAME'
    ];


    public function primaryDetails() {
        return $this->hasMany('App\Models\OfferPrimaryDetails', 'F_SLS_BUNDLE_PRIMARY_SET_NO');
    }


}
