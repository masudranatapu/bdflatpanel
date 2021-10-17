<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Web\Slider;
use App\Repositories\Admin\Slider\SliderInterface;
use App\Http\Requests\Web\SliderRequest;
use Auth;
class SliderController extends Controller
{

    protected $slider;
    protected $sliderInt;

    public function __construct(SliderInterface $sliderInt, Slider $slider)
    {
        $this->slider  = $slider;
        $this->sliderInt  = $sliderInt;
    }

    public function getAllSlider(Request $request){

        $this->resp = $this->sliderInt->getPaginatedList($request);

        $data['slider'] = $this->resp->data;
        return view('admin.web.slider.index')->withData($data);
    }
     public function createSlider(){
        return view('admin.web.slider.create');

    }


    public function postStore(Request $request)
    {
        $this->resp = $this->sliderInt->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);

    }

    public function getEdit(Request $request, $id)
    {
        $data[] = '';
        $this->resp = $this->sliderInt->getShow($id);
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
        return view('admin.web.slider.edit')->withData($this->resp->data);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->sliderInt->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function getDelete($id)
    {
        $this->resp = $this->sliderInt->delete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function changeFeatureStatus(Request $request){

        $id                   = $request->id;
        $slider               = Slider::findOrFail($id);
        $slider->IS_FEATURE   = !$slider->IS_FEATURE;
        $slider->MODIFIED_BY  = Auth::user()->PK_NO;
        $slider->update();
        return response()->json($slider);
    }

}
