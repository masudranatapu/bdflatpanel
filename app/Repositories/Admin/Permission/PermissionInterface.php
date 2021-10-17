<?php

namespace App\Repositories\Admin\Permission;


interface PermissionInterface
{
    public function getPaginatedList($request, int $per_page = 20);
    public function getList();
    public function postStore($request);
    public function postUpdate($request, int $id);
    public function getShow(int $id);
    public function delete(int $id);
}
