<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AreaRequest;
use App\Repositories\Admin\Area\AreaInterface;
use App\Repositories\Admin\City\CityInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AreaController extends Controller
{
    protected $area;
    protected $city;
    protected $resp;

    public function __construct(AreaInterface $area, CityInterface $city)
    {
        $this->area = $area;
        $this->city = $city;
    }

    public function getIndex()
    {
        $data['areas'] = $this->area->getAreas()->data;
        return view('admin.area.index', compact('data'));
    }

    public function getCreate()
    {
        $data['cities'] = $this->city->getCities()->data->pluck('CITY_NAME', 'PK_NO');
        return view('admin.area.create', compact('data'));
    }

    public function postStore(AreaRequest $request): RedirectResponse
    {
        $this->resp = $this->area->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $data['area'] = $this->area->getArea($id)->data;
        $data['cities'] = $this->city->getCities()->data->pluck('CITY_NAME', 'PK_NO');
        return view('admin.area.edit', compact('data'));
    }

    public function postUpdate(AreaRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->area->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getArea(Request $request): \Illuminate\Http\JsonResponse
    {
        $status = false;
        $data = [];

        if ($request->query->get('city')) {
            $city = $this->area->getCityAreas($request->query->get('city'));
            if ($city && $city->areas) {
                $data = $city->areas->pluck('AREA_NAME', 'PK_NO');
                $status = true;
            }
        }

        if ($request->query->get('area')) {
            $area = $this->area->getSubArea($request->query->get('area'));
            if ($area) {
                $data = $area->pluck('AREA_NAME', 'PK_NO');
                $status = true;
            }
        }
        return Response::json([
            'status' => $status,
            'data' => $data
        ]);
    }
}
