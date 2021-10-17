<?php

namespace App\Models;

use App\Traits\RepoResponse;
use Illuminate\Database\Eloquent\Model;

class LeadPrice extends Model
{
    use RepoResponse;
    protected $table = 'SS_LEAD_PRICE';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;


    public function getPaginatedList()
    {
        return LeadPrice::find(1);
    }

    public function postUpdate($request)
    {
        try{
            $lead_price                                      = LeadPrice::find(1);
            $lead_price->AGENT_PROP_VIEW_SALES_PRICE         = $request->apv_sale_price;
            $lead_price->AGENT_PROP_VIEW_RENT_PRICE          = $request->apv_rent_price;
            $lead_price->AGENT_PROP_VIEW_ROOMMATE_PRICE      = $request->apv_roommate_price;
            $lead_price->AGENT_COMM_SALES_PRICE              = $request->ac_sale_price;
            $lead_price->AGENT_COMM_RENT_PRICE               = $request->ac_rent_price;
            $lead_price->AGENT_COMM_ROOMMATE_PRICE           = $request->ac_roommate_price;
            $lead_price->LEAD_VIEW_SALES_PRICE               = $request->lv_sale_price;
            $lead_price->LEAD_VIEW_RENT_PRICE                = $request->lv_rent_price;
            $lead_price->LEAD_VIEW_ROOMMATE_PRICE            = $request->lv_roommate_price;
            $lead_price->update();

            return $this->formatResponse(true, 'Price Updated Successfully', 'admin.listing_price.list');
        }catch (\Exception $e){
//            dd($e);
            return $this->formatResponse(false, 'Something Wrong', 'admin.listing_price.list');
        }
    }
}
