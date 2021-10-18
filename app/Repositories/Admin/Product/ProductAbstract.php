<?php

namespace App\Repositories\Admin\Product;

use App\Models\ListingPrice;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ListingSEO;
use App\Models\ProdImgLib;
use App\Models\ListingType;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use App\Models\ListingImages;
use App\Models\ListingPayment;
use App\Models\ProductVariant;
use App\Models\ListingVariants;
use App\Models\AdminUser as User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ListingAdditionalInfo;
use Intervention\Image\Facades\Image;
use App\Repositories\Admin\Auth\AuthAbstract;

class ProductAbstract implements ProductInterface
{
    use RepoResponse;

    protected $user;
    protected $auth;
    protected $product;

    public function __construct(User $user, AuthAbstract $auth, Product $product)
    {
        $this->user = $user;
        $this->auth = $auth;
        $this->product = $product;
    }

    public function getPaginatedList($request, int $per_page = 2000)
    {

        //4=DELETED
        $data['listings'] = [];
        $data['user_type'] = DB::table('SS_USER_TYPE')->where('TYPE_NO', '!=', 1)->orderBy('PK_NO', 'ASC')->pluck('TITLE', 'PK_NO');
        $data['listing_type'] = DB::table('PRD_LISTING_TYPE')->where('IS_ACTIVE', '=', 1)->orderBy('ORDER_ID', 'ASC')->pluck('NAME', 'PK_NO');
        $data['cities'] = DB::table('SS_CITY')->pluck('CITY_NAME', 'PK_NO');
        return $this->formatResponse(true, '', 'admin.product.list', $data);
    }


    public function postUpdate($request, int $id): object
    {
      
        DB::beginTransaction();
        try {
            $area = $request->area;
            if ($request->sub_area && $request->sub_area > 0) {
                $area = $request->sub_area;
            }

            $list = Product::with(['listingSEO', 'getUser'])->find($id);
            $list->PROPERTY_FOR = $request->property_for;
            $list->F_PROPERTY_TYPE_NO = $request->propertyType;
            $list->F_CITY_NO = $request->city;
            $list->F_AREA_NO = $area;
            $list->ADDRESS = $request->address;
            $list->F_PROPERTY_CONDITION = $request->condition;
            $list->TITLE = $request->ad_title;
            $list->PRICE_TYPE = $request->property_priceChek;
            $list->CONTACT_PERSON1 = $request->contact_person;
            $list->CONTACT_PERSON2 = $request->contact_person_2;
            $list->MOBILE1 = $request->mobile;
            $list->MOBILE2 = $request->mobile_2;
            $list->F_LISTING_TYPE = $request->listing_type;
            $list->TOTAL_FLOORS = $request->floor;
            $list->FLOORS_AVAIABLE = json_encode($request->floor_available);
            $list->MODIFIED_BY = Auth::id();
            $list->MODIFIED_AT = Carbon::now();
            $list->URL_SLUG_LOCKED = 1;
            $list->IS_VERIFIED = $request->is_verified ? 1 : 0;
            $list->CI_PAYMENT = $request->ci_payment ? 1 : 0;
            $list->CI_PRICE = $request->contact_view_price;
            $list->AGENT_COMMISSION_AMT = $request->agent_commission_amt ?? 0;
            $list->PAYMENT_AUTO_RENEW = $request->auto_payment_renew ? 1 : 0;
            $list->MAX_SHARING_PERMISSION = $request->max_sharing_permission;
            if ($request->billing == 'paid') {
                $price = ListingPrice::where('F_LISTING_TYPE_NO', $request->listing_type)->first();
                $list_type = ListingType::where('PK_NO', $request->listing_type)->first();
                $property_price = 0;
                if ($request->property_for == 'roommate') {
                    $property_price = $price->ROOMMAT_PRICE;
                } elseif ($request->property_for == 'sale') {
                    $property_price = $price->SELL_PRICE;
                } elseif ($request->property_for == 'rent') {
                    $property_price = $price->RENT_PRICE;
                }
                if ($property_price <= $list->getUser->UNUSED_TOPUP) {
                    $list->PAYMENT_STATUS = 1;
                    $pay = new ListingPayment();
                    $pay->F_LISTING_NO = $id;
                    $pay->F_USER_NO = $list->F_USER_NO;
                    $pay->START_DATE = date('Y-m-d');
                    $pay->END_DATE = date('Y-m-d', strtotime($list_type->DURATION . ' days'));
                    $pay->AMOUNT = $property_price;
                    $pay->save();
                    if ($request->status == 10) {
                        $list->STATUS = $request->status;
                    }
                } else {
                    return $this->formatResponse(false, 'Insufficient balance !', 'admin.product.list');
                }

            }

            if ($request->status != 10) {
                $list->STATUS = $request->status;
            } else if ($list->PAYMENT_STATUS == 1 || $list->getUser->USER_TYPE == 5) {
                $list->STATUS = $request->status;
                $list->PAYMENT_STATUS = 1;
            } else {
                return $this->formatResponse(false, 'Payment required !', 'admin.product.list');
            }

            $list->update();

            $property_size = $request->size;
            ListingVariants::where('F_LISTING_NO', $id)->delete();
            foreach ($property_size as $key => $item) {
                $data = array(
                    'F_LISTING_NO' => $list->PK_NO,
                    'PROPERTY_SIZE' => $request->size[$key],
                    'BEDROOM' => $request->bedroom[$key],
                    'BATHROOM' => $request->bathroom[$key],
                    'TOTAL_PRICE' => $request->price[$key],
                    'IS_DEFAULT' => $key == 0,
                );
                ListingVariants::insert($data);
            }

            // SEO
            $seo = $list->listingSEO;
            if (!$seo) {
                $seo = new ListingSEO();
                $seo->F_LISTING_NO = $list->PK_NO;
            }
            $seo->META_TITLE = $request->meta_title;
            $seo->META_DESCRIPTION = $request->meta_description;
            $seo->META_URL = $request->meta_url;

            if ($request->hasFile('seo_image')) {
                if ($seo->OG_IMAGE_PATH) {
                    $this->removeFile($seo->OG_IMAGE_PATH);
                }
                $image = $request->file('seo_image')[0];
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/listings/' . $list->PK_NO . '/seo/';
                $image->move($image_path, $image_name);
                $seo->OG_IMAGE_PATH = $image_path . $image_name;
            }
            $seo->save();

            // for image upload
            if ($request->hasfile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    $name = uniqid() . '.' . $image->getClientOriginalExtension();
                    $name2 = uniqid() . '.' . $image->getClientOriginalExtension();
                    $waterMarkUrl = public_path('assets/img/logo.png');

                    $destinationPath = public_path('/uploads/listings/' . $list->PK_NO . '/');
                    $destinationPath2 = public_path('/uploads/listings/' . $list->PK_NO . '/thumb');

                    if (!file_exists($destinationPath2)) {
                        mkdir($destinationPath2, 0755, true);
                    }
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $thumb_img = Image::make($image->getRealPath());
                    $thumb_img->backup();
                    $thumb_img->resize(172, 115, function ($constraint) {
                    });
                    $thumb_img->save($destinationPath2 . '/' . $name2);
                    $thumb_img->reset();
                    $thumb_img->insert($waterMarkUrl, 'bottom-left', 5, 5);
                    $thumb_img->save($destinationPath . '/' . $name);

                    ListingImages::create([
                        'F_LISTING_NO' => $list->PK_NO,
                        'IMAGE_PATH' => '/uploads/listings/' . $id . '/' . $name,
                        'IMAGE' => $name,
                        'THUMB_PATH' => '/uploads/listings/' . $list->PK_NO . '/thumb/' . $name2,
                        'THUMB' => $name2,
                    ]);
                }
            }

            //  for features
            $features = ListingAdditionalInfo::where('F_LISTING_NO', $request->id)->first();
            $features->F_LISTING_NO = $list->PK_NO;
            $features->F_FACING_NO = $request->facing;
            $features->HANDOVER_DATE = Carbon::parse($request->handover_date)->format('Y-m-d H:i:s');
            $features->DESCRIPTION = $request->description;
            $features->LOCATION_MAP = $request->map_url;
            $features->VIDEO_CODE = $request->videoURL;
            $features->F_FEATURE_NOS = json_encode($request->features);
            $features->F_NEARBY_NOS = json_encode($request->nearby);
            $features->update();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return $this->formatResponse(false, 'Listings not updated !', 'admin.product.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Listings has been updated successfully !', 'admin.product.list');

    }


    /*
    private function removeFile($path)
    {
        if (file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }
    */

    public function getShow(int $id): object
    {
        $data = Product::select('PRD_LISTINGS.*', 'a.SELL_PRICE', 'a.RENT_PRICE', 'a.ROOMMAT_PRICE', 'b.DURATION')
            ->leftJoin('SS_LISTING_PRICE as a', 'a.F_LISTING_TYPE_NO', 'PRD_LISTINGS.F_LISTING_TYPE')
            ->leftJoin('PRD_LISTING_TYPE as b', 'b.PK_NO', 'PRD_LISTINGS.F_LISTING_TYPE')
            ->where('PRD_LISTINGS.PK_NO', $id)
            ->first();

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.product.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.product.list', null);
    }

    public function delete(int $id)
    {
        DB::begintransaction();

        try {
            $product = Product::find($id)->delete();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete product !', 'admin.product.list');
        }

        DB::commit();

        return $this->formatResponse(true, 'Successfully delete product with variant product !', 'admin.product.list');
    }


    /*
    public function getProductSearchList($request)
    {

        // $categories = DB::table('PRD_MASTER_SETUP')->whereIn('F_PRD_SUB_CATEGORY_ID', $sub_categories)->get();
        // dd($categories);

        $category = trim($request->category);
        $sub_category = trim($request->sub_category);
        $brand = trim($request->brand);
        $prod_model = trim($request->prod_model);
        $name = trim($request->name);
        $vat_class = trim($request->vat_class);
        $hs_code = trim($request->hs_code);
        $ig_code = trim($request->ig_code);
        $sku_id = trim($request->sku_id);
        $barcode = trim($request->barcode);
        $shipping_method = trim($request->preferred_shipping_method);


        $data = ProductVariant::where('PRD_VARIANT_SETUP.IS_ACTIVE', '=', 1)
            ->select('PRD_VARIANT_SETUP.*')
            ->join('PRD_MASTER_SETUP', 'PRD_MASTER_SETUP.PK_NO', 'PRD_VARIANT_SETUP.F_PRD_MASTER_SETUP_NO');

        if (!empty($name)) {
            // $data->orWhere('PRD_VARIANT_SETUP.VARIANT_NAME', 'LIKE', '%' . $name . '%');
            // $data->where('PRD_VARIANT_SETUP.KEYWORD_SEARCH', 'LIKE', '%' . $name . '%');
            // $data->orWhere('PRD_VARIANT_SETUP.BARCODE', 'LIKE', '%' . $name . '%');
            // $data->orWhere('PRD_VARIANT_SETUP.MRK_ID_COMPOSITE_CODE', 'LIKE', '%' . $name . '%');

            $pieces = explode(" ", $name);
            if ($pieces) {
                foreach ($pieces as $key => $piece) {
                    $data->where('PRD_VARIANT_SETUP.VARIANT_NAME', 'LIKE', '%' . $piece . '%');
                    $data->Where('PRD_VARIANT_SETUP.KEYWORD_SEARCH', 'LIKE', '%' . $piece . '%');
                }
            }
        }


        if (!empty($vat_class)) {
            $data->where('PRD_VARIANT_SETUP.F_VAT_CLASS', '=', $vat_class);
        }

        if (!empty($vat_class)) {
            $data->where('PRD_VARIANT_SETUP.F_VAT_CLASS', '=', $vat_class);
        }
        if (!empty($shipping_method)) {
            $data->where('PRD_VARIANT_SETUP.PREFERRED_SHIPPING_METHOD', '=', $shipping_method);
        }

        if (!empty($hs_code)) {
            $data->where('PRD_VARIANT_SETUP.HS_CODE', 'LIKE', '%' . $hs_code . '%');
        }

        if (!empty($sub_category)) {
            $data->where('PRD_MASTER_SETUP.F_PRD_SUB_CATEGORY_ID', '=', $sub_category);

        } elseif (!empty($category)) {
            $sub_categories = DB::table('PRD_SUB_CATEGORY')->where('F_PRD_CATEGORY_NO', $category)->Pluck('PK_NO');
            if (!empty($sub_categories)) {
                $data->whereIn('PRD_MASTER_SETUP.F_PRD_SUB_CATEGORY_ID', $sub_categories);
            }

        }
        if (!empty($brand)) {
            $data->where('PRD_MASTER_SETUP.F_BRAND', '=', $brand);
        }
        if (!empty($prod_model)) {
            $data->where('PRD_MASTER_SETUP.F_MODEL', '=', $prod_model);
        }
        if (!empty($ig_code)) {
            $data->where('PRD_VARIANT_SETUP.MRK_ID_COMPOSITE_CODE', '=', $ig_code);
        }
        if (!empty($sku_id)) {
            $data->where('PRD_VARIANT_SETUP.COMPOSITE_CODE', '=', $sku_id);
        }
        if (!empty($barcode)) {
            $data->where('PRD_VARIANT_SETUP.BARCODE', '=', $barcode);
        }


        $data = $data->orderBy('PRD_MASTER_SETUP.DEFAULT_NAME', 'ASC')->groupBy('PRD_VARIANT_SETUP.PK_NO')->get();

        return $this->formatResponse(true, '', 'admin.product.list', $data);
    }


    // public function getSearchList($request)
    // {
    //     $string = trim($request->search_string);
    //     $data = Auth::where('user_type','!=',1 )
    //             ->where('auths.email', 'LIKE', '%' . $string . '%')->orWhere('auths.username', 'LIKE', '%' . $string . '%')
    //         ->join('admin_users', 'admin_users.auth_id', '=', 'auths.id')
    //         ->join('auth_role', 'auth_role.auth_id', '=', 'auths.id')
    //         ->leftJoin ('roles', 'roles.id', '=', 'auth_role.role_id')
    //         ->leftJoin ('user_groups', 'user_groups.id', '=', 'auth_role.USER_GROUP_ID')
    //         ->select('auths.username','auths.email','auths.mobile_no','auths.can_login','admin_users.first_name','admin_users.last_name','admin_users.designation','admin_users.auth_id','admin_users.profile_pic_url','admin_users.status', 'user_groups.group_name','roles.role_name')->get();
    //     return $this->formatResponse(true, '', 'admin', $data);
    // }


    public function deleteImage(int $id): object
    {
        DB::begintransaction();
        try {
            $image = ListingImages::find($id);
            $this->removeFile($image->IMAGE_PATH);
            $this->removeFile($image->THUMB_PATH);
            $image->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete product photo !', 'admin.product.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Successfully delete product photo !', 'admin.product.list');
    }


    public function postStoreProductVariant($request)
    {

        $brand_name = null;
        $model_name = null;
        $vat_amount = null;
        $prd_no = $request->pk_no;
        $color = DB::table('PRD_COLOR')->where('PK_NO', $request->color)->first();
        $size = DB::table('PRD_SIZE')->where('PK_NO', $request->size)->first();
        $vat_class = DB::table('ACC_VAT_CLASS')->where('PK_NO', $request->vat_class)->first();
        if ($color) {
            $color_name = $color->NAME;
        }

        if ($size) {
            $size_name = $size->NAME;
        }
        if ($vat_class) {
            $vat_amount = $vat_class->RATE;
        }

        $result = ProductVariant::where(['F_PRD_MASTER_SETUP_NO' => $prd_no, 'F_SIZE_NO' => $request->size, 'F_COLOR_NO' => $request->color])->first();

        if ($result) {
            return $this->formatResponse(false, 'Unable to create product variant because multiple product not allow by same color and same size !', 'admin.product.create');
        }
        DB::beginTransaction();
        try {
            $prod = new ProductVariant();
            $prod->F_PRD_MASTER_SETUP_NO = $prd_no;
            $prod->VARIANT_NAME = $request->name;
            $str = strtolower($request->name);
            $prod->URL_SLUG = Str::slug($str);
            $prod->VARIANT_CUSTOMS_NAME = $request->customs_name;
            $prod->F_SIZE_NO = $request->size;
            $prod->SIZE_NAME = $size_name;
            $prod->F_COLOR_NO = $request->color;
            $prod->COLOR = $color_name;
            $prod->HS_CODE = $request->hs_code;
            $prod->BARCODE = $request->barcode;
            $prod->IS_BARCODE_BY_MFG = $request->is_barcode_by_mfg ? 1 : 0;
            $prod->NARRATION = $request->narration;
            $prod->SHORT_NARRATION = $request->short_narration;
            $prod->PROMOTIONAL_MESSAGE = $request->promotional_message;
            $prod->F_PRIMARY_IMG_VARIANT_ID = null;
            $prod->PRIMARY_IMG_RELATIVE_PATH = null;
            $prod->REGULAR_PRICE = $request->price;
            $prod->INSTALLMENT_PRICE = $request->price_ins;
            $prod->SEA_FREIGHT_CHARGE = $request->sea_freight;
            $prod->AIR_FREIGHT_CHARGE = $request->air_freight;
            $prod->PREFERRED_SHIPPING_METHOD = $request->def_shipping_method;
            $prod->LOCAL_POSTAGE = $request->local_postage;
            $prod->INTER_DISTRICT_POSTAGE = $request->int_postage;
            $prod->F_VAT_CLASS = $request->vat_class;
            $prod->VAT_AMOUNT_PERCENT = $vat_amount;
            $prod->save();

            if ($request->file('images')) {

                $i = 0;
                foreach ($request->file('images') as $key => $image) {
                    // $image = $request->file('pro_image');
                    $filename = $image->getClientOriginalExtension();
                    $destinationPath1 = 'media/images/product';
                    $destinationPath2 = 'media/images/product/thumb';
                    if (!file_exists($destinationPath1)) {
                        mkdir($destinationPath1, 0755, true);
                    }
                    if (!file_exists($destinationPath2)) {
                        mkdir($destinationPath2, 0755, true);
                    }
                    $img = Image::make($image->getRealPath());
                    $file_name1 = 'prod_' . date('dmY') . '_' . uniqid() . '.' . $filename;
                    $file_name2 = 'prod_' . date('dmY') . '_' . uniqid() . '.webp';
                    Image::make($img)->save($destinationPath1 . '/' . $file_name1);
                    Image::make($img)->encode('webp', 100)->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        // $constraint->upsize();
                    })->save($destinationPath2 . '/' . $file_name2);
                    $image_url = $destinationPath1 . '/' . $file_name1;
                    $thumb_url = $destinationPath2 . '/' . $file_name2;

                    $file_name = 'prod_' . date('dmY') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $img_lib = new ProdImgLib();
                    $img_lib->F_PRD_VARIANT_NO = $prod->PK_NO;
                    $img_lib->IS_MASTER = 0;
                    $img_lib->F_FILE_TYPE = 1;
                    // $img_lib->FILE_EXT          = $image->getClientOriginalExtension();
                    $img_lib->RELATIVE_PATH = '/' . $image_url;
                    $img_lib->THUMB_PATH = '/' . $thumb_url;
                    $img_lib->SERIAL_NO = $i;
                    $img_lib->save();
                    if ($i == 0) {
                        $def_relative_path = '/media/images/products/' . $prd_no . '/' . $file_name;
                        $def_relative_id = $img_lib->PK_NO;
                    }
                    $image->move(public_path() . '/media/images/products/' . $prd_no . '/', $file_name);
                    $i++;
                }
                $update_prod = ProductVariant::find($prod->PK_NO);
                $update_prod->F_PRIMARY_IMG_VARIANT_ID = $def_relative_id ?? null;
                $update_prod->PRIMARY_IMG_RELATIVE_PATH = $def_relative_path ?? null;
                $update_prod->update();
            }
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Product variant not created successfully!', 'admin.product.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'Product variant has been created successfully !', 'admin.product.list');
    }


    public function postUpdateProductVariant($request, int $id)
    {
        $brand_name = null;
        $model_name = null;
        $vat_amount = null;
        $prd_no = $request->pk_no;
        $color = DB::table('PRD_COLOR')->where('PK_NO', $request->color)->first();
        $size = DB::table('PRD_SIZE')->where('PK_NO', $request->size)->first();
        $vat_class = DB::table('ACC_VAT_CLASS')->where('PK_NO', $request->vat_class)->first();
        if ($color) {
            $color_name = $color->NAME;
        }
        if ($size) {
            $size_name = $size->NAME;
        }
        if ($vat_class) {
            $vat_amount = $vat_class->RATE;
        }

        DB::beginTransaction();
        try {
            $prod = ProductVariant::find($id);
            $prod->F_PRD_MASTER_SETUP_NO = $prd_no;
            $prod->VARIANT_NAME = $request->name;
            $str = strtolower($request->name);
            $prod->URL_SLUG = Str::slug($str);
            $prod->F_SIZE_NO = $request->size;
            $prod->SIZE_NAME = $size_name;
            $prod->F_COLOR_NO = $request->color;
            $prod->COLOR = $color_name;
            $prod->HS_CODE = $request->hs_code;
            $prod->BARCODE = $request->barcode;
            $prod->IS_BARCODE_BY_MFG = $request->is_barcode_by_mfg ? 1 : 0;
            $prod->NARRATION = $request->narration;
            $prod->SHORT_NARRATION = $request->short_narration;
            $prod->PROMOTIONAL_MESSAGE = $request->promotional_message;
            $prod->F_PRIMARY_IMG_VARIANT_ID = null;
            $prod->PRIMARY_IMG_RELATIVE_PATH = null;
            $prod->REGULAR_PRICE = $request->price;
            $prod->INSTALLMENT_PRICE = $request->price_ins;
            $prod->SEA_FREIGHT_CHARGE = $request->sea_freight;
            $prod->AIR_FREIGHT_CHARGE = $request->air_freight;
            $prod->PREFERRED_SHIPPING_METHOD = $request->def_shipping_method;
            $prod->LOCAL_POSTAGE = $request->local_postage;
            $prod->INTER_DISTRICT_POSTAGE = $request->int_postage;
            $prod->F_VAT_CLASS = $request->vat_class;
            $prod->VAT_AMOUNT_PERCENT = $vat_amount;
            $prod->update();

            if ($request->file('images')) {
                $i = 0;
                foreach ($request->file('images') as $key => $image) {
                    $file_nam = 'prod_' . date('dmY') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();

                    $filename = $image->getClientOriginalExtension();
                    $destinationPath1 = 'media/images/products/' . $prd_no;
                    $destinationPath2 = 'media/images/products/' . $prd_no . '/thumb';
                    if (!file_exists($destinationPath1)) {
                        mkdir($destinationPath1, 0755, true);
                    }

                    if (!file_exists($destinationPath2)) {
                        mkdir($destinationPath2, 0755, true);
                    }

                    $img = Image::make($image->getRealPath());
                    $file_name1 = 'prod_' . date('dmY') . '_' . uniqid() . '.' . $filename;
                    $file_name2 = 'prod_' . date('dmY') . '_' . uniqid() . '.webp';
                    Image::make($img)->save($destinationPath1 . '/' . $file_name1);
                    Image::make($img)->encode('webp', 100)->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                        // $constraint->upsize();
                    })->save($destinationPath2 . '/' . $file_name2);
                    $image_url = $destinationPath1 . '/' . $file_name1;
                    $thumb_url = $destinationPath2 . '/' . $file_name2;

                    $img_lib = new ProdImgLib();
                    $img_lib->F_PRD_VARIANT_NO = $prod->PK_NO;
                    $img_lib->IS_MASTER = 0;
                    $img_lib->F_FILE_TYPE = 1;
                    $img_lib->RELATIVE_PATH = '/' . $image_url;
                    $img_lib->THUMB_PATH = '/' . $thumb_url;
                    $img_lib->SERIAL_NO = $i;
                    $img_lib->save();

                    //$image->move(public_path().'/media/images/products/'.$prd_no.'/', $file_name);

                    if ($prod->PRIMARY_IMG_RELATIVE_PATH == null) {
                        if ($i == 0) {
                            $def_relative_path = $image_url;
                            $def_relative_id = $img_lib->PK_NO;

                        }
                        $update_prod = ProductVariant::find($id);
                        $update_prod->F_PRIMARY_IMG_VARIANT_ID = $def_relative_id ?? null;
                        $update_prod->PRIMARY_IMG_RELATIVE_PATH = $def_relative_path ?? null;
                        $update_prod->update();
                    }
                    $i++;
                }
            } else {
                $img_lib = ProdImgLib::select('RELATIVE_PATH')->where('F_PRD_VARIANT_NO', $id)->where('SERIAL_NO', 0)->first();

                if ($img_lib) {
                    $prod->PRIMARY_IMG_RELATIVE_PATH = $img_lib->RELATIVE_PATH;
                    $prod->update();
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Product variant not updated successfully !', 'admin.product.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Product variant has been updated successfully !', 'admin.product.list');
    }

    public function getDeleteProductVariant(int $id)
    {
        DB::begintransaction();

        try {
            $product = ProductVariant::find($id)->delete();

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete product variant !', 'admin.product.list');
        }

        DB::commit();

        return $this->formatResponse(true, 'Successfully delete product !', 'admin.product.list');
    }

    */


}
