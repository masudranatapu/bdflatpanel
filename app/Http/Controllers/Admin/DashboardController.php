<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use App\Models\Box;
use App\Models\City;
use App\Models\Brand;
use App\Models\Order;
use App\Models\State;
use App\Models\Stock;
use App\Models\PoCode;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\Shipment;
use App\Models\NotifySms;
use App\Models\Warehouse;
use App\Models\AccBankTxn;
use App\Models\ProductModel;
use App\Models\WarehouseZone;
use App\Models\BookingDetails;
use App\Models\PaymentBankAcc;
use App\Models\ProductVariant;
use App\Models\CustomerAddress;
use App\Models\DispatchDetails;
use App\Models\PaymentCustomer;
use App\Models\ShippingAddress;
use App\Models\SmsNotification;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Models\Agent;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function __construct()
    {
    }

    public function getIndex() {

        return view('admin.dashboard.home');
    }

    public function homepage() {
        return view('admin.dashboard.home');
    }

    public function postDashboardNote(Request $request)
    {
        DB::beginTransaction();

        try {
            DB::table('SS_STICKY_NOTE')->update(['NOTE'=>$request->note]);
        } catch (\Exception $e) {
            DB::rollback();
            return 0;
        }
        DB::commit();
        return 1;
    }
}
