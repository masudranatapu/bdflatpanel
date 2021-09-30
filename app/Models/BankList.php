<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BankList extends Model
{
    protected $table 		= 'ACC_BANK_LIST';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = ['BANK_NAME'];

}
