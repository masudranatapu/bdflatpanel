<?php

namespace App\Repositories\Admin\Pages;

use App\Models\Pages;
use App\Models\PagesCategory;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;

class PagesAbstract implements PagesInterface
{
    use RepoResponse;

    protected $status;
    protected $msg;

    public function getPages($limit = 2000): object
    {
        $pages = Pages::with('pageCategory')->orderByDesc('ORDER_ID')->paginate($limit);
        return $this->formatResponse(true, '', '', $pages);
    }

    public function getPage(int $id): object
    {
        $page = Pages::with('pageCategory')->find($id);
        return $this->formatResponse(true, '', '', $page);
    }

    public function storePage($request): object
    {
        $this->status = false;
        $this->msg = 'Page could not be added!';

        DB::beginTransaction();
        try {
            $page = new Pages();
            $page->F_PAGE_CATEGORY_NO = $request->page_category;
            $page->TITLE = $request->page_title;
            $page->URL_SLUG = env('APP_URL') . '/page/' . $request->page_url;
            $page->SEARCH_URL = $request->search_url;
            $page->ORDER_ID = $request->order_id;
            $page->META_DESCRIPTION = $request->meta_description;
            $page->META_KEYWARDS = $request->meta_keywords;
            $page->IS_ACTIVE = $request->status;
            $page->IS_BOTTOM_VIEW = $request->view_on_bottom_list ? 1 : 0;

            if ($request->hasFile('images')) {
                $image = $request->file('images')[0];
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = '/uploads/pages/';
                $image->move(public_path($imagePath), $imageName);

                $page->IMAGE_PATH = $imagePath . $imageName;
            }
            $page->save();

            $this->status = true;
            $this->msg = 'Page added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages.list');
    }

    public function updatePage($request, int $id)
    {
        $this->status = false;
        $this->msg = 'Page could not be updated!';

        DB::beginTransaction();
        try {
            $page = Pages::find($id);
            $page->F_PAGE_CATEGORY_NO = $request->page_category;
            $page->TITLE = $request->page_title;
            $page->URL_SLUG = env('APP_URL') . '/page/' . $request->page_url;
            $page->SEARCH_URL = $request->search_url;
            $page->ORDER_ID = $request->order_id;
            $page->META_DESCRIPTION = $request->meta_description;
            $page->META_KEYWARDS = $request->meta_keywords;
            $page->IS_ACTIVE = $request->status;
            $page->IS_BOTTOM_VIEW = $request->view_on_bottom_list ? 1 : 0;

            if ($request->hasFile('images')) {
                $image = $request->file('images')[0];
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = '/uploads/pages/';
                $image->move(public_path($imagePath), $imageName);
                $page->IMAGE_PATH = $imagePath . $imageName;
            }
            $page->save();

            $this->status = true;
            $this->msg = 'Page updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages.list');
    }

    public function deletePage(int $id): object
    {
        $this->status = false;
        $this->msg = 'Page could not be deleted!';

        DB::beginTransaction();
        try {
            $category = Pages::find($id);
            $category->delete();

            $this->status = true;
            $this->msg = 'Page deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages.list');
    }

    public function getPagesCategories($limit = 2000): object
    {
        $categories = PagesCategory::orderByDesc('PROPERTY_FOR')->paginate($limit);
        return $this->formatResponse(true, '', '', $categories);
    }

    public function getPagesCategory($id)
    {
        $category = PagesCategory::find($id);
        return $this->formatResponse(true, '', '', $category);
    }

    public function storePagesCategory($request): object
    {
        $this->status = false;
        $this->msg = 'Page category could not be added!';

        DB::beginTransaction();
        try {
            $category = new PagesCategory();
            $category->NAME = $request->category_name;
            $category->IS_ACTIVE = $request->status;
            $category->META_KEYWARDS = $request->meta_keywords;
            $category->META_DESCRIPTION = $request->meta_description;
            $category->ORDER_ID = $request->order_id;
            $category->PROPERTY_FOR = $request->property_for;
            $category->save();

            $this->status = true;
            $this->msg = 'Page category added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages-category.list');
    }

    public function updatePagesCategory($request, int $id)
    {
        $this->status = false;
        $this->msg = 'Page category could not be updated!';

        DB::beginTransaction();
        try {
            $category = PagesCategory::find($id);
            $category->NAME = $request->category_name;
            $category->IS_ACTIVE = $request->status;
            $category->META_KEYWARDS = $request->meta_keywords;
            $category->META_DESCRIPTION = $request->meta_description;
            $category->ORDER_ID = $request->order_id;
            $category->PROPERTY_FOR = $request->property_for;
            $category->save();

            $this->status = true;
            $this->msg = 'Page category updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages-category.list');
    }

    public function deletePagesCategory(int $id)
    {
        $this->status = false;
        $this->msg = 'Page category could not be deleted!';

        DB::beginTransaction();
        try {
            $category = PagesCategory::find($id);
            $category->delete();

            $this->status = true;
            $this->msg = 'Page category deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.pages-category.list');
    }
}
