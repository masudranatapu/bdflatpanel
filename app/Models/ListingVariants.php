<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingVariants extends Model
{
    protected $table = 'PRD_LISTING_VARIANTS';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;
    protected $fillable = ['F_LISTING_NO', 'PROPERTY_SIZE', 'BEDROOM', 'BATHROOM','TOTAL_PRICE'];

    public function getListingVariants($id)
    {
        return ListingVariants::where('F_LISTING_NO', $id)->get();
    }
}
