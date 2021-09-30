<?php
namespace App\Repositories\Admin\Box;

use DB;
use App\Models\Box;
use App\Models\Stock;
use App\Models\BoxType;
use App\Models\Shipment;
use App\Traits\RepoResponse;

class BoxAbstract implements BoxInterface
{
    use RepoResponse;

    protected $shipment;
    protected $box_type;
    protected $stock;

    public function __construct(Shipment $shipment, Stock $stock, BoxType $box_type)
    {
        $this->shipment = $shipment;
        $this->box_type = $box_type;
        $this->stock    = $stock;
    }

    public function getBox($id)
    {
        $box = $this->stock->select('*','SKUID as sku_id'
        ,DB::raw('IFNULL(count(SKUID),0) as total')
        ,DB::raw('IFNULL(AVG(PRODUCT_PURCHASE_PRICE),0) as PRODUCT_PURCHASE_PRICE')
        ,DB::raw('IFNULL(AVG(REGULAR_PRICE),0) as REGULAR_PRICE')
        ,DB::raw('GROUP_CONCAT(DISTINCT(PRC_IN_IMAGE_PATH)) as PRC_IN_IMAGE_PATH')
        ,DB::raw('(select ifnull(count(PK_NO),0) from INV_STOCK where F_BOX_NO='.$id.' and PRODUCT_STATUS = 60 and SKUID = sku_id ) as unboxed')
        )
        ->where('F_BOX_NO', $id)
        ->groupBy('SKUID')
        ->get();
        return $this->formatResponse(true, '', ' ', $box);
    }

    public function getBoxTypeAdd($id)
    {
        $data = [];
        if ($id > 0) {
            $data = $this->box_type->where('PK_NO', $id)->first();
        }
        return $this->formatResponse(true, '', ' ', $data);
    }

    public function getBoxTypeList()
    {
        $box_type = $this->box_type->where('IS_ACTIVE', 1)->get();
        return $this->formatResponse(true, '', ' ', $box_type);
    }

    public function postUpdate($request)
    {
        $box_type = 'SEA';

        $dup_box = Box::where('BOX_NO',$request->box_label)->count();
        if ($dup_box > 0) {
            return $this->formatResponse(false, 'Duplicate Box Label !', ' ', null);
        }
        $box_type = substr($request->box_label, 0, 1);
        if ($box_type == 1) {
            $box_type = 'AIR';
        }
        DB::beginTransaction();
        try {
            Box::where('PK_NO',$request->id)->update(['BOX_NO'=>$request->box_label]);
            Stock::where('F_BOX_NO',$request->id)->update(['BOX_BARCODE'=>$request->box_label,'BOX_TYPE'=>$box_type]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(true, 'Please Try Again !', ' ', null);
        }
        DB::commit();
        return $this->formatResponse(false, 'Box Label Updated !', ' ', null);

    }

    public function postBoxTypeStore($request)
    {
        DB::beginTransaction();
        try {
            if ($request->box_pk > 0) {
                $box_type       = BoxType::find($request->box_pk);
            }else{
                $box_type           = new BoxType();
            }
            $box_type->TYPE     = $request->type;
            $box_type->WIDTH_CM = $request->width;
            $box_type->LENGTH_CM= $request->length;
            $box_type->HEIGHT_CM= $request->height;

            $box_type->save();
            $type_list = $this->box_type->where('IS_ACTIVE', 1)->get();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Please Try Again !', ' ', $type_list);
        }
        DB::commit();
        return $this->formatResponse(true, 'Action was successfull !', '', $type_list);
    }

    public function getBoxTypeDelete($id)
    {
        DB::beginTransaction();
        try {
            $this->box_type->where('PK_NO',$id)->delete();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Please Try Again !', ' ');
        }
        DB::commit();
        return $this->formatResponse(true, 'Action was successfull !', '');
    }
}
