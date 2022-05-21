<?php

namespace App\Http\Helpers;

use App\Models\Appointment;
use App\Models\Project;
use App\Models\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DashboardHelper
{
    public function get($userId, $params)
    {
        return [
          'tasks' => $this->getDashboardTasks($userId, $params),
          'appointments' => $this->getDashboardAppointments($userId),
          'progress_appointments' => $this->getProgressAppointments($userId),
          'progress_tasks' => $this->getProgressTasks($userId, $params),
          'closed_tasks' => $this->getClosedTasks($userId, $params),
          'waiting_tasks' => $this->getWaitingTasks($userId, $params)
        ];
    }

    // -- Useful

    /**
     * Return all the future tasks of the connected user
     *
     * @param $userId
     * @return Task[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    
    private function getDashboardTasks($userId, $params)
    {
        $output = [];
        $today = Carbon::today();

        $tasks = Task::query()
        ->select([
          'tasks.id as id',
          'tasks.name as name',
          'tasks.note as note',
          'tasks.status as status',
          'tasks.accepted as accepted',
          'projects.id as project_id',
          'projects.name as project_name',
          'project_categories.name as project_category',
          'project_categories.color as project_category_color',
          'task_types.name as task_type',
          'tasks.start_date as start_date',
          'tasks.end_date as end_date',
          'tasks.created_at as created_at',
          'users.name as created_by'
        ])
  			->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
  			->join('projects', 'tasks.project_id', '=', 'projects.id')
  			->join('project_categories', 'projects.category_id', 'project_categories.id')
  			->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
  			->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
  		 	->join('users', 'tasks.created_by', '=', 'users.id')
  		 	->with('talents')
         ->where('task_talent.user_id', '=', $userId);
         

         $appointments = Appointment::query()
        ->select([
          'appointments.id as id',
          'appointments.lastname as lastname',
          'appointments.firstname as firstname',
          'appointments.datetime as datetime',
          'appointments.email as email',
          'appointments.location as location'
          
        ]);
  			
  		// 	->where(function ($query) {
				// 	$query->whereNull('tasks.accepted')
				// 	->orWhere('tasks.accepted', '=', 1);
				// });

  			// Get progress tasks
  			$progressTasks = clone $tasks;
      	$progressTasks = $progressTasks->where(function ($query) {
					$query->where('status')
          ->orWhere('tasks.start_date', '<=', Carbon::today())
          ->where('tasks.status','!=','CLOSED');
        })->orderBy('tasks.end_date')->groupBy('tasks.id')->get();

      	// Get waiting tasks
      	$waitingTasks = clone $tasks;
				$waitingTasks = $waitingTasks->where(function ($query) { $query->where('status')->orWhere('tasks.start_date','>',Carbon::today())->where('tasks.status','!=','CLOSED');})->orderBy('tasks.end_date')->groupBy('tasks.id')->get();

				// Get closed tasks
				$closedTasks = clone $tasks;
				$closedTasks = $closedTasks->where(function ($query) { $query->whereNull('status')->orWhere('status', '=', 'CLOSED');})->orderBy('tasks.status')->groupBy('tasks.id')->limit(10)->get();

				// Filters for only specific results
        if(!empty($params['params']['filter_status'])){
        	if($params['params']['filter_status'] == 'STATUS_PROGRESS')
        		$output = $progressTasks;
        	

        	if($params['params']['filter_status'] == 'STATUS_WAITING'){
        		$output = $waitingTasks;
        	}

        	if($params['params']['filter_status'] == 'STATUS_CLOSED'){
        		$output = $closedTasks;
        	}

        	if($params['params']['filter_status'] == 'STATUS_ALL'){
        		$output = $allTasks;
          }
          
        } else {
        	// Or return all tasks
        	$output = $progressTasks;
        }

        return $output;

    }

    /**
     * Return all the future appointments of the connected user
     *
     * @param $userId
     * @return Appointment[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    private function getDashboardAppointments($userId)
    {
        return Appointment::query()
          ->where('created_by', '=', $userId)
          //->where('datetime', '>', new \DateTime())
          ->whereDate('datetime' ,Carbon::today())
          ->orderBy('datetime')
          ->get();
    }
    private function getProgressAppointments($userId)
    {
        return Appointment::query()
          ->where('created_by', '=', $userId)
          ->whereDate('datetime','>', Carbon::today())
          ->orderBy('datetime')
          ->get();
    }

    /**
     * Return all the future tasks of the connected user
     *
     * @param $userId
     * @return Task[]|Builder[]|Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection|Carbon\Carbon
     */

    private function getProgressTasks($userId, $params)
    {
    	$tasks = Task::query()
  			->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
         ->where('task_talent.user_id', '=', $userId)
         ->where('tasks.status','!=', 'CLOSED') 
				->where(function ($query) {
					$query->whereNull('tasks.start_date')
          ->orWhere('tasks.start_date', '<=', Carbon::today());
          // ->orWhere('tasks.end_date', '>', Carbon::today());
				});

			return $tasks->count();
    }

    private function getClosedTasks($userId, $params)
    {
    	$closedtasks = Task::query()
  			->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
  		 	->where('task_talent.user_id', '=', $userId)
				->where(function ($query) {
					$query->whereNull('tasks.status')
					->orWhere('tasks.status', '=', 'CLOSED');
				});

			return $closedtasks->count();
    }

    private function getWaitingTasks($userId, $params)
    {
    	$waitingtasks = Task::query()
  			->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
         ->where('task_talent.user_id', '=', $userId)
         ->where('tasks.status','!=', 'CLOSED')  
				->where(function ($query) {
					$query->whereNull('tasks.start_date')
          ->orWhere('tasks.start_date', '>', Carbon::today());
          
				});

			return $waitingtasks->count();
    }
}
