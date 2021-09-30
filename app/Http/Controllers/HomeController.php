<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Repositories\Dashboard\DashboardRepoInterface;
use App\Http\Models\CustomUser as User;

class HomeController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $dashboard;

    public function __construct(DashboardRepoInterface $dashboard)
    {
        // $this->middleware('auth');
        $this->dashboard = $dashboard;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return redirect('admin')->with('success', 'Successfully reaches the dashboard !');
        // return back()->with('success', 'Item created successfully!');
        $data = $this->dashboard->getData();
        return view('home')->withData($data);
    }


    public function getTestApi(Request $request)
    {
        $users = User::all();
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully.');
    }
}
