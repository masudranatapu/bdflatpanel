<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyConditionRequest;
use App\Repositories\Admin\PropertyCondition\PropertyConditionInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyConditionController extends Controller
{
    protected $condition;
    protected $resp;

    public function __construct(PropertyConditionInterface $condition)
    {
        $this->condition = $condition;
    }

    public function getIndex()
    {
        $data['conditions'] = $this->condition->getPropertyConditions()->data;
        return view('admin.property-condition.index', compact('data'));
    }

    public function getCreate()
    {
        return view('admin.property-condition.create');
    }

    public function postStore(PropertyConditionRequest $request): RedirectResponse
    {
        $this->resp = $this->condition->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $data['condition'] = $this->condition->getPropertyCondition($id)->data;
        return view('admin.property-condition.edit', compact('data'));
    }

    public function postUpdate(PropertyConditionRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->condition->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
