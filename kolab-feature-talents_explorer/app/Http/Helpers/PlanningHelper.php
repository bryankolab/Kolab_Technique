<?php

namespace App\Http\Helpers;


use App\Models\Appointment;
use App\Models\Task;
use App\Models\TaskType;
use App\User;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PlanningHelper
{
    public function get($params)
    {
        // if (!_helper('api')->checkParameterExistAndNotEmpty('filter_talents_id', $params)) {
        //     $params['filter_talents_id'] = [2]; // Todo : If no talent selected return the planning for the current user only
        // }

        if (!_helper('api')->checkParameterExistAndNotEmpty('date_month', $params) || !_helper('api')->checkParameterExistAndNotEmpty('date_year', $params)) {
          $params['date_month'] = date('m');
          $params['date_year'] = date('Y');
        }

        $period = $this->getDatePeriod($params['date_month'], $params['date_year']);

        $tasksByDate = $this->getTasksByDate(
        	$params['filter_talents_id'],
        	$params['date_month'],
        	$params['date_year'],
        	$params
        );

        $appointmentsByDate = $this->getAppointmentsByDate(
        	$params['filter_talents_id'],
        	$params['date_month'],
        	$params['date_year']
        );

        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $dateFormat = 'Y-m-d';

        // Populating the Planning Array
        foreach ($period as $date) {
      		//$dateFr = mb_convert_encoding(ucWords(strftime('%A %d %h %Y', strtotime($date->format('Y-m-d')))), 'UTF-8');
          $_date = $date->format('Y-m-d');
      		$date->setTime(00, 00, 00);

          // Initializing empty array for the date in case there is no task at the date
          $result['planning'][$_date] = [];

          foreach ($tasksByDate as $key => $tasksByTalent) {
            //if (($date >= DateTime::createFromFormat($dateFormat, $key)) && ($date <= DateTime::createFromFormat($dateFormat, $key))) {
              // Add the task if the current date is in the task interval
              foreach ($tasksByTalent as $talentId => $tasks) {
              	foreach($tasks as $task){
	              	$start_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->start_date);
	              	$end_date = DateTime::createFromFormat('Y-m-d H:i:s', $task->end_date);
	              	if (($date >= $start_date) && ($date <= $end_date)) {
	              		$task->type = "TASK";
	                	$result['planning'][$_date][$talentId]['events'][] = $task;
	                }
	              }
              }
            //}
          }

          $date = $date->format('Y-m-d');
          foreach($appointmentsByDate as $key => $appointmentsByTalent) {
            if ($date == $key) {
              // Add the task if the current date is in the task interval
              foreach ($appointmentsByTalent as $talentId => $appointments) {
              	foreach($appointments as $appointment){
              		$appointment->type = "APPOINTMENT";
                	$result['planning'][$_date][$talentId]['events'][] = $appointment;
              	}
              }
            }
          }
        }

        // Retrieve the talents names for the planning head
        $result['talent_name'] = $this->getTalentsName($params['filter_talents_id']);

        $result['task_name'] = [];
        if(!empty($params['filter_task_types_id']))
        	$result['task_name'] = $this->getTasksName($params['filter_task_types_id']);

        return $result;
    }

    //
    // -- Useful
    //

    private function getDatePeriod($month, $year)
    {
        $dateFormat = "d/m/Y";

        try {
          $startDate = \DateTime::createFromFormat($dateFormat, "01/$month/$year");
          $endDate = \DateTime::createFromFormat($dateFormat, '' . $this->getMonthNbDays($month, $year) . "/$month/$year");

          return new DatePeriod(
            $startDate,
            new DateInterval('P1D'),
            $endDate
          );
        } catch (\Exception $e) {
          _helper('api')->error($e->getMessage());
        }

    }

    private function isLeapYear($year)
    {
        return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year % 400) == 0)));
    }

    /**
     * Get the numbers of day in one month
     * @param $month
     * @param $year
     * @return int
     */
    private function getMonthNbDays($month, $year)
    {
        $shortMonth = [4, 6, 9, 11];
        $february = 2;

        if ($month == $february) {
            return $this->isLeapYear($year) ? 30 : 29;
        } elseif (in_array($month, $shortMonth)) {
            return 31;
        } else {
            return 32;
        }
    }

    /**
     * Return the tasks grouped by dates and talent id
     *
     * @param $selectedTalentsId
     * @param int $month
     * @param int $year
     * @param $params
     * @return Task[]|Collection|Builder[]|\Illuminate\Support\Collection
     *
     */
    private function getTasksByDate($selectedTalentsId, int $month, int $year, $params)
    {
        $tasksByDate = Task::query()
            ->select([
              'tasks.id as id',
              'tasks.name as name',
              'tasks.note as note',
              'tasks.status as status',
              'tasks.accepted as accepted',
              'projects.id as project_id',
              'projects.name as project_name',
              'project_categories.name as project_category',
              'task_types.name as task_type',
              'tasks.start_date as start_date',
              'tasks.end_date as end_date',
              'tasks.created_at as created_at',
              'users.name as created_by',
              'task_talent.user_id as talent_id',
              DB::raw('DATE(start_date) as task_date')
            ])
      			->join('task_talent', 'tasks.id', '=', 'task_talent.task_id')
      			->join('projects', 'tasks.project_id', '=', 'projects.id')
      			->join('project_categories', 'projects.category_id', 'project_categories.id')
      			->join('task_type_task', 'tasks.id', '=', 'task_type_task.task_id')
      			->join('task_types', 'task_type_task.task_type_id', '=', 'task_types.id')
      		 	->join('users', 'tasks.created_by', '=', 'users.id')
      		 	->with('talents')
      		 	->whereIn('task_talent.user_id', $selectedTalentsId)
      		 	->where('tasks.accepted', 1);
      			//->where(function ($query) {
						// 	$query->whereNull('tasks.accepted')
						// 	->orWhere('tasks.accepted', '=', 1);
						// });
            //->where('tasks.end_date', '>', new \DateTime());

        if (_helper('api')->checkParameterExistAndNotEmpty("filter_projects_id", $params)) {
        	$tasksByDate->whereIn('tasks.project_id', [$params['filter_projects_id']]);
        }

        if (_helper('api')->checkParameterExistAndNotEmpty("filter_task_types_id", $params)) {
        	$tasksByDate->whereIn('task_type_task.task_type_id', $params['filter_task_types_id']);
        }

        return $tasksByDate->get()->groupBy(['task_date', 'talent_id']);
    }

    /**
     * Return the appointments grouped by dates and talent id
     *
     * @param $selectedTalentsId
     * @param int $month
     * @param int $year
     * @return Appointment[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    private function getAppointmentsByDate($selectedTalentsId, int $month, int $year)
    {
        // return Appointment::select([DB::raw('DATE(datetime) as appointment_date'), 'id', 'lastname', 'firstname', 'job', 'created_by as talent_id'])
        //     ->whereIn('created_by', $selectedTalentsId)
        //     ->whereMonth('datetime', $month)
        //     ->whereYear('datetime', $year)
        //     ->get()
        //     ->groupBy(['appointment_date', 'talent_id']);

        $appointments = Appointment::select(['*', DB::raw('DATE(datetime) as appointment_date'), 'created_by as talent_id'])
        		->with('room')
            ->whereIn('created_by', $selectedTalentsId)
            ->whereMonth('datetime', $month)
            ->whereYear('datetime', $year)
            ->get()
            ->groupBy(['appointment_date', 'talent_id']);

        return $appointments;
    }

    /**
     * Return the appointments grouped by dates and talent id
     *
     * @param $selectedTalentsId
     * @return Appointment[]|Collection|Builder[]|\Illuminate\Support\Collection
     */
    private function getTalentsName($selectedTalentsId)
    {
        $talents = User::select(['id', 'lastname', 'firstname'])
            ->whereIn('id', $selectedTalentsId)
            ->get();

        return $talents->mapToGroups(function ($item, $key) {
            return [$item['id'] => $item];
        });
    }

    private function getTasksName($selectedTasksId)
    {
        $tasks = TaskType::select(['id', 'name'])
            ->whereIn('id', $selectedTasksId)
            ->get();

        return $tasks->mapToGroups(function ($item, $key) {
            return [$item['id'] => $item];
        });
    }
}
