<?php

namespace App\Http\Helpers;

use Illuminate\Database\Eloquent\Collection;

use App\Models\ProjectCategory;

/**
 * Class ProjectCategoryHelper
 * @package App\Http\Helpers
 */
class ProjectCategoryHelper
{
    /**
     * Retrieve all ProjectCategory
     *
     * @return ProjectCategory[]|Collection
     */
    public function all($term = null)
    {
        if ($term) {
            return ProjectCategory::where('name', 'like', '%' . $term . '%')->get();
        } else {
            return ProjectCategory::all();
        }
    }

    /**
     * [add description]
     * @param [type] $name [description]
     */
    public function add($name, $color)
    {
    	$category = new ProjectCategory;
    	$category->name = $name;
    	$category->color = $color;
    	$category->save();

    	return $category;
    }

    /**
     * [delete description]
     * @param [type] $id [description]
     */
    public function delete($id)
    {
    	$category = ProjectCategory::find($id);

    	if($category){
    		$category->delete();
    	}

    	return 'Project category deleted';
    }

}
