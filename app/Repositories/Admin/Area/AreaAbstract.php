<?php


namespace App\Repositories\Admin\Area;


use App\Models\Area;
use App\Models\City;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AreaAbstract implements AreaInterface
{
    use RepoResponse;

    protected $status;
    protected $msg;

    public function getAreas($limit = 2000): object
    {
        $areas = [];
        $area = Area::orderBy('ORDER_ID', 'DESC')->where('IS_PARENT',1)->get();

        if($area){
            $i = 0;
            foreach ($area as $value) {
                $areas[$i]['PK_NO'] = $value->PK_NO;
                $areas[$i]['ORDER_ID'] = $value->ORDER_ID;
                $areas[$i]['AREA_NAME'] = $value->AREA_NAME;
                $areas[$i]['CITY_NAME'] = $value->CITY_NAME;
                $areas[$i]['LAT'] = $value->LAT;
                $areas[$i]['LON'] = $value->LON;
                $areas[$i]['SUB_AREA_NAME'] = null;
                $sub_area = Area::orderBy('ORDER_ID', 'DESC')->where('IS_PARENT',0)->where('F_PARENT_AREA_NO',$value->PK_NO)->get();
                if($sub_area && count($sub_area) > 0 ){
                    foreach ($sub_area as $value1) {
                        $i++;
                        $areas[$i]['PK_NO'] = $value1->PK_NO;
                        $areas[$i]['ORDER_ID'] = $value1->ORDER_ID;
                        $areas[$i]['AREA_NAME'] = null;
                        $areas[$i]['CITY_NAME'] = $value1->CITY_NAME;
                        $areas[$i]['LAT'] = $value1->LAT;
                        $areas[$i]['LON'] = $value1->LON;
                        $areas[$i]['SUB_AREA_NAME'] = $value1->AREA_NAME;

                    }
                }

                $i++;
             }
        }


        return $this->formatResponse(true, '', 'admin.area.list', $areas);
    }

    public function getArea(int $id): object
    {
        $area = Area::find($id);
        return $this->formatResponse(true, '', 'admin.area.list', $area);
    }

    public function postStore($request): object
    {
        $this->status = false;
        $this->msg = 'Area could not be added!';

        DB::beginTransaction();
        try {
            $city = new Area();
            $city->AREA_NAME = $request->area_name;
            $city->URL_SLUG = Str::slug($request->area_name);
            $city->F_PARENT_AREA_NO = $request->parent_area > 0 ? $request->parent_area : null;
            $city->IS_PARENT = $request->parent_area == 0;
            $city->ORDER_ID = $request->order;
            $city->F_CITY_NO = $request->city;
            $city->LAT = $request->latitude;
            $city->LON = $request->longitude;
            $city->save();

            $this->status = true;
            $this->msg = 'Area added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.area.list');
    }

    public function postUpdate($request, int $id): object
    {
        $this->status = false;
        $this->msg = 'Area could not be updated!';

        DB::beginTransaction();
        try {
            $city = Area::find($id);
            $city->AREA_NAME = $request->area_name;
            $city->URL_SLUG = Str::slug($request->area_name);
            $city->F_PARENT_AREA_NO = $request->parent_area > 0 ? $request->parent_area : null;
            $city->IS_PARENT = $request->parent_area == 0;
            $city->ORDER_ID = $request->order;
            $city->F_CITY_NO = $request->city;
            $city->LAT = $request->latitude;
            $city->LON = $request->longitude;
            $city->save();

            $this->status = true;
            $this->msg = 'Area added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.area.list');
    }

    public function getCityAreas(int $id)
    {
        return City::with(['areas'])->find($id);
    }

    public function getSubArea(int $id)
    {
        return Area::where('F_PARENT_AREA_NO', $id)->get();
    }
}
