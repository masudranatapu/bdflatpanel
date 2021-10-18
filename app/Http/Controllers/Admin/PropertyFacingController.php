<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyFacingRequest;
use App\Repositories\Admin\PropertyFeatures\PropertyFeaturesInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyFacingController extends Controller
{
    protected $features;
    protected $resp;

    public function __construct(PropertyFeaturesInterface $features)
    {
        $this->features = $features;
    }

    public function getIndex()
    {
        $data['facings'] = $this->features->getFacings()->data;
        return view('admin.property-facing.index', compact('data'));
    }

    public function getCreate()
    {
        return view('admin.property-facing.create');
    }

    public function postStore(PropertyFacingRequest $request): RedirectResponse
    {
        $this->resp = $this->features->storeFacing($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $data['facing'] = $this->features->getFacing($id)->data;
        return view('admin.property-facing.edit', compact('data'));
    }

    public function postUpdate(PropertyFacingRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->features->updateFacing($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
