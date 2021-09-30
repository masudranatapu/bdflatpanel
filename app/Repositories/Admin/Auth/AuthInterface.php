<?php

namespace App\Repositories\Admin\Auth;


interface AuthInterface
{
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function getShow(int $id);
    public function delete(int $id);
}
