<?php
namespace App\Repositories\Admin\OfferPrimary;

use App\Models\OfferPrimary;
use App\Models\OfferPrimaryDetails;
use App\Models\ProductVariant;
use App\Traits\RepoResponse;
use DB;

class OfferPrimaryAbstract implements OfferPrimaryInterface
{
    use RepoResponse;

    protected $offerPrimary;

    public function __construct(OfferPrimary $offerPrimary)
    {
        $this->offerPrimary = $offerPrimary;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->offerPrimary->orderBy('PRIMARY_SET_NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, 'Data Found', 'admin.offer_primary.list', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $offer                      = new OfferPrimary();
            $offer->PRIMARY_SET_NAME    = $request->name;
            $offer->COMMENTS            = $request->comment;
            $offer->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer primary list not created successfully !', 'admin.offer_primary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Offer primary list has been created successfully !', 'admin.offer_primary.list');
    }

    public function findOrThrowException(int $id)
    {
        $data = $this->offerPrimary->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.offer_primary.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.offer_primary.list', null);
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();

        try {
            $offer                      = OfferPrimary::find($PK_NO);
            $offer->PRIMARY_SET_NAME    = $request->name;
            $offer->COMMENTS            = $request->comment;
            $offer->update();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer primary list not updated successfully !', 'admin.offer_primary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Offer primary list has been updated successfully !', 'admin.offer_primary.list');
    }

    public function delete($PK_NO)
    {
        // $accSource = AccountSource::where('PK_NO',$PK_NO)->first();
        // $accSource->IS_ACTIVE = 0;
        // if ($accSource->update()) {
        //     return $this->formatResponse(true, 'Successfully deleted Payment Source', 'admin.account.list');
        // }
        // return $this->formatResponse(false,'Unable to delete Payment Source','admin.account.list');
    }
    public function getVariantList($request){

            $products = ProductVariant::whereIn('PK_NO', $request->product_no)->get();
            // return  $products;
            $data = view('admin.offer_primary._product_list')->withRows($products)->render();
            return $this->formatResponse(true, 'Data found', 'admin.offer_primary.list', $data);


    }

    public function postStoreProduct($request)
    {
        DB::beginTransaction();

        try {

            if(isset($request->variant_no ) && (count($request->variant_no) > 0 )){
                foreach($request->variant_no as $key => $variant_no){
                    $offer                                  = new OfferPrimaryDetails();
                    $offer->F_SLS_BUNDLE_PRIMARY_SET_NO     = $request->master_pk_no;
                    $offer->F_PRD_VARIANT_NO                = $variant_no;
                    $offer->PRD_VARIANT_NAME                = $request->variant_name[$key];
                    $offer->SKUID                           = $request->variant_skuid[$key];
                    $offer->save();
                }
            }



        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Primary list product not created successfully !', 'admin.offer_primary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Primary list product has been created successfully !', 'admin.offer_primary.list');
    }

    public function getDeleteProduct($id)
    {
        DB::beginTransaction();

        try {

            OfferPrimaryDetails::where('PK_NO',$id)->delete($id);

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Primary list product not deleted successfully !', 'admin.offer_primary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Primary list product has been deleted successfully !', 'admin.offer_primary.list');
    }






}
