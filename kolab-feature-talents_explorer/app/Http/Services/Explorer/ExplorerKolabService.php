<?php

namespace App\Http\Services\Explorer;


use App\Models\Client;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Task;
use App\Models\TaskType;
use App\User;
use Carbon\Carbon;
use Exception;

class ExplorerKolabService
{
    /**
     * Création d'un projet dans Kolab
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @return void
     */
    public function createKolabProjectFromExplorerProposition(ExplorerMissionProposition $explorerMissionProposition)
    {
        $explorerMission = $explorerMissionProposition->explorerMission;

        $project = new Project();

        $project->name = $explorerMission->name;
        $project->production = "Kolab";
        $project->client_id = $this->findOrCreateKolabClientFromExplorerUser($explorerMissionProposition->client);
        $project->client_phone = $explorerMissionProposition->client->email;
        $project->client_email = $explorerMissionProposition->client->phone;
        $project->category_id = $this->findOrCreateKolabProjectCategory('Mission Explorer');

        switch ($explorerMission->deadline) {
            case "Dès maintenant":
                $project->date_deadline = Carbon::now()->add(1, 'day')->format('Y-m-d 00:00:00');
                break;
            case "Dans la semaine":
                $project->date_deadline = Carbon::now()->add(1, 'week')->format('Y-m-d 00:00:00');
                break;
            case "Dans 2 semaines" :
                $project->date_deadline = Carbon::now()->add(2, 'week')->format('Y-m-d 00:00:00');
                break;
            case "Dans le mois":
                $project->date_deadline = Carbon::now()->add(1, 'month')->format('Y-m-d 00:00:00');
                break;
        }

        //Il faudra améliorer cette partie

        $project->save();


        $project->talents()->sync($explorerMissionProposition->freelance_id);
        $project->save();

        $explorerMissionProposition->kolab_project_id = $project->id;
        $explorerMissionProposition->save();
    }

    /**
     * Création d'une tache dans Kolab
     * @param ExplorerMissionQuote $explorerMissionQuote
     * @return void
     */
    public function createKolabPlanningTask(ExplorerMissionQuote $explorerMissionQuote)
    {
        try {
            if ($explorerMissionQuote->deadline != null && $explorerMissionQuote->deadline != '') {
                $kolabTask = new Task();

                $kolabTask->name = "Devis de la mission " . $explorerMissionQuote->proposition->mission->name;
                $kolabTask->project_id = $explorerMissionQuote->proposition->kolabProject->id;
                $kolabTask->note = $explorerMissionQuote->comments;
                $kolabTask->start_date = Carbon::now()->format('Y-m-d 00:00:00');

                switch ($explorerMissionQuote->deadline) {
                    case "Dans 1 jour":
                        $kolabTask->end_date = Carbon::now()->add(1, 'day')->format('Y-m-d 00:00:00');
                        break;
                    case "Dans 2 jours":
                        $kolabTask->end_date = Carbon::now()->add(2, 'day')->format('Y-m-d 00:00:00');
                        break;
                    case "Dans 3 jours" :
                        $kolabTask->end_date = Carbon::now()->add(3, 'day')->format('Y-m-d 00:00:00');
                        break;
                    case "Dans 4 jours":
                        $kolabTask->end_date = Carbon::now()->add(4, 'day')->format('Y-m-d 00:00:00');
                        break;
                }

                $kolabTask->created_by = $explorerMissionQuote->proposition->client_id;
                $kolabTask->updated_by = $explorerMissionQuote->proposition->client_id;
                $kolabTask->status = 'PROGRESS';
                $kolabTask->accepted = 1;
                $kolabTask->save();

                $kolabTask->talents()->sync($explorerMissionQuote->proposition->freelance_id);
                $kolabTask->save();

                $kolabTaskType = $this->findOrCreateKolabTaskType($explorerMissionQuote->proposition->mission->type);
                $kolabTask->taskTypes()->sync($kolabTaskType);
                $kolabTask->save();


                $explorerMissionQuote->kolab_task_id = $kolabTask->id;
                $explorerMissionQuote->save();
            }
        } catch (Exception $e) {
        }
    }

    /**
     * Find or create a new Client in the database and return the client ID
     * @param User $explorerClientUser
     * @return mixed
     */
    private function findOrCreateKolabClientFromExplorerUser(User $explorerClientUser)
    {
        $client = Client::where('explorer_client_id', '=', $explorerClientUser->id)->first();

        if ($client == null) {
            $client = Client::where('name', '=', $explorerClientUser->name)->first();
        }

        if ($client == null) {
            $client = new Client();
            $client->explorer_client_id = $explorerClientUser->id;
            $client->name = $explorerClientUser->name;
            $client->created_by = $explorerClientUser->id;
            $client->updated_by = $explorerClientUser->id;

            try {
                $client->save();
            } catch (Exception $e) {

            }
        }

        return $client->id;
    }

    private function findOrCreateKolabProjectCategory($explorerMissionType)
    {
        $projectCategory = ProjectCategory::where('name', '=', $explorerMissionType)->first();

        if ($projectCategory == null) {
            $projectCategory = new ProjectCategory();
            $projectCategory->name = $explorerMissionType;
            $projectCategory->save();
        }

        return $projectCategory->id;
    }

    private function findOrCreateKolabTaskType($explorerMissionType)
    {
        $taskType = TaskType::where('name', '=', $explorerMissionType)->first();

        if ($taskType == null) {
            $taskType = new TaskType();
            $taskType->name = $explorerMissionType;
            $taskType->save();
        }

        return $taskType->id;
    }
}
