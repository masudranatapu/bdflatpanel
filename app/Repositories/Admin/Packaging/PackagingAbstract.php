<?php
namespace App\Repositories\Admin\Packaging;

use PDF;
use DB;
use Carbon\Carbon;
use App\Models\Box;
use App\Models\Stock;
use App\Models\Packaging;
use App\Models\Packing;
use App\Models\Hscode;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Shipment;
use App\Models\ProductVariant;
use App\Traits\RepoResponse;

class PackagingAbstract implements PackagingInterface
{
    use RepoResponse;

    protected $packaging;
    protected $packing;
    protected $product_variant;
    protected $category;
    protected $hscode;
    protected $subcategory;
    protected $shipment;

    public function __construct(Packaging $packaging, Packing $packing, ProductVariant $product_variant, Category $category, Hscode $hscode, SubCategory $subcategory, Shipment $shipment)
    {
        $this->packaging 	          = $packaging;
        $this->packing 		          = $packing;
        $this->product_variant        = $product_variant;
        $this->category               = $category;
        $this->hscode                 = $hscode;
        $this->subcategory            = $subcategory;
        $this->shipment               = $shipment;
    }

    public function findOrThrowException($shipment_no)
    {
        $data['data'] 		    = $this->packaging->where('F_SHIPMENT_NO',$shipment_no)->orderBy('BOX_SERIAL_NO','ASC')->get();
        $data['packing_list']   = $this->packing->where('F_SHIPMENT_NO',$shipment_no)->get();
        $data['category']       = $this->category->getCategorCombo();
        $data['box_combo']      = $this->getBoxes($shipment_no);
        $data['shipment_info']  = $this->shipment->find($shipment_no);
        return $this->formatResponse(true, '', 'admin.packaging.edit', $data);
    }

    public function postPackingItemStore($request)
    {


        DB::beginTransaction();

        try {

        $variant        = $this->product_variant->where('PK_NO',$request->product_variant_no)->first();
        $subcategory    = $this->subcategory->where('PK_NO',$request->sub_category)->first();
        // $shipment       = $this->packaging->where('F_SHIPMENT_NO',$request->shipment_no)->first();

        $box_info = Packaging::where('F_SHIPMENT_NO', $request->shipment_no)->where('F_BOX_NO',$request->box_no)->first();
        if(empty($box_info)){

            $box_info = Packaging::where('F_SHIPMENT_NO', $request->shipment_no)->where('PK_NO',$request->box_no)->first();
        }


        $item                   = new Packing();
        $item->F_SHIPMENT_NO    = $request->shipment_no;
        $item->SHIPMENT_NAME    = $box_info->SHIPMENT_NAME;
        $item->BOX_SERIAL_NO    = $box_info->BOX_SERIAL_NO;
        $item->F_BOX_NO         = $box_info->F_BOX_NO ;
        $item->PRD_VARINAT_NO   = $request->product_variant_no ?? null;
        $item->HS_CODE          = $request->hs_code;
        $item->SKU_ID           = $variant->COMPOSITE_CODE ?? null;
        $item->IG_CODE          = $variant->MRK_ID_COMPOSITE_CODE ?? null;
        $item->SUBCATEGORY_NAME = $subcategory->NAME;
        $item->PRC_INV_NAME     = $request->description;
        $item->QTY              = $request->qty;
        $item->UNIT_PRICE       = $request->price;
        $item->TOTAL_PRICE      = $request->qty*$request->price;

        $item->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Unable to add new item in the box !', 'admin.packaging.edit');
        }

        DB::commit();

        return $this->formatResponse(true, 'Item has been added successfully in the box !', 'admin.packaging.edit');
    }

    public function getBoxes($shipment_no)
    {
        $result = $this->packaging->where('F_SHIPMENT_NO',$shipment_no)->select('PK_NO','BOX_SERIAL_NO','F_BOX_NO')->orderBy('BOX_SERIAL_NO','ASC')->get();


        //dd($response);
        /*
        if(!empty($result)){
            foreach ($result as $key => $value) {
                $result[1] = 'BOX NO '.$value;

            }
        }
        */
        return $result;
    }


    public function postPackingItemDelete($request)
    {
        DB::beginTransaction();

        try {
                $items = $request->records;
                if ($items) {
                    foreach ($items as $key => $value) {
                        $this->packing->where(['F_SHIPMENT_NO' => $request->shipment_no, 'PK_NO' => $value])->delete();
                    }
                }

            } catch (\Exception $e) {


                DB::rollback();
                return $this->formatResponse(false, 'Unable to delete item from the box !', 'admin.packaging.edit');
            }

            DB::commit();

            return $this->formatResponse(true, 'Box Item deleted successfully from the box !', 'admin.packaging.edit');
    }

    public function gePackagingListInfo($key, $type)
    {

        $query   =  $this->product_variant
                        ->select('PRD_VARIANT_SETUP.*','PRD_CATEGORY.NAME as CATEGORY_NAME','PRD_CATEGORY.PK_NO as CATEGORY_PK_NO', 'PRD_SUB_CATEGORY.NAME as SUB_CATEGORY_NAME', 'PRD_SUB_CATEGORY.PK_NO as SUB_CATEGORY_PK_NO' )
                        ->leftJoin('PRD_MASTER_SETUP', 'PRD_MASTER_SETUP.PK_NO', '=', 'PRD_VARIANT_SETUP.F_PRD_MASTER_SETUP_NO')
                        ->leftJoin('PRD_SUB_CATEGORY', 'PRD_SUB_CATEGORY.PK_NO', '=', 'PRD_MASTER_SETUP.F_PRD_SUB_CATEGORY_ID')
                        ->leftJoin('PRD_CATEGORY', 'PRD_CATEGORY.PK_NO', '=', 'PRD_SUB_CATEGORY.F_PRD_CATEGORY_NO');
                        // ->query();


        $query->when($type == 'igcode', function ($q) use ($key) {
            return $q->where('PRD_VARIANT_SETUP.MRK_ID_COMPOSITE_CODE', $key);
        });
        $query->when($type == 'barcode', function ($q) use ($key) {
            return $q->where('PRD_VARIANT_SETUP.BARCODE', $key);
        });
        $product = $query->first();
        if (!empty($product)) {
            $data['hs_code'] = $this->hscode->where('F_PRD_SUB_CATEGORY_NO', $product->SUB_CATEGORY_PK_NO)->get();
            $data['product'] = $product;
        }else{
            $data['hs_code'] = array();
            $data['product'] = array();
        }

        return $this->formatResponse(true, '', 'get-packaginglist-info', $data);
    }


    public function postPackingItemUpdate($request)
    {
        DB::beginTransaction();
            try {
                $box = Packaging::find($request->shipment_no);
                $box->WIDTH_CM  = $request->box_width ;
                $box->LENGTH_CM = $request->box_length ;
                $box->HEIGHT_CM = $request->box_height ;
                $box->WEIGHT_KG = $request->box_weight ;
                $box->INVOICE_DETAILS  = $request->invoice_details;
                $box->update();


                if ($request->box_item_id) {
                    foreach ($request->box_item_id as $key => $value) {
                        $item                   = Packing::find($value);
                        $item->PRC_INV_NAME     = $request->prc_inv_name[$key];
                        $item->SUBCATEGORY_NAME = $request->subcategory_name[$key];
                        $item->QTY              = $request->box_qnty[$key];
                        $item->UNIT_PRICE       = $request->box_price[$key];

                        $item->TOTAL_PRICE      = $request->box_qnty[$key]*$request->box_price[$key];
                        $item->update();
                    }
                }

            } catch (\Exception $e) {
// dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update item for the box !', 'admin.packaging.edit');
        }

        DB::commit();
        return $this->formatResponse(true, 'Item updated successfully for the box !', 'admin.packaging.edit');
    }


    public function postPackagingboxStore($request)
    {
        DB::beginTransaction();
        try {
            $pack = new Packaging();
            $shipment       = $this->packaging->where('F_SHIPMENT_NO',$request->shippment_no)->first();

            $pack->F_SHIPMENT_NO  = $request->shippment_no ;
            $pack->SHIPMENT_NAME  = $shipment->SHIPMENT_NAME ;
            $pack->BOX_SERIAL_NO  = $request->box_serial_no ;
            $pack->WIDTH_CM  = $request->box_width ;
            $pack->LENGTH_CM = $request->box_length ;
            $pack->HEIGHT_CM = $request->box_height ;
            $pack->WEIGHT_KG = 30 ;


            $pack->save();





        } catch (\Exception $e) {
// dd($e);
        DB::rollback();
        return $this->formatResponse(false, 'Box not created successfully !', 'admin.packaging.edit');
    }

    DB::commit();
    return $this->formatResponse(true, 'Box created successfully !', 'admin.packaging.edit');

    }


    public function getPackaginglistPrint($shipment_no)
    {

        $data['data']           = $this->packaging->where('F_SHIPMENT_NO',$shipment_no)->orderBy('BOX_SERIAL_NO','ASC')->get();
        $data['packing_list']   = $this->packing->where('F_SHIPMENT_NO',$shipment_no)->get();
        $data['category']       = $this->category->getCategorCombo();
        $data['box_combo']      = $this->getBoxes($shipment_no);


        $pdf = PDF::loadView('admin.packaging.packing_list_pdf', compact('data'));

        // dd($pdf);


        $fileName = 'packing-list-'.$shipment_no.'.pdf';


        // return view('admin.packing.packing_list_pdf', compact('data'));

        // $curtime = date('d-m-Y H:i:s');
        $pdf->download($fileName);


    }




}
