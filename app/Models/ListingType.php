<?php

namespace App\Models;

use App\Traits\RepoResponse;
use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    protected $table = 'PRD_LISTING_TYPE';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;

    public function listingPrice()
    {
        return $this->hasOne('App\Models\ListingPrice', 'F_LISTING_TYPE_NO');
    }

    public function getPaginatedList()
    {
        return ListingType::get();
    }

}
