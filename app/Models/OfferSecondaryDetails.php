<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSecondaryDetails extends Model
{
    protected $table 		= 'SLS_BUNDLE_SECONDARY_SET_DTL';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable     = ['PRD_VARIANT_NAME'];


    public function variant() {
        return $this->hasOne('App\Models\ProductVariant', 'PK_NO', 'F_PRD_VARIANT_NO');
    }


}



