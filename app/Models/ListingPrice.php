<?php

namespace App\Models;

use App\Traits\RepoResponse;
use Illuminate\Database\Eloquent\Model;

class ListingPrice extends Model
{
    use RepoResponse;
    protected $table = 'SS_LISTING_PRICE';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;

    public function postUpdate($request)
    {
//        dd($request->all());
        try{
            $listing_name1                                  = ListingType::where('PK_NO',1)->first();
            $listing_name1->NAME                            = $request->gl_sale_name0;
            $listing_name1->DURATION                        = $request->gl_duration0;
            $listing_name1->update();

            $listing_name2                                  = ListingType::where('PK_NO',2)->first();
            $listing_name2->NAME                            = $request->gl_sale_name1;
            $listing_name2->DURATION                        = $request->gl_duration1;
            $listing_name2->update();

            $listing_name3                                  = ListingType::where('PK_NO',3)->first();
            $listing_name3->NAME                            = $request->gl_sale_name2;
            $listing_name3->DURATION                        = $request->gl_duration2;
            $listing_name3->update();

            $listing_name4                                  = ListingType::where('PK_NO',4)->first();
            $listing_name4->NAME                            = $request->gl_sale_name3;
            $listing_name4->DURATION                        = $request->gl_duration3;
            $listing_name4->update();



            $listing_price1                                 = ListingPrice::where('F_LISTING_TYPE_NO',1)->first();
            $listing_price1->SELL_PRICE                    = $request->gl_sale_price0;
            $listing_price1->RENT_PRICE                     = $request->gl_rent_price0;
            $listing_price1->ROOMMAT_PRICE                  = $request->gl_roommate_price0;
            $listing_price1->update();

            $listing_price2                                 = ListingPrice::where('F_LISTING_TYPE_NO',2)->first();
            $listing_price2->SELL_PRICE                    = $request->gl_sale_price1;
            $listing_price2->RENT_PRICE                     = $request->gl_rent_price1;
            $listing_price2->ROOMMAT_PRICE                  = $request->gl_roommate_price1;
            $listing_price2->update();

            $listing_price3                                 = ListingPrice::where('F_LISTING_TYPE_NO',3)->first();
            $listing_price3->SELL_PRICE                    = $request->gl_sale_price2;
            $listing_price3->RENT_PRICE                     = $request->gl_rent_price2;
            $listing_price3->ROOMMAT_PRICE                  = $request->gl_roommate_price2;
            $listing_price3->update();

            $listing_price4                                 = ListingPrice::where('F_LISTING_TYPE_NO',4)->first();
            $listing_price4->SELL_PRICE                    = $request->gl_sale_price3;
            $listing_price4->RENT_PRICE                     = $request->gl_rent_price3;
            $listing_price4->ROOMMAT_PRICE                  = $request->gl_roommate_price3;
            $listing_price4->update();

            return $this->formatResponse(true, 'Price Updated Successfully', 'admin.listing_price.list');
        }catch (\Exception $e){
//            dd($e);
            return $this->formatResponse(false, 'Something Wrong', 'admin.listing_price.list');
        }
    }
}
