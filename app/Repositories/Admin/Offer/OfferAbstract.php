<?php
namespace App\Repositories\Admin\Offer;

use DB;

use App\Models\Offer;
use App\Traits\RepoResponse;

class OfferAbstract implements OfferInterface
{
    use RepoResponse;
    protected $offer;

    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->offer->orderBy('BUNDLE_NAME', 'ASC')->get();

        if ($data && count($data) > 0 ) {
            foreach ($data as $key => $value) {
                $now        = strtotime(date('Y-m-d'));
                $start      = strtotime($value->VALIDITY_FROM);
                $end        = strtotime($value->VALIDITY_TO);
                if($start <= $now && $now <= $end) {
                    $value->day_diff_with_current = "Yes";
                }else{
                    $value->day_diff_with_current = "No";
                }
            }
        }

        return $this->formatResponse(true, '', 'admin.offer.list', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $offer = new Offer();
            $offer->BUNDLE_NAME         = $request->name;
            $offer->BUNDLE_NAME_PUBLIC  = $request->public_name;
            $offer->COUPON_CODE         = $request->coupon_code;
            $offer->VALIDITY_FROM       = $request->validity_from ? date('Y-m-d',strtotime($request->validity_from)) : null;
            $offer->VALIDITY_TO         = $request->validity_to ? date('Y-m-d',strtotime($request->validity_to)) : null;
            $offer->F_A_LIST_NO         = $request->lista;
            $offer->F_B_LIST_NO         = $request->listb;
            $offer->F_BUNDLE_TYPE       = $request->offer_type;
            $offer->P_AMOUNT            = $request->p_amount;
            $offer->P2_AMOUNT           = $request->p2_amount;
            $offer->P_SS                = $request->p_ss;
            $offer->P_SM                = $request->p_sm;
            $offer->P_AIR               = $request->p_air;
            $offer->P_SEA               = $request->p_sea;
            $offer->X1_QTY              = $request->x1_qty;
            $offer->X2_QTY              = $request->x2_qty;
            $offer->ZA1                 = $request->za1;
            $offer->ZA2                 = $request->za2;
            $offer->ZA3                 = $request->za3;
            $offer->R_AMOUNT            = $request->r_amount;
            $offer->R2_AMOUNT           = $request->r2_amount;
            $offer->R_SS                = $request->r_ss;
            $offer->R_SM                = $request->r_sm;
            $offer->R_AIR               = $request->r_air;
            $offer->R_SEA               = $request->r_sea;
            $offer->Y1_QTY              = $request->y1_qty;
            $offer->Y2_QTY              = $request->y2_qty;
            $offer->ZB1                 = $request->zb1;
            $offer->ZB2                 = $request->zb2;
            $offer->ZB3                 = $request->zb3;
            $offer->save() ;

            if($request->file('image')){
                $offer =  Offer::find($offer->PK_NO);
                $image = $request->file('image');
                $file_name = 'bundle_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                $offer->IMAGE     = '/media/images/bundle/'.$offer->PK_NO.'/'.$file_name;
                $offer->update();
                $image->move(public_path().'/media/images/bundle/'.$offer->PK_NO.'/', $file_name);
            }

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer not created successfully !', 'admin.offer.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Offer has been created successfully !', 'admin.offer.list');
    }

    public function postUpdate($request, $pk_no)
    {
        DB::beginTransaction();
        try {
            $offer                      = Offer::find($pk_no);
            $offer->BUNDLE_NAME         = $request->name;
            $offer->BUNDLE_NAME_PUBLIC  = $request->public_name;
            $offer->COUPON_CODE         = $request->coupon_code;
            $offer->VALIDITY_FROM       = date('Y-m-d',strtotime($request->validity_from));
            $offer->VALIDITY_TO         = date('Y-m-d',strtotime($request->validity_to));
            $offer->F_A_LIST_NO         = $request->lista;
            $offer->F_B_LIST_NO         = $request->listb;
            $offer->F_BUNDLE_TYPE       = $request->offer_type;
            $offer->P_AMOUNT            = $request->p_amount;
            $offer->P2_AMOUNT           = $request->p2_amount;
            $offer->P_SS                = $request->p_ss;
            $offer->P_SM                = $request->p_sm;
            $offer->P_AIR               = $request->p_air;
            $offer->P_SEA               = $request->p_sea;
            $offer->X1_QTY              = $request->x1_qty;
            $offer->X2_QTY              = $request->x2_qty;
            $offer->ZA1                 = $request->za1;
            $offer->ZA2                 = $request->za2;
            $offer->ZA3                 = $request->za3;
            $offer->R_AMOUNT            = $request->r_amount;
            $offer->R2_AMOUNT           = $request->r2_amount;
            $offer->R_SS                = $request->r_ss;
            $offer->R_SM                = $request->r_sm;
            $offer->R_AIR               = $request->r_air;
            $offer->R_SEA               = $request->r_sea;
            $offer->Y1_QTY              = $request->y1_qty;
            $offer->Y2_QTY              = $request->y2_qty;
            $offer->ZB1                 = $request->zb1;
            $offer->ZB2                 = $request->zb2;
            $offer->ZB3                 = $request->zb3;
            if($request->file('image')){
                $image = $request->file('image');
                $file_name = 'bundle_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
                // dd($file_name);
                $offer->IMAGE     = '/media/images/bundle/'.$pk_no.'/'.$file_name;
                $image->move(public_path().'/media/images/bundle/'.$pk_no.'/', $file_name);
            }

            $offer->update() ;

        } catch (\Exception $e) {

        DB::rollback();
        return $this->formatResponse(false, 'Offer not updated successfully !', 'admin.offer.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Offer has been updated successfully !', 'admin.offer.list');

    }

    public function delete($pk_no)
    {
        $offer = Offer::where('PK_NO',$pk_no)->delete();
        if ($offer) {
            return $this->formatResponse(true, 'Successfully deleted offer', 'admin.offer.list');
        }else{
            return $this->formatResponse(false,'Unable to delete offer','admin.offer.list');
        }
    }
}
