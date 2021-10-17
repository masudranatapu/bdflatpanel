<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table 		= 'SA_TOKEN';
    protected $primaryKey   = 'PK_NO';
    // public $timestamps      = false;

}
