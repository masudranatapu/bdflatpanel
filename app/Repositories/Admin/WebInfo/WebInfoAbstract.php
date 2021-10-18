<?php

namespace App\Repositories\Admin\WebInfo;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use App\Models\City;
use App\Models\State;
use App\Models\WebInfo;
use App\Models\Country;
use App\Models\PoCode;
use App\Traits\RepoResponse;


class WebInfoAbstract implements WebInfoInterface
{
    use RepoResponse;

    protected $webInfo;
    protected $state;
    protected $city;
    protected $country;
    protected $imageMap;

    public function __construct(WebInfo $webInfo, Country $country, State $state, City $city)
    {
        $this->webInfo = $webInfo;
        $this->state = $state;
        $this->city = $city;
        $this->country = $country;

        $this->imageMap = [
            'HEADER_LOGO',
            'FOOTER_LOGO',
            'APP_LOGO',
            'META_IMAGE',
            'FAVICON'
        ];
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->address->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.address_type.list', $data);
    }


    public function postStore($request): object
    {
        DB::beginTransaction();
        try {
            $webInfo = WebInfo::find(1);
            if (!$webInfo) {
                $webInfo = new WebInfo();
                $webInfo->PK_NO = 1;
            }

            $webInfo->TITLE                         = $request->title;
            $webInfo->DESCRIPTION                   = $request->description;
            $webInfo->PHONE_1                       = $request->phone_one;
            $webInfo->PHONE_2                       = $request->phone_two;
            $webInfo->EMAIL_1                       = $request->email_one;
            $webInfo->EMAIL_2                       = $request->email_two;
            $webInfo->HQ_ADDRESS                    = $request->hq_address;
            $webInfo->URL                           = $request->url;
            $webInfo->FACEBOOK_URL                  = $request->facebook_url;
            $webInfo->TWITTER_URL                   = $request->twitter_url;
            $webInfo->INSTAGRAM_URL                 = $request->instagram_url;
            $webInfo->YOUTUBE_URL                   = $request->youtube_url;
            $webInfo->PINTEREST_URL                 = $request->pinterest_url;
            $webInfo->WHATS_APP                     = $request->whatsapp;
            $webInfo->FB_APP_ID                     = $request->facebook_app_id;
            $webInfo->FACEBOOK_SECRET_ID            = $request->facebook_secret_id;
            $webInfo->GOOGLE_APP_ID                 = $request->google_app_id;
            $webInfo->GOOGLE_CLIENT_ID              = $request->google_client_id;
            $webInfo->GOOGLE_CLIENT_SECRET          = $request->google_client_secret;
            $webInfo->ANDROID_APP_LINK              = $request->android_app_link;
            $webInfo->ANDROID_APP_VERSION           = $request->android_app_version;
            $webInfo->ANALYTIC_ID                   = $request->analytic_id;
            $webInfo->LANGUAGE_ID                   = $request->language_id;
            $webInfo->IPHONE_APP_LINK               = $request->ios_app_link;
            $webInfo->IPHONE_APP_VERSION            = $request->ios_app_version;
            $webInfo->COPYRIGHT_TEXT                = $request->copyright_text;
            $webInfo->FEATURE_PROPERTY_LIMIT        = $request->feature_property_limit;
            $webInfo->ROOMMATE_PROPERTY_LIMIT       = $request->roommate_property_limit;
            $webInfo->RENT_PROPERTY_LIMIT           = $request->rent_property_limit;
            $webInfo->SALE_PROPERTY_LIMIT           = $request->sale_property_limit;
            $webInfo->VERIFIED_PROPERTY_LIMIT       = $request->verified_property_limit;
            $webInfo->SIMILAR_PROPERTY_LIMIT        = $request->similar_property_limit;
            $webInfo->LISTING_LEAD_CLAIMED_TIME     = $request->listing_lead_claimed_time;
            $webInfo->SEEKER_BONUS_BALANCE          = $request->seeker_bonus_amount;
            $webInfo->OWNER_BONUS_BALANCE           = $request->owner_bonus_amount;
            $webInfo->META_TITLE                    = $request->meta_title;
            $webInfo->META_KEYWARDS                 = $request->meta_keywords;
            $webInfo->META_DESCRIPTION              = $request->meta_description;
            $webInfo->DEFAULT_CI_PRICE              = $request->default_ci_price;

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    if ($key >= count($this->imageMap)) {
                        break;
                    }
                    $field = $this->imageMap[$key];
                    $webInfo->{$field} = $this->uploadImage($image);
                }
            }

            $webInfo->save();
        } catch (\Exception $e) {
            DB::rollback();
//            dd($e);
            return $this->formatResponse(false, $e->getMessage(), 'admin.generalinfo');
        }
        DB::commit();
        return $this->formatResponse(true, 'Web info has been updated successfully !', 'admin.generalinfo');
    }

    public function uploadImage($image): string
    {
        $imageUrl = '';
        if ($image) {
            $file_name = 'img_' . date('dmY') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imageUrl = '/uploads/images/generalInfo/';
            $image->move(public_path($imageUrl), $file_name);
            $imageUrl .= $file_name;
        }
        return $imageUrl;
    }

    public function findOrThrowException($id)
    {
        $data = $this->address->where('PK_NO', '=', $id)->first();

        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.address_type.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.address_type.list', null);
    }


    public function postUpdate($request, $id)
    {

        DB::beginTransaction();
        try {
            $webInfo = Category::find($id);
            $webInfo->META_TITLE = $request->meta_title;
            $webInfo->META_KEYWORDS = $request->meta_keywords;
            $webInfo->META_DESC = $request->meta_description;
            if (!is_null($request->file('fav_icon'))) {
                $webInfo->FAV_PATH = $this->uploadImage($request->fav_icon);
            }
            if (!is_null($request->file('site_logo'))) {
                $webInfo->LOGO_PATH = $this->uploadImage($request->site_logo);
            }

            $webInfo->update();


        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, $e, 'admin.generalinfo');
        }
        DB::commit();
        return $this->formatResponse(true, 'Web info has been updated successfully !', 'admin.generalinfo');
    }

    public function delete($id)
    {

        DB::begintransaction();
        try {
            $this->address->where('PK_NO', $id)->delete();


        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete this address type !', 'admin.address_type.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Successfully delete this  address type !', 'admin.address_type.list');
    }

    public function getCityAddress($id = null)
    {
        $data['city_details'] = null;
        if ($id != null) {
            $data['city_details'] = City::where('PK_NO', $id)->first();
        }
        $data['countryCombo'] = $this->country->getCountryCombo();
        $data['stateCombo'] = $this->state->getStateCombo();

        return $this->formatResponse(true, 'Data Found !', 'admin.address_type.list', $data);
    }

    public function getPostageAddress($id = null)
    {
        $data['postage_details'] = null;
        $data['countryCombo'] = $this->country->getCountryCombo();
        $data['stateCombo'] = $this->state->getStateCombo();

        if ($id != null) {
            $data['postage_details'] = PoCode::select('SS_PO_CODE.*', 'c.F_STATE_NO')
                ->join('SS_CITY as c', 'SS_PO_CODE.F_CITY_NO', 'c.PK_NO')
                ->where('SS_PO_CODE.PK_NO', $id)
                ->first();
            $data['cityCombo'] = $this->city->where('F_STATE_NO', $data['postage_details']->F_STATE_NO)->pluck('CITY_NAME', 'PK_NO');
        } else {
            $data['cityCombo'] = $this->city->where('F_STATE_NO', 1)->pluck('CITY_NAME', 'PK_NO');
        }
        return $this->formatResponse(true, 'Data Found !', 'admin.address_type.list', $data);
    }

    public function postCityAddress($request, $id)
    {
        DB::begintransaction();
        try {
            $state = State::select('STATE_NAME')->where('PK_NO', $request->state)->first();
            if ($id == 0) {
                $city = new City();
                $message = 'City Created Successfully !';
            } else {
                $city = City::find($id);
                $message = 'City Updated Successfully !';
            }
            $city->CITY_NAME = $request->city;
            $city->F_STATE_NO = $request->state;
            $city->STATE_NAME = $state->STATE_NAME;
            $city->F_COUNTRY_NO = $request->country;
            $city->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.address_type.list');
        }
        DB::commit();
        return $this->formatResponse(true, $message, 'admin.address_type.list');
    }

    public function postPostageAddress($request, $id)
    {
        DB::begintransaction();
        try {
            $city = City::select('CITY_NAME')->where('PK_NO', $request->city)->first();
            if ($id == 0) {
                $post_code = new PoCode();
                $message = 'Post Code Created Successfully !';
            } else {
                $post_code = PoCode::find($id);
                $message = 'Post Code Updated Successfully !';
            }
            $post_code->PO_CODE = $request->postage;
            $post_code->F_CITY_NO = $request->city;
            $post_code->CITY_NAME = $city->CITY_NAME;
            $post_code->F_COUNTRY_NO = $request->country;
            $post_code->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.address_type.list');
        }
        DB::commit();
        return $this->formatResponse(true, $message, 'admin.address_type.list');
    }

    public function getCityList()
    {
        $data = City::select('SS_CITY.*', 'c.NAME')
            ->leftjoin('SS_COUNTRY as c', 'c.PK_NO', 'SS_CITY.F_COUNTRY_NO')
            ->get();
        return $this->formatResponse(true, 'Data Found', 'admin.address_type.list', $data);
    }

    public function getPostageList()
    {
        $data = PoCode::select('SS_PO_CODE.PK_NO', 'SS_PO_CODE.PO_CODE', 'SS_PO_CODE.CITY_NAME', 'c.NAME', 'city.STATE_NAME')
            ->leftjoin('SS_COUNTRY as c', 'c.PK_NO', 'SS_PO_CODE.F_COUNTRY_NO')
            ->leftjoin('SS_CITY as city', 'city.PK_NO', 'SS_PO_CODE.F_CITY_NO')
            ->get();
        return $this->formatResponse(true, 'Data Found', 'admin.address_type.list', $data);
    }


}
