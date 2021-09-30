<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyFeaturesRequest;
use App\Repositories\Admin\PropertyFeatures\PropertyFeaturesInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyFeaturesController extends Controller
{
    protected $features;
    protected $resp;

    public function __construct(PropertyFeaturesInterface $features)
    {
        $this->features = $features;
    }

    public function getIndex()
    {
        $data['features'] = $this->features->getFeatures()->data;
        return view('admin.property-features.index', compact('data'));
    }

    public function getCreate()
    {
        return view('admin.property-features.create');
    }

    public function postStore(PropertyFeaturesRequest $request): RedirectResponse
    {
        $this->resp = $this->features->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $data['feature'] = $this->features->getFeature($id)->data;
        return view('admin.property-features.edit', compact('data'));
    }

    public function postUpdate(PropertyFeaturesRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->features->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
