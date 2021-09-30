<?php

namespace App\Http\Controllers\Admin;

use App\Models\Area;
use App\Models\City;
use App\Models\FloorList;
use App\Models\ListingAdditionalInfo;
use App\Models\ListingFeatures;
use App\Models\ListingImages;
use App\Models\ListingVariants;
use App\Models\NearBy;
use App\Models\PropertyCondition;
use App\Models\PropertyFacing;
use App\Models\PropertyListingType;
use App\Models\PropertyType;
use Session;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Hscode;
use App\Models\Product;
use App\Models\Category;
use App\Models\VatClass;
use App\Models\ProductSize;
use App\Models\SubCategory;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Repositories\Admin\Product\ProductInterface;


class ProductController extends BaseController
{
    protected $product;
    protected $property_type;
    protected $city;
    protected $area;
    protected $property_condition;
    protected $listing_variants;
    protected $floor_list;
    protected $property_facing;
    protected $property_additional_info;
    protected $listing_feature;
    protected $near_by;
    protected $property_listing_type;
    protected $property_listing_images;
    protected $resp;

    protected $productModel;
    protected $productInt;
    protected $vatClass;
    protected $category;
    protected $subCategory;
    protected $brand;
    protected $size;
    protected $color;
    protected $hscode;

    public function __construct(
        Product $product,
        PropertyType $property_type,
        City $city,
        Area $area,
        PropertyCondition $property_condition,
        ListingVariants $listing_variants,
        FloorList $floor_list,
        PropertyFacing $property_facing,
        ListingAdditionalInfo $property_additional_info,
        ListingFeatures $listing_feature,
        NearBy $near_by,
        PropertyListingType $property_listing_type,
        ListingImages $property_listing_images,


        ProductModel $productModel,
        ProductInterface $productInt,
        VatClass $vatClass,
        Category $category,
        SubCategory $subCategory,
        Brand $brand,
        ProductSize $size,
        Color $color,
        Hscode $hscode
    )
    {
        $this->product = $product;
        $this->property_type = $property_type;
        $this->city = $city;
        $this->area = $area;
        $this->property_condition = $property_condition;
        $this->listing_variants = $listing_variants;
        $this->floor_list = $floor_list;
        $this->property_facing = $property_facing;
        $this->property_additional_info = $property_additional_info;
        $this->listing_feature = $listing_feature;
        $this->near_by = $near_by;
        $this->property_listing_type = $property_listing_type;
        $this->property_listing_images = $property_listing_images;


        $this->productModel = $productModel;
        $this->productInt = $productInt;
        $this->vatClass = $vatClass;
        $this->category = $category;
        $this->subCategory = $subCategory;
        $this->brand = $brand;
        $this->color = $color;
        $this->size = $size;
        $this->hscode = $hscode;
    }

    public function getIndex(Request $request)
    {

        $this->resp = $this->productInt->getPaginatedList($request);
        $data = $this->resp->data;
        return view('admin.product.index', compact('data'));
    }


    public function getProductSearch()
    {
        Session::put('list_type', 'searchlist');
        return view('admin.product.search_list');
    }



    public function getProdModel($brand_id)
    {
        $prod_model = $this->productModel->getProdModel($brand_id);
        return response()->json($prod_model);

    }

    public function getSubcat($cat_id)
    {
        $sub_cat = $this->subCategory->getSubcateByCategor($cat_id);
        return response()->json($sub_cat);
    }



    public function postStoreProductVariant(ProductVariantRequest $request)
    {
        $this->resp = $this->productInt->postStoreProductVariant($request);
        $pk_no = $request->pk_no;
        return redirect()->route('admin.product.edit', ['id' => $pk_no, 'type' => 'variant', 'tab' => 2])->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function getEdit(Request $request, $id)
    {
        $data[] = '';
        $this->resp = $this->productInt->getShow($id);
        $data['property_type'] = $this->property_type->getProperty();
        $data['city'] = $this->city->getCity();
        $data['area'] = $this->area->getArea($this->resp->data->F_CITY_NO);
        $data['property_condition'] = $this->property_condition->getPropertyCondition();
        $data['listing_variants'] = $this->listing_variants->getListingVariants($id);
        $data['floor_list'] = $this->floor_list->getFloorList();
        $data['property_facing'] = $this->property_facing->getPropertyFacing();
        $data['property_additional_info'] = $this->property_additional_info->getAdditionalInfo($id);
        $data['listing_feature'] = $this->listing_feature->getListingFeature();
        $data['near_by'] = $this->near_by->getNearBy();
        $data['property_listing_type'] = $this->property_listing_type->getPropertyListingType();
        // dd($data['property_listing_type']);
        $data['property_listing_images'] = $this->property_listing_images->getListingImages($id);

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
        return view('admin.product.property_edit')->withProduct($this->resp->data)->withData($data);

    }

    public function postDeleteImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $this->resp = $this->productInt->deleteImage($request->get('id'));
        return response()->json($this->resp);
    }

    public function getaAtivity($id)
    {
        $data['property'] = Product::find($id);
        return view('admin.product.property_activity',compact('data'));
    }


    public function getView($id)
    {
        $data = [];
        $this->resp = $this->productInt->getShow($id);
        $data['property_type'] = $this->property_type->getProperty();
        $data['city'] = $this->city->getCity();
        $data['area'] = $this->area->getArea($this->resp->data->F_CITY_NO);
        $data['property_condition'] = $this->property_condition->getPropertyCondition();
        $data['listing_variants'] = $this->listing_variants->getListingVariants($id);
        $data['floor_list'] = $this->floor_list->getFloorList();
        $data['property_facing'] = $this->property_facing->getPropertyFacing();
        $data['property_additional_info'] = $this->property_additional_info->getAdditionalInfo($id);
        $data['listing_feature'] = $this->listing_feature->getListingFeature();
        $data['near_by'] = $this->near_by->getNearBy();
        $data['property_listing_type'] = $this->property_listing_type->getPropertyListingType();
        $data['property_listing_images'] = $this->property_listing_images->getListingImages($id);

        return view('admin.product.view')->withProduct($this->resp->data)->withData($data);
    }

    public function getDeleteImage($id): \Illuminate\Http\JsonResponse
    {
        $this->resp = $this->productInt->deleteImage($id);
        return response()->json($this->resp);
    }

    public function getHscode($subcat_id = null)
    {
        $this->resp = $this->hscode->getHscodeCombo($subcat_id);
        return response()->json($this->resp);
    }

    public function putUpdate(ProductRequest $request, $id)
    {
        $this->resp = $this->productInt->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdateProductVariant(ProductVariantRequest $request, $id)
    {
        $this->resp = $this->productInt->postUpdateProductVariant($request, $id);
        return redirect()->route('admin.product.edit', ['id' => $request->pk_no, 'type' => 'variant', 'tab' => 2])->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function getDelete($id)
    {
        $this->resp = $this->productInt->delete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDeleteProductVariant($id)
    {
        $this->resp = $this->productInt->getDeleteProductVariant($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getProductSearchList(Request $request)
    {
        if ($request->ajax()) {
            $this->resp = $this->productInt->getProductSearchList($request);
            $multiple_select = trim($request->multiple_select);
            $html = view('admin.components._result_rows')->withRows($this->resp->data)->withMultiselect($multiple_select)->render();
            $data['html'] = $html;
            return response()->json($data);

        } else {
            $this->resp = $this->productInt->getProductSearchList($request);
            $data[] = '';
            $data['vat_class_combo'] = $this->vatClass->getVatClassCombo();
            $data['category_combo'] = $this->category->getCategorCombo();
            $data['brand_combo'] = $this->brand->getBrandCombo();
            $data['rows'] = $this->resp->data;

            return view('admin.product.search_result', compact('data'));
        }


    }


    public function getProductSearchGoBack(Request $request)
    {

        $url = $request->parent_url;
        $queryString = $product_no_arra = request()->get('product_no');

        if (empty($url)) {
            return redirect()->back();
        }

        if (empty($queryString)) {
            return redirect()->to($url);
        }

        $queryString = http_build_query($queryString, 'product_no_');
        $queryString = $url . '?' . $queryString;

        if (!empty($url) && (!empty($product_no_arra))) {
            return redirect()->to($queryString);
        } else {
            return redirect()->back();
        }


    }

    public function test(Request $request)
    {

        $data = '';
        return view('admin.product.test')->withData($data);
    }

    public function getArea($id)
    {
        $data['area'] = Area::where('F_CITY_NO', $id)->whereNull('F_PARENT_AREA_NO')->orderBy('AREA_NAME', 'ASC')->pluck('AREA_NAME', 'PK_NO');
        return response()->json($data);
    }

    public function addListingVariant(Request $request)
    {
        $data['html'] = view('admin.product._add_listing_variant',compact('request'))->render();
        return response()->json($data);
    }

    public function getPropertyType($id)
    {
        return PropertyType::where('PK_NO',$id)->first()->TYPE;
    }

}
