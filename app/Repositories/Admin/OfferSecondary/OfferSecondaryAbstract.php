<?php
namespace App\Repositories\Admin\OfferSecondary;

use App\Models\OfferSecondary;
use App\Models\OfferSecondaryDetails;
use App\Models\ProductVariant;
use App\Traits\RepoResponse;
use DB;

class OfferSecondaryAbstract implements OfferSecondaryInterface
{
    use RepoResponse;

    protected $offerSecondary;

    public function __construct(OfferSecondary $offerSecondary)
    {
        $this->offerSecondary = $offerSecondary;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->offerSecondary->orderBy('SECONDARY_SET_NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, 'Data Found', 'admin.offer_secondary.list', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $offer                      = new OfferSecondary();
            $offer->SECONDARY_SET_NAME    = $request->name;
            $offer->COMMENTS            = $request->comment;
            $offer->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer secondary list not created successfully !', 'admin.offer_secondary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Offer secondary list has been created successfully !', 'admin.offer_secondary.list');
    }

    public function findOrThrowException(int $id)
    {
        $data = $this->offerSecondary->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.offer_secondary.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.offer_secondary.list', null);
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();

        try {
            $offer                      = OfferSecondary::find($PK_NO);
            $offer->SECONDARY_SET_NAME  = $request->name;
            $offer->COMMENTS            = $request->comment;
            $offer->update();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer secondary list not updated successfully !', 'admin.offer_secondary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Offer secondary list has been updated successfully !', 'admin.offer_secondary.list');
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
            $data = view('admin.offer_secondary._product_list')->withRows($products)->render();
            return $this->formatResponse(true, 'Data found', 'admin.offer_secondary.list', $data);


    }

    public function postStoreProduct($request)
    {
        DB::beginTransaction();

        try {

            if(isset($request->variant_no ) && (count($request->variant_no) > 0 )){
                foreach($request->variant_no as $key => $variant_no){
                    $offer                                  = new OfferSecondaryDetails();
                    $offer->F_SLS_BUNDLE_SECONDARY_SET_NO   = $request->master_pk_no;
                    $offer->F_PRD_VARIANT_NO                = $variant_no;
                    $offer->PRD_VARIANT_NAME                = $request->variant_name[$key];
                    $offer->SKUID                           = $request->variant_skuid[$key];
                    $offer->save();
                }
            }



        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Secondary list product not created successfully !', 'admin.offer_secondary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Secondary list product has been created successfully !', 'admin.offer_secondary.list');
    }

    public function getDeleteProduct($id)
    {
        DB::beginTransaction();

        try {

            OfferSecondaryDetails::where('PK_NO',$id)->delete($id);

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Secondary list product not deleted successfully !', 'admin.offer_secondary.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Secondary list product has been deleted successfully !', 'admin.offer_secondary.list');
    }






}
