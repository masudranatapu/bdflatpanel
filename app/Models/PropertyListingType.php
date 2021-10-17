<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PropertyListingType extends Model
{
    protected $table        = 'PRD_LISTING_TYPE';
    protected $primaryKey   = 'PK_NO';
    protected $fillable     = ['NAME'];

    public function getPropertyListingType()
    {
        return $data = PropertyListingType::select('PRD_LISTING_TYPE.PK_NO','PRD_LISTING_TYPE.NAME','PRD_LISTING_TYPE.DURATION','SS_LISTING_PRICE.SELL_PRICE','SS_LISTING_PRICE.RENT_PRICE','SS_LISTING_PRICE.ROOMMAT_PRICE')->leftJoin('SS_LISTING_PRICE', 'SS_LISTING_PRICE.F_LISTING_TYPE_NO','PRD_LISTING_TYPE.PK_NO')->where('PRD_LISTING_TYPE.IS_ACTIVE', 1)->get();
    }
}
