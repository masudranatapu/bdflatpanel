<?php
namespace App\Repositories\Admin\Brand;
use App\Models\Brand;
use App\Traits\RepoResponse;
use DB;
use Image;
use Auth;
use Str;
class BrandAbstract implements BrandInterface
{
    use RepoResponse;

    protected $brand;

    public function __construct(Brand $brand)
    {
        $this->brand = $brand;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->brand->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'product.brand.list', $data);
    }

    public function getSlugByText($txt){
        $str = strtolower($txt);
        $slug = Str::slug($str);
         // $slug  =str_replace(" ", "-", preg_replace('/([%\$!-_;:,.#\'"@*]+)/','-', $str));
        return $slug;
    }


    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $brand                      = new Brand();
            $brand->CODE                = 'D';
            $brand->NAME                = $request->name;
            if(!empty($request->slug)){
                $brand->SLUG            = $this->getSlugByText($request->url_slug);
            }
            else {
                $brand->SLUG            = $this->getSlugByText($request->name);
            }

            if(!is_null($request->file('logo')))
            {
                $brand->BRAND_LOGO      = $this->uploadImage($request->logo);
            }

            if(!empty($request->is_active)){
                $brand->IS_ACTIVE       = $request->is_active;
            }
            else{
                $brand->IS_ACTIVE       = 1;
            }

            $brand->IS_FEATURE          = $request->is_feature;

            $brand->ORDER_ID            = Brand::max('ORDER_ID')+1;

            $brand->F_SS_CREATED_BY     = Auth::user()->PK_NO;

            $brand->SS_CREATED_ON       = date("Y-m-d h:i:s", time());

            $brand->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Brand not created successfully !', 'product.brand.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Brand has been created successfully !', 'product.brand.list');
    }

    public function findOrThrowException($id)
    {
        $data = $this->brand->where('PK_NO', '=', $id)->first();

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.brand.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.brand.list', null);
    }


    public function postUpdate($request, $id)
    {

        DB::beginTransaction();

        try {
            $brand = Brand::find($id);
            $brand->NAME                        = $request->name;
            if(!empty($request->slug)){
                $brand->SLUG                    = $this->getSlugByText($request->url_slug);
            }
            else {
                $brand->SLUG                    = $this->getSlugByText($request->name);
            }

            if(!is_null($request->file('logo')))
            {
                $brand->BRAND_LOGO              = $this->uploadImage($request->logo);
            }
            if(!empty($request->is_active)){
                $brand->IS_ACTIVE                   = $request->is_active;
            }

            $brand->IS_FEATURE          = $request->is_feature;

            $brand->F_SS_MODIFIED_BY            = Auth::user()->PK_NO;

            $brand->SS_MODIFIED_ON              = date("Y-m-d h:i:s", time());

            $brand->update();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Brand !', 'product.brand.list');
        }

        DB::commit();

        return $this->formatResponse(true, 'Brand has been updated successfully !', 'product.brand.list');
    }

    public function uploadImage($image)
    {
      if($image)
      {
          $filename = $image->getClientOriginalExtension();
          $file_name1 = 'prod_'. date('dmY'). '_' .uniqid().'.'.$filename;
          $image->move(public_path().'/media/images/banner/', $file_name1);
          $imageUrl1 = '/media/images/banner/'. $file_name1;

      }

      return $imageUrl1;

    }

    public function delete($id)
    {
        DB::begintransaction();
        try {
            $brand = Brand::find($id)->delete();
            // $brand->IS_ACTIVE = 0;
            // $brand->update();

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete this action !', 'product.brand.list');
        }

        DB::commit();

        return $this->formatResponse(true, 'Successfully delete this action !', 'product.brand.list');
    }




}
