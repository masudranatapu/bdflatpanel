<?php

namespace App\Repositories\Admin\Owner;

use App\Models\Owner;
use App\Models\Product;
use App\Models\Customer;
use App\Models\OwnerInfo;
use App\Models\Transaction;
use App\Traits\RepoResponse;
use App\Models\CustomerAddress;
use App\Models\PaymentCustomer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OwnerAbstract implements OwnerInterface
{
    use RepoResponse;

    protected $owner;

    public function __construct(Owner $owner)
    {
        $this->owner = $owner;
    }

    public function getPaginatedList($request)
    {
        $data = $this->owner->where('STATUS', '!=', 3);
        if ($request->owner) {
            $data->where('USER_TYPE', $request->owner);
        } else {
            $data->whereNotIn('USER_TYPE', [1, 5]);
        }
        $data = $data->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.owner.index', $data);
    }

    public function getShow(int $id)
    {
        $owner = Owner::with(['properties', 'info'])->find($id);
        return $this->formatResponse(true, '', '', $owner);
    }

    public function postUpdate($request, $id): object
    {
        $status = false;
        $msg = 'User not updated!';

        DB::beginTransaction();
        try {
            $user = Owner::with(['info'])->find($id);

            if ($user->USER_TYPE == 2) {
                $user->NAME = $request->name;
                $user->EMAIL = $request->email;
                $user->MOBILE_NO = $request->mobile_no;
                $user->LISTING_LIMIT = $request->listing_limit;

                $info = $user->info;
                if (!$info) {
                    $info = new OwnerInfo();
                    $info->F_USER_NO = $user->PK_NO;
                }

                if($request->site_url){
                    $check = DB::table('WEB_USER_INFO')->where('SITE_URL',$request->site_url)->first();
                    if($check){
                        $msg = 'This site url already used for another owner!';
                        return $this->formatResponse($status, $msg, 'admin.owner.list');
                    }else{
                        $info->SITE_URL = $request->site_url;
                        $info->IS_LOCKED_SITE_URL = 1;
                    }
                }

                $info->SHOP_OPEN_TIME = $request->open_time;
                $info->SHOP_CLOSE_TIME = $request->close_time;
                $info->WORKING_DAYS = json_encode($request->working_days);
                $info->save();

                if ($request->hasFile('images')) {
                    $user->PROFILE_PIC_URL = $this->uploadImage($request->file('images')[0], $user->PK_NO);
                }
            } else {
                $user->NAME = $request->company_name;
                $user->EMAIL = $request->email;
                $user->MOBILE_NO = $request->mobile_no;
                $user->LISTING_LIMIT = $request->listing_limit;
                $user->DESIGNATION = $request->designation;
                $user->CONTACT_PER_NAME = $request->contact_person_name;
                $user->ADDRESS = $request->office_address;

                $info = $user->info;
                if (!$info) {
                    $info = new OwnerInfo();
                    $info->F_USER_NO = $user->PK_NO;
                }

                $info->META_TITLE = $request->meta_title;
                $info->META_DESCRIPTION = $request->meta_description;

                $info->ABOUT_COMPANY = $request->about_company;
                $info->SHOP_OPEN_TIME = $request->open_time;
                $info->SHOP_CLOSE_TIME = $request->close_time;
                $info->WORKING_DAYS = json_encode($request->working_days);

                if($request->site_url){
                    $check = DB::table('WEB_USER_INFO')->where('SITE_URL',$request->site_url)->first();
                    if($check){
                        $msg = 'This site url already used for another owner!';
                        return $this->formatResponse($status, $msg, 'admin.owner.list');
                    }else{
                        $info->SITE_URL = $request->site_url;
                        $info->IS_LOCKED_SITE_URL = 1;
                    }
                }

                if ($request->hasFile('images')) {
                    $imgMap = ['LOGO', 'BANNER'];
                    foreach ($request->file('images') as $key => $image) {
                        if ($key >= count($imgMap)) {
                            break;
                        }
                        $info->{$imgMap[$key]} = $this->uploadImage($image, $user->PK_NO);
                    }
                }

                $info->save();
            }

            $user->PAYMENT_AUTO_RENEW = $request->auto_payment_renew;
            $user->IS_FEATURE = $request->feature;
            $user->USER_TYPE = $request->user_type;

            if ($request->auto_payment_renew == 1) {
                $user->properties()->update([
                    'PAYMENT_AUTO_RENEW' => 1
                ]);
            }

            if($user->USER_TYPE != $request->user_type){
                Product::where('F_USER_NO',$id)->update(['USER_TYPE' => $request->user_type]);
            }
            $user->save();
            $status = true;
            $msg = 'User updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
        }
        DB::commit();
        return $this->formatResponse($status, $msg, 'admin.owner.list');
    }

    public function updatePassword($request, $id)
    {
        $status = false;
        $msg = 'Password could not be updated!';

        DB::beginTransaction();
        try {
            $user = Owner::find($id);
            $user->PASSWORD = Hash::make($request->password);
            $user->save();

            $status = true;
            $msg = 'Password updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'admin.owner.list');
    }

    private function uploadImage($image, $id = null): string
    {
        $imageUrl = '';
        if ($image) {
            $file_name = 'img_' . date('dmY') . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imageUrl = '/uploads/images/owner/' . ($id ? $id . '/' : '');
            $image->move(public_path($imageUrl), $file_name);
            $imageUrl .= $file_name;
        }
        return $imageUrl;
    }

    public function getPayments(int $id)
    {
        $payments = PaymentCustomer::with('customer')
            ->where('F_CUSTOMER_NO', '=', $id)
            ->get();
        return $this->formatResponse(true, '', 'admin.owner.payment', $payments);
    }

    public function getCustomerTxn($id)
    {
        try {
            $data = Transaction::with(['payment', 'customer'])->where('F_CUSTOMER_NO', $id)->get();
        } catch (\Throwable $th) {
            return $this->formatResponse(false, 'Data not found', 'admin.owner.list');
        }
        return $this->formatResponse(true, 'Payment list found successfully !', 'admin.owner.list', $data);

    }

    public function storePayment($request, int $id)
    {
        $status = false;
        $msg = 'Payment not successful!';

        DB::beginTransaction();
        try {
            $payment = new PaymentCustomer();
            $payment->F_CUSTOMER_NO = $id;
            $payment->AMOUNT = $request->amount;
            $payment->F_ACC_PAYMENT_BANK_NO = 3;
            $payment->PAYMENT_CONFIRMED_STATUS = 1;
            $payment->PAYMENT_NOTE = $request->note;
            $payment->PAYMENT_DATE = date('Y-m-d');
            $payment->PAYMENT_TYPE = 2;
            $payment->save();

            $status = true;
            $msg = 'Payment successful!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'admin.owner.payment');
    }

    public function postRecharge($request, int $id): object
    {
        $status = false;
        $msg = 'Recharge not successful!';

        DB::beginTransaction();
        try {
            $payment = new PaymentCustomer();
            $payment->F_CUSTOMER_NO = $id;
            $payment->AMOUNT = $request->amount;
            $payment->F_ACC_PAYMENT_BANK_NO = $request->payment_account ?? 4;
            $payment->PAYMENT_CONFIRMED_STATUS = 1;
            $payment->PAYMENT_NOTE = $request->note;
            $payment->PAYMENT_DATE = date('Y-m-d', strtotime($request->payment_date));
            $payment->PAYMENT_TYPE = $request->payment_type;
            $payment->SLIP_NUMBER = $request->slip_number;

            if ($request->hasFile('images')) {
                $file = $request->file('images')[0];
                $file_name = uniqid() . '.' . $file->getClientOriginalExtension();
                $file_path = 'uploads/attachments/' . $id . '/';
                $file->move(public_path($file_path), $file_name);

                $payment->ATTACHMENT_PATH = $file_path . $file_name;
            }
            $payment->save();

            $status = true;
            $msg = 'Recharge successful!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'admin.owner.payment');
    }

    public function getTransaction($id): object
    {
        $transaction = Transaction::with(['customer', 'payment'])->find($id);
        return $this->formatResponse(true, '', '', $transaction);
    }

    /*

    public function getShow(int $id)
    {
        $data =  Reseller::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.reseller.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.reseller.list', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $mobile = (int)$request->phone;
            $check_customer = Customer::where('MOBILE_NO',$mobile)->first();
            $check_reseller = Reseller::where('MOBILE_NO',$mobile)->first();
            if($check_customer){
                return $this->formatResponse(false, 'This mobile no existed in customer table', 'admin.reseller.create');
            }
            if($check_reseller){
                return $this->formatResponse(false, 'This mobile no existed in reseller table', 'admin.reseller.create');
            }
            $reseller                       = new Reseller();
            $reseller->NAME                 = str_replace("’","'",$request->name);
            $reseller->MOBILE_NO            = $mobile;
            $reseller->ALTERNATE_NO         = $request->alt_phone;
            $reseller->EMAIL                = $request->email;
            $reseller->FB_ID                = $request->fb_id;
            $reseller->IG_ID                = $request->ig_id;
            $reseller->UKSHOP_ID            = $request->uk_id;
            $reseller->UKSHOP_PASS          = bcrypt($request->uk_pass);
            $reseller->DISCOUNT_PERCENTAGE  = $request->discount;
            $reseller->ADDRESS_LINE_1       = $request->address1;
            $reseller->ADDRESS_LINE_2       = $request->address2;
            $reseller->ADDRESS_LINE_3       = $request->address3;
            $reseller->ADDRESS_LINE_4       = $request->address4;
            $reseller->CITY                 = $request->city;
            $reseller->STATE                = $request->state;
            $reseller->POST_CODE            = $request->postcode;
            $reseller->F_COUNTRY_NO         = $request->country;
            $reseller->F_PREFERRED_AGENT_NO = $request->agent;
            $reseller->IS_ACTIVE            = 1;
            $reseller->save();

            $reseller_add                           = new CustomerAddress();
            $reseller_add->NAME                     = str_replace("’","'",$request->name);
            $reseller_add->TEL_NO                   = $mobile;
            $reseller_add->ADDRESS_LINE_1           = $request->address1;
            $reseller_add->ADDRESS_LINE_2           = $request->address2;
            $reseller_add->ADDRESS_LINE_3           = $request->address3;
            $reseller_add->ADDRESS_LINE_4           = $request->address4;
            $reseller_add->F_COUNTRY_NO             = $request->country;
            $reseller_add->STATE                    = $request->state;
            $reseller_add->CITY                     = $request->city;
            $reseller_add->POST_CODE                = $request->postcode;
            $reseller_add->F_ADDRESS_TYPE_NO        = 1;
            $reseller_add->F_RESELLER_NO            = $reseller->PK_NO;
            $reseller_add->IS_ACTIVE                = 1;
            $reseller_add->IS_DEFAULT               = 1;
            $reseller_add->save();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.reseller.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Reseller has been created successfully !', 'admin.reseller.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();
            try {

                $reseller                       = Reseller::where('PK_NO', $PK_NO)->first();
                $reseller->NAME                 = str_replace("’","'",$request->name);
                $reseller->MOBILE_NO            = (int)$request->phone;
                $reseller->ALTERNATE_NO         = $request->alt_phone;
                $reseller->EMAIL                = $request->email;
                $reseller->FB_ID                = $request->fb_id;
                $reseller->IG_ID                = $request->ig_id;
                $reseller->UKSHOP_ID            = $request->uk_id;
                $reseller->UKSHOP_PASS          = bcrypt($request->uk_pass);
                $reseller->DISCOUNT_PERCENTAGE  = $request->discount;
                $reseller->ADDRESS_LINE_1       = $request->address1;
                $reseller->ADDRESS_LINE_2       = $request->address2;
                $reseller->ADDRESS_LINE_3       = $request->address3;
                $reseller->ADDRESS_LINE_4       = $request->address4;
                $reseller->CITY                 = $request->city;
                $reseller->STATE                = $request->state;
                $reseller->POST_CODE            = $request->postcode;
                $reseller->F_COUNTRY_NO         = $request->country;
                $reseller->F_PREFERRED_AGENT_NO = $request->agent;
                $reseller->save();

            } catch (\Exception $e) {

                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.reseller.list');
            }

            DB::commit();
            return $this->formatResponse(true, 'Reseller Informstion has been Updated successfully', 'admin.reseller.list');
    }

    public function delete($PK_NO)
    {
        $reseller = Reseller::where('PK_NO',$PK_NO)->first();
        $reseller->IS_ACTIVE = 0;
        if ($reseller->update()) {
            return $this->formatResponse(true, 'Successfully deleted Reseller Account', 'admin.reseller.list');
        }
        return $this->formatResponse(false,'Unable to delete Reseller Account','admin.reseller.list');
    }

    */
}
