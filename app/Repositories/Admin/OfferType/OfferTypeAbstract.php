<?php
namespace App\Repositories\Admin\OfferType;

use DB;
use App\Models\OfferType;
use App\Traits\RepoResponse;

class OfferTypeAbstract implements OfferTypeInterface
{
    use RepoResponse;
    protected $offerType;

    public function __construct(OfferType $offerType)
    {
        $this->offerType = $offerType;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->offerType->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.offer_type.list', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $type                  = new OfferType();
            $type->NAME            = $request->name;
            $type->PUBLIC_NAME     = $request->public_name;
            $type->P_AMOUNT        = $request->p_amount;
            $type->P2_AMOUNT       = $request->p2_amount;
            $type->P_SS            = $request->p_ss;
            $type->P_SM            = $request->p_sm;
            $type->P_AIR           = $request->p_air;
            $type->P_SEA           = $request->p_sea;
            $type->X1_QTY          = $request->x1_qty;
            $type->X2_QTY          = $request->x2_qty;
            $type->ZA1             = $request->za1;
            $type->ZA2             = $request->za2;
            $type->ZA3             = $request->za3;
            $type->R_AMOUNT        = $request->r_amount;
            $type->R2_AMOUNT       = $request->r2_amount;
            $type->R_SS            = $request->r_ss;
            $type->R_SM            = $request->r_sm;
            $type->R_AIR           = $request->r_air;
            $type->R_SEA           = $request->r_sea;
            $type->Y1_QTY          = $request->y1_qty;
            $type->Y2_QTY          = $request->y2_qty;
            $type->ZB1             = $request->zb1;
            $type->ZB2             = $request->zb2;
            $type->ZB3             = $request->zb3;
            $type->save();


        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Offer type has been not created successfully !', 'admin.offer_type.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Offer type has been created successfully !', 'admin.offer_type.list');
    }

    public function findOrThrowException(int $id)
    {
        $data = $this->offerType->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.offer_type.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.offer_type.list', null);
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();
        try {
            $type                  = OfferType::find($PK_NO);
            $type->NAME            = $request->name;
            $type->PUBLIC_NAME     = $request->public_name;
            $type->P_AMOUNT        = $request->p_amount;
            $type->P2_AMOUNT       = $request->p2_amount;
            $type->P_SS            = $request->p_ss;
            $type->P_SM            = $request->p_sm;
            $type->P_AIR           = $request->p_air;
            $type->P_SEA           = $request->p_sea;
            $type->X1_QTY          = $request->x1_qty;
            $type->X2_QTY          = $request->x2_qty;
            $type->ZA1             = $request->za1;
            $type->ZA2             = $request->za2;
            $type->ZA3             = $request->za3;
            $type->R_AMOUNT        = $request->r_amount;
            $type->R2_AMOUNT       = $request->r2_amount;
            $type->R_SS            = $request->r_ss;
            $type->R_SM            = $request->r_sm;
            $type->R_AIR           = $request->r_air;
            $type->R_SEA           = $request->r_sea;
            $type->Y1_QTY          = $request->y1_qty;
            $type->Y2_QTY          = $request->y2_qty;
            $type->ZB1             = $request->zb1;
            $type->ZB2             = $request->zb2;
            $type->ZB3             = $request->zb3;
            $type->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Offer type has been not updated successfully !', 'admin.offer_type.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Offer type has been updated successfully !', 'admin.offer_type.list');
    }

    public function delete($PK_NO)
    {
        $accSource = AccountSource::where('PK_NO',$PK_NO)->first();
        $accSource->IS_ACTIVE = 0;
        if ($accSource->update()) {
            return $this->formatResponse(true, 'Successfully deleted Payment Source', 'admin.account.list');
        }
        return $this->formatResponse(false,'Unable to delete Payment Source','admin.account.list');
    }
}
