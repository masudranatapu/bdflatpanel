<?php
namespace App\Repositories\Admin\Currency;

use App\Models\Currency;
use App\Traits\RepoResponse;
use DB;

class CurrencyAbstract implements CurrencyInterface
{
    use RepoResponse;

    protected $currency;

    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->currency->orderBy('PK_NO', 'ASC')->get();

        return $this->formatResponse(true, '', 'admin.currency.index', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $currency                   = new Currency();
            $currency->CODE             = $request->code;
            $currency->NAME             = $request->name;
            $currency->EXCHANGE_RATE_GB = $request->rate;
            $currency->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.currency.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Currency has been created successfully !', 'admin.currency.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        if ($request->type == 'name') {
            $column = 'NAME';
        }else if ($request->type == 'code') {
            $column = 'CODE';
        }else{
            $column = 'EXCHANGE_RATE_GB';
        }
        try {
            $this->currency->where('PK_NO', $PK_NO)->update([ $column => $request->currency_value ]);
        } catch (\Exeption $th) {
            return $this->formatResponse(true, $th->getMessage(), 'admin.currency.list');
        }
        return $this->formatResponse(true, 'Curency has been Updated successfully', 'admin.currency.list');
    }

    public function delete($PK_NO)
    {
        $accSource = $this->currency->where('PK_NO',$PK_NO)->first();

        if ($accSource->delete()) {
            return $this->formatResponse(true, 'Successfully deleted Currency', 'admin.currency.list');
        }
        return $this->formatResponse(false,'Unable to delete Currency','admin.currency.list');
    }
}
