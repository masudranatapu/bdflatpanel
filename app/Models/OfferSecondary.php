<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSecondary extends Model
{
    protected $table 		= 'SLS_BUNDLE_SECONDARY_SET';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'SECONDARY_SET_NAME'
    ];


    public function secondaryDetails() {
        return $this->hasMany('App\Models\OfferSecondaryDetails', 'F_SLS_BUNDLE_SECONDARY_SET_NO');
    }


}
