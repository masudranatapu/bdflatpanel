<?php


namespace App\Repositories\Admin\Area;


interface AreaInterface
{
    public function getAreas($limit = 2000);

    public function getArea(int $id);

    public function postStore($request);

    public function postUpdate($request, int $id);

    public function getCityAreas(int $id);

    public function getSubArea(int $id);
}
