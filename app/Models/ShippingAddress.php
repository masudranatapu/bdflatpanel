<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table        = 'SC_SHIPING_ADDRESS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['NAME'];

    public function state() {
        return $this->hasOne('App\Models\State', 'F_COUNTRY_NO', 'PK_NO');
    }
}
