<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingView extends Model
{
    protected $table        = 'PRD_LISTING_VIEW';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    protected $fillable     = ['F_LISTING_NO', 'PROPERTY_SIZE', 'BEDROOM', 'BATHROOM','TOTAL_PRICE'];




}


