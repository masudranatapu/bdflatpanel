<?php

namespace App\Repositories\Admin\Pages;

interface PagesInterface
{
    public function getPages($limit = 2000);

    public function getPage(int $id);

    public function storePage($request);

    public function updatePage($request, int $id);

    public function deletePage(int $id);

    public function getPagesCategories(int $limit = 2000);

    public function getPagesCategory($id);

    public function storePagesCategory($request);

    public function updatePagesCategory($request, int $id);

    public function deletePagesCategory(int $id);
}
