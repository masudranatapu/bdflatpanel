<?php


namespace App\Repositories\Admin\PropertyFeatures;


use App\Models\FloorList;
use App\Models\ListingFeatures;
use App\Models\PropertyFacing;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyFeaturesAbstract implements PropertyFeaturesInterface
{
    use RepoResponse;

    protected $status;
    protected $msg;

    public function getFeatures($limit = 2000): object
    {
        $features = ListingFeatures::orderBy('ORDER_ID', 'DESC')->paginate($limit);
        return $this->formatResponse(true, '', '', $features);
    }

    public function getFeature(int $id): object
    {
        $feature = ListingFeatures::find($id);
        return $this->formatResponse(true, '', '', $feature);
    }

    public function postStore($request)
    {
        $this->status = false;
        $this->msg = 'Feature not added!';

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->title);
            $check = ListingFeatures::where('URL_SLUG', '=', $slug)->first();
            if ($check) {
                $slug .= '-' . (ListingFeatures::max('PK_NO') + 1);
            }

            $feature = new ListingFeatures();
            $feature->TITLE = $request->title;
            $feature->ORDER_ID = $request->order_id;
            $feature->IS_ACTIVE = $request->status;
            $feature->URL_SLUG = $slug;
            if ($request->hasFile('icon')) {
                $image = $request->file('icon');
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/listings/features/';
                $image->move($image_path, $image_name);
                $feature->ICON = $image_path . $image_name;
            }
            $feature->save();

            $this->status = true;
            $this->msg = 'Feature added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.features');
    }

    public function postUpdate($request, int $id): object
    {
        $this->status = false;
        $this->msg = 'Feature not updated!';

        DB::beginTransaction();
        try {
            $feature = ListingFeatures::find($id);
            $feature->TITLE = $request->title;
            $feature->ORDER_ID = $request->order_id;
            $feature->IS_ACTIVE = $request->status;
            if ($request->hasFile('icon')) {
                $image = $request->file('icon');
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/listings/features/';
                $image->move($image_path, $image_name);
                $feature->ICON = $image_path . $image_name;
            }
            $feature->save();

            $this->status = true;
            $this->msg = 'Feature updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.features');
    }

    public function getFacings($limit = 2000): object
    {
        $facings = PropertyFacing::orderBy('ORDER_ID', 'DESC')->paginate($limit);
        return $this->formatResponse(true, '', '', $facings);
    }

    public function getFacing(int $id): object
    {
        $facing = PropertyFacing::find($id);
        return $this->formatResponse(true, '', '', $facing);
    }

    public function storeFacing($request)
    {
        $this->status = false;
        $this->msg = 'Facing not added!';

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->title);
            $check = PropertyFacing::where('URL_SLUG', '=', $slug)->first();
            if ($check) {
                $slug .= '-' . (PropertyFacing::max('PK_NO') + 1);
            }

            $facing = new PropertyFacing();
            $facing->TITLE = $request->title;
            $facing->ORDER_ID = $request->order_id;
            $facing->IS_ACTIVE = $request->status;
            $facing->URL_SLUG = $slug;
            $facing->save();

            $this->status = true;
            $this->msg = 'Facing added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.facing');
    }

    public function updateFacing($request, int $id): object
    {
        $this->status = false;
        $this->msg = 'Facing not updated!';

        DB::beginTransaction();
        try {
            $facing = PropertyFacing::find($id);
            $facing->TITLE = $request->title;
            $facing->ORDER_ID = $request->order_id;
            $facing->IS_ACTIVE = $request->status;
            $facing->save();

            $this->status = true;
            $this->msg = 'Facing updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.facing');
    }

    public function getFloors($limit = 2000): object
    {
        $floors = FloorList::orderBy('ORDER_ID', 'DESC')->paginate($limit);
        return $this->formatResponse(true, '', '', $floors);
    }

    public function getFloor(int $id): object
    {
        $floor = FloorList::find($id);
        return $this->formatResponse(true, '', '', $floor);
    }

    public function storeFloor($request): object
    {
        $this->status = false;
        $this->msg = 'Floor not added!';

        DB::beginTransaction();
        try {
            $floor = new FloorList();
            $floor->NAME = $request->name;
            $floor->ORDER_ID = $request->order_id;
            $floor->IS_ACTIVE = $request->status;
            $floor->save();

            $this->status = true;
            $this->msg = 'Floor added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.floor');
    }

    public function updateFloor($request, int $id): object
    {
        $this->status = false;
        $this->msg = 'Floor not updated!';

        DB::beginTransaction();
        try {
            $floor = FloorList::find($id);
            $floor->NAME = $request->name;
            $floor->ORDER_ID = $request->order_id;
            $floor->IS_ACTIVE = $request->status;
            $floor->save();

            $this->status = true;
            $this->msg = 'Floor updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.floor');
    }
}
