<?php
namespace App\Repositories\Admin\Auth;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\Member;
use App\Models\Auth;
use File;

class AuthAbstract implements AuthInterface
{
    use RepoResponse;

    public function postStore($request)
    {
        $auth               = new Auth();
        $auth->USERNAME     = $request->username;
        $auth->MOBILE_NO    = $request->mobile_no;
        $auth->EMAIL        = $request->email;
        $auth->PASSWORD     = Hash::make($request->password);
        $auth->GENDER       = $request->gender;
        $auth->CAN_LOGIN    = $request->can_login;
        $auth->STATUS       = $request->status;
        $auth->FIRST_NAME   = $request->first_name;
        $auth->LAST_NAME    = $request->last_name;
        $auth->DESIGNATION  = $request->designation;

        if ($request->profile_pic != '') {
            $file_name = 'profile_'. date('dmY'). '_' .time(). '.' . $request->profile_pic->getClientOriginalExtension();
            $request->profile_pic->move(public_path('media/images/profile/'), $file_name);
            $auth->PROFILE_PIC_URL = url('media/images/profile/'.$file_name);
            $auth->PROFILE_PIC = $file_name;
        }

        if ($auth->save()) {
            return $this->formatResponse(true, 'Member created successfully !', '', $auth->PK_NO);
        } else {
            return $this->formatResponse(false, 'Unable to created member !', '', 0);
        }
    }

    public function postUpdate($request, int $id, string $type = null)
    {
        $auth = Auth::where('PK_NO', $id)->first();
        $auth->MOBILE_NO = $request->mobile_no;
        $auth->GENDER = $request->gender;
        if ($type == 'single' && isset($request->password)) {
            $auth->PASSWORD = Hash::make($request->password);
        }else{
            $auth->USERNAME = $request->username;
            $auth->EMAIL = $request->email;
            $auth->CAN_LOGIN = $request->can_login;
            $auth->STATUS = $request->status;
        }
        $auth->FIRST_NAME = $request->first_name;
        $auth->LAST_NAME = $request->last_name;
        $auth->DESIGNATION = $request->designation;

        if ($request->profile_pic != '') {

            if(File::exists(public_path('media/images/profile/'.$auth->PROFILE_PIC))) {
                File::delete(public_path('media/images/profile/'.$auth->PROFILE_PIC));
            }
            $file_name = 'profile_'. date('dmY'). '_' .time(). '.' . $request->profile_pic->getClientOriginalExtension();
            $request->profile_pic->move(public_path('media/images/profile/'), $file_name);
            $auth->PROFILE_PIC_URL = url('media/images/profile/'.$file_name);
            $auth->PROFILE_PIC = $file_name;
        }
        $auth->UPDATED_AT = date('Y-m-d H:i:s');
        // $auth->update();
        // if (isset($request->password) && $request->password != '') {
        //         $auth->password = Hash::make($request->password);
        // }

        if ($auth->update()) {
            return $this->formatResponse(true, 'Member updated successfully !', '', $auth->id);
        } else {
            return $this->formatResponse(false, 'Unable to update member !', '', 0);
        }
    }

    public function getShow(int $id)
    {
        // TODO: Implement getShow() method.
    }

    public function delete(int $id)
    {
        // TODO: Implement delete() method.
    }
}
