<?php


namespace App\Repositories\Admin\City;


use App\Models\City;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CityAbstract implements CityInterface
{
    use RepoResponse;

    protected $status;
    protected $msg;

    public function getCities($limit = 2000): object
    {
        $cities = City::orderBy('ORDER_ID', 'DESC')->paginate($limit);
        return $this->formatResponse(true, '', 'admin.city.list', $cities);
    }

    public function getCity(int $id): object
    {
        $cities = City::find($id);
        return $this->formatResponse(true, '', 'admin.city.list', $cities);
    }

    public function postStore($request): object
    {
        $this->status = false;
        $this->msg = 'City could not be added!';

        DB::beginTransaction();
        try {
            $city = new City();
            $city->CITY_NAME = $request->city_name;
            $city->URL_SLUG = Str::slug($request->city_name);
            $city->ORDER_ID = $request->order;
            $city->LAT = $request->latitude;
            $city->LON = $request->longitude;
            $city->IS_POPULATED = $request->populate;
            $city->IS_ACTIVE = $request->status;
            $city->save();

            $this->status = true;
            $this->msg = 'City added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.city.list');
    }

    public function postUpdate($request, int $id): object
    {
        $this->status = false;
        $this->msg = 'City could not be update!';

        DB::beginTransaction();
        try {
            $city = City::find($id);
            $city->CITY_NAME = $request->city_name;
            $city->URL_SLUG = Str::slug($request->city_name);
            $city->ORDER_ID = $request->order;
            $city->LAT = $request->latitude;
            $city->LON = $request->longitude;
            $city->IS_POPULATED = $request->populate;
            $city->IS_ACTIVE = $request->status;
            $city->save();

            $this->status = true;
            $this->msg = 'City updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.city.list');
    }
}
