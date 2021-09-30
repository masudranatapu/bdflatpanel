<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\VendorRequest;
use App\Repositories\Admin\Faulty\FaultyInterface;

class FaultyController extends BaseController
{
    protected $faultyInt;

    public function __construct(FaultyInterface $faultyInt)
    {
        $this->faultyInt       = $faultyInt;
    }


    public function getIndex($type,$id)
    {
        $this->resp = $this->faultyInt->findOrThrowException($type,$id);
        $data       = $this->resp->data;

        return view('admin.faulty.faulty')->withData($data);
    }

    public function ajaxFaultyChecker($id) {

        $data = $this->faultyInt->ajaxFaultyChecker($id);
        return $data;
    }
}
