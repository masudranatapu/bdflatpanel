<?php

namespace App\Repositories\Admin\ProductModel;

use App\Traits\RepoResponse;
use App\Models\ProductModel;
use DB;

class ProductModelAbstract implements ProductModelInterface
{

    use RepoResponse;

    protected $productModel;

    public function __construct(ProductModel $productModel)
    {
        $this->productModel = $productModel;
    }

    public function getPaginatedList($request)
    {
        $data = $this->productModel->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '','admin.product-model',$data);
    }
    public function postStore($request)
    {
        $productModel                   = new ProductModel();
        $productModel->NAME             = $request->name;
        $productModel->CODE             = 'P';
        $productModel->F_PRD_BRAND_NO   = $request->brand;
        $productModel->COMPOSITE_CODE   = $request->composite_code;

        if ($productModel->save()) {
            return $this->formatResponse(true, 'Product model has been created successfully', 'admin.product-model');
        }

        return $this->formatResponse(false, 'Unable to create product model !', 'admin.product-model.new');
    }

    public function getShow(int $PK_NO)
    {
        $data = ProductModel::where('PK_NO',$PK_NO)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.product-model.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.product-model', null);
    }

    public function postUpdate($request, $PK_NO)
    {
        $productModel = ProductModel::find($PK_NO);
        $productModel->NAME = $request->name;
        $productModel->F_PRD_BRAND_NO = $request->brand;
        $productModel->COMPOSITE_CODE = $request->composite_code;

        if ($productModel->update()) {
            return $this->formatResponse(true, 'Product model has been Updated successfully', 'admin.product-model');
        }

        return $this->formatResponse(false, 'Unable to update Product model !', 'admin.productModel.new');
    }

    public function delete(int $PK_NO)
    {
        $productModel = ProductModel::find($PK_NO)->delete();
        // $productModel->IS_ACTIVE = 0;
        // $productModel->update();

        return $this->formatResponse(true,'Successfully deleted product model','admin.product-model');
    }


    public function getList()
    {
        return DB::table('admin.product-Model')->pluck('name', 'PK_NO');
    }


}
