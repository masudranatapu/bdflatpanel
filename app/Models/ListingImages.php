<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingImages extends Model
{
    public $timestamps = false;
    protected $table = 'PRD_LISTING_IMAGES';
    protected $primaryKey = 'PK_NO';
    protected $fillable = ['F_LISTING_NO', 'IMAGE_PATH', 'IMAGE','THUMB_PATH', 'THUMB', 'IS_DEFAULT'];

    public function getListingImages($id)
    {
        return ListingImages::where('F_LISTING_NO', $id)->get();
    }

}
