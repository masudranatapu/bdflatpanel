<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\RepoResponse;
use DB;

class PaymentMethod extends Model
{
    use RepoResponse;

    protected $table = 'ACC_PAYMENT_METHODS';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';


    public function postStore($request)
    {
//        dd($request->all());
        DB::beginTransaction();
        try {
            $about              = new PaymentMethod();
            $about->NAME        = $request->payment_method_name;
            $about->IS_ACTIVE   = $request->status;
            $about->save();

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create Payment Method !', 'web.payment_method');
        }
        DB::commit();

        return $this->formatResponse(true, 'Payment Method been created successfully !', 'web.payment_method');
    }

    public function postUpdate($request, $id)
    {
//        dd($request->all());
        DB::beginTransaction();
        try {
            $about              = PaymentMethod::where('PK_NO',$id)->first();
            $about->NAME        = $request->payment_method_name;
            $about->IS_ACTIVE   = $request->status;
            $about->update();

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Payment Method !', 'web.payment_method');
        }
        DB::commit();

        return $this->formatResponse(true, 'Payment Method been updated successfully !', 'web.payment_method');
    }

}
