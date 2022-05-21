<?php

namespace App\Http\Helpers;

use App\Models\Task;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

use App\User;
use Illuminate\Support\Facades\Request;
use App\Http\Helpers\NotificationHelper;

class TaskHelper
{

    /**
     * Get all tasks
     * @param $request
     * @return Task[]|Collection [Object]  Task
     */
    public function all($request = [])
    {
      $tasks = Task::query();

      // Filter by name
      if (_helper('api')->checkParameterExistAndNotEmpty('filter_name', $request)) {
        $tasks->where('name', 'LIKE', '%' . $request['filter_name'] . '%');
      }

      // Filter by categories
      if (_helper('api')->checkParameterExistAndNotEmpty('filter_categories', $request)) {
        $tasks->whereIn('category_id', $request['filter_categories']);
      }

      return $tasks->orderBy('id', 'ASC')->get();
    }

    /**
     * Get task details
     * @param  [Integer] $id Task id
     * @return Task|Builder|Model|object|null [Object]  Task
     */
    public function get($id)
    {
      return Task::where('id', $id)->first();
    }

    /**
     * Get base
     */
    public function getBase($select = [])
    {
    	$sTasks = [
      	'tasks.id as id',
        'tasks.name as name',
        'tasks.note as note',
        'tasks.status as status',
        'tasks.accepted as accepted',
        'tasks.start_date as start_date',
        'tasks.end_date as end_date',
        'tasks.created_at as created_at',
      ];

      $sProjects = [
        'projects.id as project_id',
        'projects.name as project_name',
        'project_categories.name as project_category',
      ];

      $sTaskTypes = [
        'task_types.name as task_type',
      ];

      $sUsers = [
      	'users.name as created_by',
    	];

    	$sTaskTalent = [
        'task_talent.user_id as talent_id'
      ];

      $selected = array_merge($sTasks, $sProjects, $sTaskTypes, $sUsers, $sTaskTalent, $select);

      $tasks = Task::query()->select($selected)
      ->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
			->join('projects', 'tasks.project_id', '=', 'projects.id')
			->join('project_categories', 'projects.category_id', 'project_categories.id')
			->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
			->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
		 	->join('users', 'tasks.created_by', '=', 'users.id')
		 	->with('talents');

		 	return $tasks;
    }

    /**
     * Delete One Task
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    public function delete($id)
    {
      $task = Task::find($id);

      try {
        $task->delete();
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      return true;
    }

    /**
     * Add or update a task
     * @param
     * @return mixed [Object]  Task
     */
    public function addOrUpdate($params)
    {
      $savedTask = [];

      $project = $params['project'];
      foreach ($params['tasks'] as $taskParams) {
        $savedTask[] = $this->saveTask($project, $taskParams, (int)$params['user'][0]);
      }

      return $savedTask;
    }

    /**
     * Patch a task status
     *
     * @param $id
     * @param $params
     */
    public function patch($id, $params)
    {
    	$this->helpers['notification'] = new NotificationHelper;
      $task = Task::find($id);

      //If task not found by ID return an error
      if ($task == null) {
        _helper('api')->error('Task ID invalid');
      }


      if ($params['action']) {
        switch ($params['action']) {
          case 'set_acceptation':
            $task->accepted = $params['value'];
            break;
          case 'set_closed':
            $task->status = 'CLOSED';
            $task->closed_at = new \DateTime();

            $this->helpers['notification']->save("La tâche ".$task->taskTypes[0]->name." du projet ".$task->project->name." a bien été clôturé", null, 'TÂCHE', $task->id);
            break;
        }
      }

      // Catch error during saving
      try {
        $task->save();

        return $task;
      } catch (Exception $e) {
        _helper('api')->error('An error occurred during save  : ' . $e->getMessage());
      }
    }

    // -- Useful

    private function saveTask($project, $taskParams, $user = null)
    {
    	$this->helpers['notification'] = new NotificationHelper;
      $mandatoryParameters = [];

      // Check if all parameters are provided
      try {
        _helper('api')->checkParameters($taskParams, $mandatoryParameters);
      } catch (Exception $e) {
        // Return the exception message if error
        _helper('api')->error($e->getMessage());
      }

      if (array_key_exists('id', $taskParams) && $taskParams['id']) {
      	$action = 'UPDATE';
        $task = Task::find($taskParams['id']);
        $task->updated_by = $user;
      } else {
      	$action = 'ADD';
        $task = new Task();
        $task->created_by = $user;
				$task->updated_by = $user;
				$task->status = 'PROGRESS';
      }

      $task->name = !empty($taskParams['name']) ? $taskParams['name'] : null;
      $task->project_id = $project;
      $task->note = !empty($taskParams['note']) ? $taskParams['note'] : null;
      $task->start_date = Carbon::parse($taskParams['date']['start'])->add(1, 'day')->format('Y-m-d 00:00:00');
      $task->end_date = Carbon::parse($taskParams['date']['end'])->add(1, 'day')->format('Y-m-d 00:00:00');

      try {
        $task->save();
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      $task->talents()->detach();
      if (array_key_exists('talent_id', $taskParams) /*&& is_array($taskParams['talent_id'])*/) {
        $task->talents()->sync([$taskParams['talent_id']]);
      }

      // If assigned user is the creator, accept task automatically
      if($taskParams['talent_id'] == $task->created_by){
      	$task->accepted = 1;
      }
      // If assigned user didn't create the task, accept task automatically
      if($taskParams['talent_id'] !== $task->created_by){
        $task->accepted = 1;
      }

      $task->taskTypes()->detach();
      if (array_key_exists('task_type_id', $taskParams) /*&& is_array($taskParams['task_type_id'])*/) {
        $task->taskTypes()->sync([$taskParams['task_type_id']]);
      }

      try {
        $task->save();

        $icon = $action == 'ADD' ? '🆕' : '✅';
        $action = $action == 'ADD' ? 'créé' : 'modifié';

        $this->helpers['notification']->save("$icon La tâche ".$task->taskTypes[0]->name." du projet ".$task->project->name." a bien été ".$action."", $user, 'TÂCHE', $task->id);
      } catch (Exception $e) {
        _helper('api')->error($e->getMessage());
      }

      return $task;
    }
}
