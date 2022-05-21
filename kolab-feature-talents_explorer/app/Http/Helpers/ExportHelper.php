<?php

namespace App\Http\Helpers;

use Exception;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\User;
use App\Models\Export;
use Illuminate\Support\Facades\Request;

class ExportHelper
{

    /**
     * Get all exports
     * @param $request
     * @return Export[]|Collection [Object]
     */
    public function all($request = [])
    {
        $exports = Export::query();

        $exports = $exports->with('resolution')
         						->with('format')
         						->with('language');

        if(!empty($request['project_id'])){
        	$exports = $exports->where('project_id', $request['project_id']);
        }

        $exports = $exports->orderBy('id')->get();

        return $exports;
    }

    /**
     * Get export details
     * @param  [Integer] $id Export id
     * @return Export|Builder|Model|object|null [Object]
     */
    public function get($id)
    {
        return Export::where('id', $id)->first();
    }

    /**
     * Delete multiple exports
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delete($ids)
    {
    		foreach($ids as $id){
	        $export = Export::find($id);

	        try {
	          $export->delete();
	        } catch (Exception $e) {
	          _info($e->getMessage());
	        }
	      }

        return true;
    }

    /**
     * Add or update a export
     * @param
     * @return mixed [Object] Export
     */
    public function addOrUpdate($params)
    {
        $savedExports = [];

        $project = $params['project'];
        foreach ($params['exports'] as $exportParams) {
            $savedExports[] = $this->saveExport($project, $exportParams, (int)$params['user'][0]);
        }

        return $savedExports;
    }

    // -- Useful

    private function saveExport($project, $exportParams, $user = null)
    {
        $mandatoryParameters = [];

        // Check if all parameters are provided
        try {
            _helper('api')->checkParameters($exportParams, $mandatoryParameters);
        } catch (Exception $e) {
            // Return the exception message if error
            _helper('api')->error($e->getMessage());
        }

        if (array_key_exists('id', $exportParams) && $exportParams['id']) {
            $export = Export::find($exportParams['id']);
            $export->updated_by = $user;
        } else {
            $export = new Export();
            $export->created_by = $user;
    				$export->updated_by = $user;
        }

        $export->name = $exportParams['name'];
        $export->duration = $exportParams['duration'];
        $export->project_id = $project;
        $export->resolution_id = $exportParams['resolution']['id'];
        $export->format_id = !empty($exportParams['format']) ? $exportParams['format']['id'] : null;
        $export->format_raw = $exportParams['format_raw'];
        $export->language_id = $exportParams['language']['id'];

        try {
            $export->save();
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        try {
            $export->save();
        } catch (Exception $e) {
            _helper('api')->error($e->getMessage());
        }

        return $export;
    }
}
