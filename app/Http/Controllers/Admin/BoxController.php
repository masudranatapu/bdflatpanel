<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Box;
use App\Models\Stock;
use App\Models\Shipment;
use App\Models\Warehouse;
use App\Models\Shipmentbox;
use App\Traits\RepoResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Box\BoxInterface;

class BoxController extends BaseController
{
    use RepoResponse;

    private $box;
    private $warehouse;
    private $shipmodel;
    private $stock;
    function __construct(BoxInterface $box, Box $box_model, Shipment $shipmodel, Shipmentbox $shipmentbox, Stock $stock)
    {
        $this->box           = $box;
        $this->box_model     = $box_model;
        $this->stock         = $stock;
    }

    public function getIndex(Request $request)
    {
        return view('admin.box.index');
    }

    public function getBox($id)
    {
        $box = $this->box->getBox($id);
        return view('admin.box.boxView')->withBoxs($box->data);
    }

    public function getNotBoxed(Request $request)
    {
        return view('admin.box.not_boxed');
    }

    public function getNotBox($id)
    {
        $sku_id = $this->stock::select('SKUID')->where('PK_NO', $id)->first();
        $box = $this->stock->select('*',DB::raw('GROUP_CONCAT(DISTINCT(PRC_IN_IMAGE_PATH)) as invoice_list'))->where('SKUID', $sku_id->SKUID)->whereRaw('F_BOX_NO IS NULL')->get();

        return view('admin.box.not_boxdView')->withItems($box);
    }

    public function putBoxLabelUpdate(Request $request)
    {
        $this->resp = $this->box->postUpdate($request);
        return view('admin.box.index')->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postBoxTypeStore(Request $request)
    {
        $this->resp = $this->box->postBoxTypeStore($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        // return view('admin.boxType.index')->with($this->resp->redirect_class, $this->resp->msg)->withRows($this->resp->data);
    }

    public function getBoxTypeList()
    {
        $this->resp = $this->box->getBoxTypeList();

        return view('admin.boxType.index')->withRows($this->resp->data);
    }

    public function getBoxTypeAdd($id = null)
    {
        $this->resp = $this->box->getBoxTypeAdd($id);

        return view('admin.boxType.create')->withData($this->resp->data);
    }

    public function getBoxTypeDelete($id)
    {
        $this->resp = $this->box->getBoxTypeDelete($id);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
?>
