<?php


namespace App\Repositories\Admin\PropertyCondition;


use App\Models\PropertyCondition;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyConditionAbstract implements PropertyConditionInterface
{
    use RepoResponse;

    protected $status;
    protected $msg;

    public function getPropertyConditions($limit = 2000): object
    {
        $conditions = PropertyCondition::orderBy('ORDER_ID', 'DESC')->paginate($limit);
        return $this->formatResponse(true, '', 'admin.property-condition', $conditions);
    }

    public function getPropertyCondition(int $id): object
    {
        $conditions = PropertyCondition::find($id);
        return $this->formatResponse(true, '', 'admin.property-condition', $conditions);
    }

    public function postStore($request)
    {
        $this->status = false;
        $this->msg = 'Property condition could not be added!';

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->property_condition);
            $check = PropertyCondition::where('URL_SLUG', $slug)->orderByDesc('PK_NO')->first();

            if ($check) {
                $slug = $slug . ('-' . ($check->PK_NO + 1));
            }

            $condition = new PropertyCondition();
            $condition->PROD_CONDITION = $request->property_condition;
            $condition->URL_SLUG = $slug;
            $condition->IS_ACTIVE = $request->status;
            $condition->ORDER_ID = $request->order_id;
            $condition->save();

            $this->status = true;
            $this->msg = 'Property condition added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.condition');
    }

    public function postUpdate($request, int $id)
    {
        $this->status = false;
        $this->msg = 'Property condition could not be updated!';

        DB::beginTransaction();
        try {
            $slug = Str::slug($request->property_condition);
            $check = PropertyCondition::where('URL_SLUG', $slug)->orderByDesc('PK_NO')->first();

            if ($check) {
                $slug = $slug . ('-' . ($check->PK_NO + 1));
            }

            $condition = PropertyCondition::find($id);
            $condition->PROD_CONDITION = $request->property_condition;
            $condition->URL_SLUG = $slug;
            $condition->IS_ACTIVE = $request->status;
            $condition->ORDER_ID = $request->order_id;
            $condition->save();

            $this->status = true;
            $this->msg = 'Property condition updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($this->status, $this->msg, 'admin.property.condition');
    }

    public function getDelete(int $id)
    {
        // TODO: Implement getDelete() method.
    }
}
