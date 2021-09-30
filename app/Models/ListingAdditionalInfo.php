<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingAdditionalInfo extends Model
{
    public $timestamps = false;
    protected $table = 'PRD_LISTING_ADDITIONAL_INFO';
    protected $primaryKey = 'PK_NO';
    protected $fillable = [
        'F_LISTING_NO',
        'FACING',
        'HANDOVER_DATE',
        'DESCRIPTION',
        'F_FEATURE_NOS',
        'FEATURES',
        'F_NEARBY_NOS',
        'NEARBY',
        'LOCATION_MAP',
        'VIDEO_CODE',
    ];

    public function getAdditionalInfo($id)
    {
        return ListingAdditionalInfo::where('F_LISTING_NO', $id)->first();
    }
}
