<?php

namespace App\Repositories\Admin\WebInfo;

interface WebInfoInterface
{
         public function getPaginatedList($request, int $per_page = 5);
         public function postStore($request);
         public function postUpdate($request, int $id);
         public function getCityAddress($id);
         public function getPostageAddress($id);
         public function delete($id);
         public function postCityAddress($request,$id);
         public function postPostageAddress($request,$id);
         public function getCityList();
         public function getPostageList();
}
