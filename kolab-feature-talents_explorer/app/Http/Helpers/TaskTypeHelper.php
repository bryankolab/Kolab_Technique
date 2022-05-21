<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\TaskType;


/**
 * Class TaskType
 * @package App\Http\Helpers
 */
class TaskTypeHelper
{
    /**
     * Retrieve all TaskType
     *
     * @return TaskType[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return TaskType::where('name', 'like', '%' . $term . '%')->orderBy('name', 'ASC')->get();
        } else {
            return TaskType::orderBy('name', 'ASC')->get();
        }
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name)
    {
    	$type = new TaskType;
    	$type->name = $name;
    	$type->save();

    	return $type;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$type = TaskType::find($id);

    	if($type){
    		$type->delete();
    	}

    	return 'Task type deleted';
    }

}
