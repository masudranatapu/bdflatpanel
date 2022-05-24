<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NearBy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NearByController extends Controller
{
    public function getIndex(){
        $nearby_area = NearBy::orderBy('PK_NO','desc')->get();
        return view('admin.nearby.index',compact('nearby_area'));
    }
    public function getCreate(){
        return view('admin.nearby.create');
    }
    public function postStore(Request $request){
        $this->status = false;
        $this->msg = 'Feature not added!';

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->title);
            $check = NearBy::where('URL_SLUG', '=', $slug)->first();
            if ($check) {
                $slug .= '-' . (NearBy::max('PK_NO') + 1);
            }

            $feature = new NearBy();
            $feature->TITLE = $request->title;
            $feature->ORDER_ID = $request->order_id;
            $feature->IS_ACTIVE = $request->status;
            $feature->URL_SLUG = $slug;
            if ($request->hasFile('icon')) {
                $image = $request->file('icon');
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/listings/features/';
                $image->move($image_path, $image_name);
                $feature->ICON = $image_path . $image_name;
            }
            $feature->save();

            $this->status = true;
            $this->msg = 'NearBy added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return redirect()->route('admin.nearby.area')->with('flashMessageSuccess', $this->msg);
    }
    public function getEdit($id)
    {
        $data = NearBy::find($id);
        return view('admin.nearby.edit',compact('data'));
    }
    public function postUpdate(Request $request,$id)
    {
        $this->status = false;
        $this->msg = 'Feature not updated!';

        DB::beginTransaction();
        try {
            $feature = NearBy::find($id);
            $feature->TITLE = $request->title;
            $feature->ORDER_ID = $request->order_id;
            $feature->IS_ACTIVE = $request->status;
            if ($request->hasFile('icon')) {
                $image = $request->file('icon');
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/listings/features/';
                $image->move($image_path, $image_name);
                $feature->ICON = $image_path . $image_name;
            }
            $feature->save();

            $this->status = true;
            $this->msg = 'NearBy updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return redirect()->route('admin.nearby.area')->with('flashMessageSuccess', $this->msg);
    }
}
