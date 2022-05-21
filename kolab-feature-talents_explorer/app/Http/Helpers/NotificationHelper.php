<?php

namespace App\Http\Helpers;

use App\Models\Notification;
use App\Models\Appointment;
use App\Models\Project;
use App\Models\Task;
use App\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class ClientHelper
 * @package App\Http\Helpers
 */
class NotificationHelper
{
    /**
     * Retrieve last ten notifications
     *
     * @return Collection
     */
    public function get()
    {
        // return collect($this->getTasksNotifications())
        //     ->concat($this->getProjectsNotifications())
        //     ->concat($this->getTalentsNotifications())
        //     ->concat($this->getAppointmentsNotifications())
        //     ->sortByDesc('datetime')->slice(0, 10)->values();

    		$notifications = Notification::whereRaw('id IN (select MAX(id) FROM notifications GROUP BY element_id, element_type)')
    			->orderBy('id', 'DESC')
    			->get();

    		return $notifications;
    }

    public function save($message, $user_id = null, $element_type = null, $element_id = null)
    {
    	$notification = new Notification;

    	$notification->message = $message;
    	$notification->user_id = $user_id;
    	$notification->element_type = $element_type;
    	$notification->element_id = $element_id;

    	$notification->save();

    	return $notification;
    }

    /**
     * Retrieve last ten tasks notifications
     * @return array
     */
    private function getTasksNotifications()
    {
        $tasks = Task::query()
            ->select([
            	'tasks.id as id',
            	DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')
            ])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($tasks as $task) {
            $notifications[] = [
                'type' => 'task',
                'user' => $task->user,
                'datetime' => $task->datetime,
                'content' => 'Task Id -> ' . $task->id
            ];
        }
        return $notifications;
    }

    /**
     * Retrieve last ten projects notifications
     * @return array
     */
    private function getProjectsNotifications()
    {
        $projects = Project::query()
            ->select(['projects.id as id', DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($projects as $project) {
            $notifications[] = [
                'type' => 'project',
                'user' => $project->user,
                'datetime' => $project->datetime,
                'content' => 'Project Id -> ' . $project->id
            ];
        }
        return $notifications;
    }

    /**
     * Retrieve last ten talents notifications
     * @return array
     */
    private function getTalentsNotifications()
    {
        $talents = User::query()
            ->select(['users.id as id', DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($talents as $talent) {
            $notifications[] = [
                'type' => 'talent',
                'user' => $talent->user,
                'datetime' => $talent->datetime,
                'content' => 'Talent Id -> ' . $talent->id
            ];
        }
        return $notifications;
    }

    /**
     * Retrieve last ten appointments notifications
     * @return array
     */
    private function getAppointmentsNotifications()
    {
        $appointments = Appointment::query()
            ->select(['appointments.id as id',  DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($appointments as $appointment) {
            $notifications[] = [
                'type' => 'appointment',
                'user' => $appointment->user,
                'datetime' => $appointment->datetime,
                'content' => 'Appointment Id -> ' . $appointment->id
            ];
        }
        return $notifications;
    }

}
