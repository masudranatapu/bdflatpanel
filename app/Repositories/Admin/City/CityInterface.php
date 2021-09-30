<?php


namespace App\Repositories\Admin\City;


interface CityInterface
{
    public function getCities($limit = 2000);

    public function getCity(int $id);

    public function postStore($request);

    public function postUpdate($request, int $id);
}
