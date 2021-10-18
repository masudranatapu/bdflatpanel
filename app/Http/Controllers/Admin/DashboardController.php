<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    public function __construct()
    {
    }

    public function getIndex()
    {
        $data['total_property'] = DB::table('PRD_LISTINGS')->count();
        $data['total_property_published'] =  DB::table('PRD_LISTINGS')->where('STATUS',10)->count();;
        $data['total_owner'] = DB::table('WEB_USER')->whereIn('USER_TYPE',[2,3,4])->count();
        $data['total_seeker'] = DB::table('WEB_USER')->where('USER_TYPE',1)->count();

        return view('admin.dashboard.home', compact('data'));
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
