<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $table 		= 'SLS_BUNDLE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'BUNDLE_NAME'
    ];

    public function offerType() {
        return $this->hasOne('App\Models\OfferType', 'PK_NO', 'F_BUNDLE_TYPE');
    }

    public function listA() {
        return $this->hasOne('App\Models\OfferPrimary', 'PK_NO', 'F_A_LIST_NO');
    }

    public function listB() {
        return $this->hasOne('App\Models\OfferSecondary', 'PK_NO', 'F_B_LIST_NO');
    }




}
