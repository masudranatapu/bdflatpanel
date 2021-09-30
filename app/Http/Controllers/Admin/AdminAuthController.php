<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Session;
use App\User;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\AuthUserGroup;
use App\Http\Controllers\Controller;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\Artisan;
use App\Http\Requests\Admin\LoginRequest;

class AdminAuthController extends Controller
{
    /**
     * the model instance
     * @var User
     */
    protected $user;
    /**
     * The Guard implementation.
     *
     * @var Authenticator
     */
    protected $auth;

    /**
     * Create a new authentication controller instance.
     *
     * @param  Authenticator  $auth
     * @return void
     */

    public function __construct(Guard $auth, User $user)
    {
        $this->user = $user;
        $this->auth = $auth;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
      public function getLogin()
      {
          if (! $this->auth->check()) {
              return view('admin.auth.login');
          } else {
              return redirect('admin');
          }
      }

      /**
       * Show the form for creating a new resource.
       *
       * @return Response
       */
    public function getLogout() {

        Session::flush();
        $this->auth->logout();

        return redirect('login');
    }

    public function postLogin(LoginRequest $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = \App\Models\Auth::where(['EMAIL' => $email])->where(function ($query) {
            $query->where('USER_TYPE', 0);
        })->first();

        $credentials = $request->only('EMAIL', 'password');
        if ($this->hasExist($user)) {
            $remember_me  = ( !empty( $request->remember_me ) ) ? true : false;

            // if ($this->auth->attempt($credentials, $request->has('remember')))
            if ($this->auth->attempt(['EMAIL' => $email, 'password' => $password], $remember_me))
            {


                // echo '<pre>';
                // echo '======================<br>';
                // print_r(Auth::user());
                // echo '<br>======================<br>';
                // exit();
                //  $admin_user = AdminUser::where('member_id', get_logged_user_id())->first();
                //   session(['profile_pic' => $admin_user->profile_pic]);
                return redirect('dashboard');
            }
        }

        return redirect()->back()->withInput()->withErrors([
            'EMAIL' => 'The credentials you entered did not match our records. Try again?',
        ]);

    }

    private function hasExist($user_array){
        if (! empty($user_array )) return true;
        return false;
    }

    public function getAgentLockScreen()
    {
        if (! $this->auth->check()) return redirect('/login');
        else if (Session::has('email')) return view('agent/auth/lock-screen')->withUser([
            'email' => Session::get('email'),
            'profile_pic' => Session::get('profile_pic'),
            'name' => get_logged_user_name(),
        ]);
        else return redirect('/');
    }

    public function getLockScreen()
    {
        if (! $this->auth->check()) return redirect('/admin/login');
        else if (Session::has('email')) return view('admin/auth/lock-screen')->withUser([
            'email' => Session::get('email'),
            'profile_pic' => Session::get('profile_pic'),
            'name' => get_logged_user_name(),
        ]);
        else return redirect('admin');
    }

    public function postLockScreen(LoginRequest $request)
    {
        $email = $request->input('email');
        if ($email == '') return redirect('/login');
        $password = $request->input('password');

        $user = Member::where(['email' => $email])->where(function ($query) {
            $query->where('user_type', 0)
                ->orWhere('user_type', 4);
        })->first();

        if ($this->hasExist($user)) {
            if ($this->auth->attempt(['email' => $email, 'password' => $password, 'user_type' => $user->user_type], $request->has('remember'))) {
                session([
                    'email'         => $email,
                    'last_active'   => date('Y-m-d H:i:s'),
                    'user_type'     => $user->user_type,
                    'has_company'   => 0
                ]);

                if ($user->user_type == '0') {
                    $admin_user = AdminUser::where('member_id', get_logged_user_id())->first();
                    session(['profile_pic' => $admin_user->profile_pic]);
                    return redirect('/admin');

                } else if ($user->user_type == '4') {
                    $agent_user = Agent::where('member_id', get_logged_user_id())->first();
                    session(['profile_pic' => $agent_user->profile_pic]);
                    session(['has_company' => $agent_user->has_company]);
                    return redirect('/');
                }
            }
        }

        return redirect()->back()->withErrors([
            'email' => 'The credentials you entered did not match our records. Try again?',
        ]);
    }
    /*++++++++++++++++ For data test ++++++++++++++++++++++++*/
    public function dataTest(Request $request)
    {
        $data = DB::SELECT(" SELECT PRD_MASTER_SETUP.PK_NO, PRD_MASTER_SETUP.F_PRD_SUB_CATEGORY_ID, PRD_SUB_CATEGORY.F_PRD_CATEGORY_NO FROM PRD_MASTER_SETUP LEFT JOIN PRD_SUB_CATEGORY ON PRD_SUB_CATEGORY.PK_NO = PRD_MASTER_SETUP.F_PRD_SUB_CATEGORY_ID LIMIT 10");

        foreach ($data as $key => $value) {
            DB::SELECT(" UPDATE PRD_MASTER_SETUP SET F_PRD_CATEGORY_ID = $value->F_PRD_CATEGORY_NO where PK_NO = $value->PK_NO ");
        }

        echo '<pre>';
        echo '======================<br>';
        print_r($data);
        echo '<br>======================<br>';
        exit();
    }






    public function clear()
    {
        Artisan::call('route:cache');
        Artisan::call('config:cache');
        Artisan::call('config:clear');
        Artisan::call('view:cache');
        Artisan::call('view:clear');
        return 'OK';
    }




}
