<?php
namespace App\Repositories\Admin\ProductSize;

use App\Models\ProductSize;
use App\Traits\RepoResponse;
use DB;

class ProductSizeAbstract implements ProductSizeInterface
{
    use RepoResponse;

    protected $productSize;

    public function __construct(ProductSize $productSize)
    {
        $this->productsize = $productSize;
    }

    public function getPaginatedList($request,int $per_page = 20)
    {
        $data = $this->productsize->where('IS_ACTIVE',1)->orderBy('NAME','ASC')->get();

        return $this->formatResponse(true, '','admin.product-size',$data);
    }
    public function postStore($request)
    {
        $productSize                   = new ProductSize();
        $productSize->NAME             = $request->name;
        $productSize->F_BRAND_NO       = $request->brand;
        if ($productSize->save()) {
            return $this->formatResponse(true, 'Productsize add has been created successfully', 'admin.product-size');
        }

        return $this->formatResponse(false, 'Unable to create productsize user !', 'admin.product-size.new');
    }

    public function getShow(int $id)
    {   
        $data = ProductSize::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.product-size.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.product-size', null);
    }

    public function postUpdate($request, $id)
    {
        $productSize = ProductSize::find($id);
        $productSize->NAME = $request->name;
        $productSize->F_BRAND_NO = $request->brand;
        if ($productSize->update()) {
            return $this->formatResponse(true, 'Productsize user has been Updated successfully', 'admin.product-size');
        }

        return $this->formatResponse(false, 'Unable to update ProductSize user !', 'admin.product-size.new');
    }

    public function delete(int $id)
    {
        $productSize = ProductSize::find($id)->delete();
        // $productSize->IS_ACTIVE = 0;
        // $productSize->update();

        return $this->formatResponse(true,'Successfully deleted productsize user','admin.product-size');
    }


    public function getList()
    {
        return DB::table('admin.product-size')->pluck('name', 'id');
    }
}
