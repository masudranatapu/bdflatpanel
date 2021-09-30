<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class RefundRequest extends Model
{
    protected $table = 'ACC_CUSTOMER_REFUND';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'MODIFIED_AT';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->CREATED_BY = Auth::id();
        });

        static::updating(function ($model) {
            $model->MODIFIED_BY = Auth::id();
        });
    }

    public function refundType(): string
    {
        $type = $this->getOriginal('REFUND_TYPE');
        switch ($type) {
            case 1:
                $txt = 'Listing Lead Payment';
                break;
            case 2:
                $txt = 'LEAD PAYMENT';
                break;
            default:
                $txt = '';
        }
        return $txt;
    }

}
