<?php
namespace App\Repositories\Admin\Dashboard;


class DashboardAbstract implements DashboardInterface
{

    public function getData()
    {
        return [ 'title' => 'This text showing form repository :) '];
    }
}