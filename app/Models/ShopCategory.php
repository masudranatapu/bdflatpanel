<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class ShopCategory extends Model
{
    protected $table 		    = 'SLS_SHOP_CATEGORY';
    protected $primaryKey   = 'PK_NO';

    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    protected $fillable = ['NAME'];
   


}
