<?php

namespace App\Http\Services\Explorer;


use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Enum\ExplorerMissionStatusEnum;
use App\Models\ExplorerDrive;
use App\Models\ExplorerMission;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionProposition;
use App\User;
use Illuminate\Support\Facades\Auth;

class ExplorerMissionService
{


    /**
     * @var ExplorerMessengerConversationService
     */
    private $explorerMessengerConversationService;

    public function __construct(ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $this->explorerMessengerConversationService = $explorerMessengerConversationService;
    }

    /**
     * @param $missionParams
     * @return ExplorerMission
     */
    public function newMission($missionParams): ExplorerMission
    {
        $mission = new ExplorerMission();

        $mission->name = $missionParams['name'];
        $mission->budget = $missionParams['budget'];
        $mission->deadline = $missionParams['deadline'];
        $mission->type = $missionParams['type'];
        $mission->description = $missionParams['description'];
        $mission->status = ExplorerMissionStatusEnum::OPEN;

        $mission->save();
        return $mission;
    }

    /**
     * @param $missionPropositionParams
     * @return ExplorerMissionProposition
     */
    public function newMissionProposition($missionPropositionParams): ExplorerMissionProposition
    {
        $proposition = new ExplorerMissionProposition();
        $mission = $this->findOrCreateMission($missionPropositionParams['mission']);

        $proposition->id_mission = $mission->id;

        $proposition->client_id = \Auth::user()->id; //Only Client can propose mission
        $proposition->freelance_id = $missionPropositionParams['propose_to_user_id'];
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_QUOTE;

        $proposition->save();

        $drive = new ExplorerDrive();
        $drive->id_proposition = $proposition->id;
        $drive->save();

        $conversation = $this->explorerMessengerConversationService->createConversation($proposition);


        return $proposition;
    }

    public function cancelMissionProposition($missionPropositionId, User $user)
    {
        $missionProposition = ExplorerMissionProposition::find($missionPropositionId);

        if ($this->checkUserIsMissionPropositionClient($missionProposition, $user)) {
            $this->updateMissionPropositionStatus($missionProposition, ExplorerMissionPropositionStatusEnum::CANCELED);
        }

        $this->explorerMessengerConversationService->addMissionCanceledSystemMessage($missionProposition->conversation);
    }

    public function quoteAddedMissionProposition(ExplorerMissionProposition $proposition)
    {
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_PAYMENT;
        $proposition->save();
    }

    public function quotePaidMissionProposition(ExplorerMissionProposition $proposition)
    {
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_WORK;
        $proposition->save();
        $this->explorerMessengerConversationService->addQuotePaidSystemMessage($proposition->conversation);
        $this->markMissionTaken($proposition);
    }

    public function workFinishedMissionProposition(ExplorerMissionProposition $proposition)
    {
        $proposition->status = ExplorerMissionPropositionStatusEnum::FINISHED_WAITING_CLOSING;
        $proposition->save();
        $this->explorerMessengerConversationService->addMissionFinishedSystemMessage($proposition->conversation);
    }

    public function closeMissionProposition($missionPropositionId, User $user)
    {
        $missionProposition = ExplorerMissionProposition::find($missionPropositionId);

        if ($this->checkUserIsMissionPropositionClient($missionProposition, $user)) {
            $this->updateMissionPropositionStatus($missionProposition, ExplorerMissionPropositionStatusEnum::CLOSED);
            $this->closeMission($missionProposition->mission);
        }

        $this->explorerMessengerConversationService->closeMissionMessage($missionProposition->conversation);
    }

    public function cancelFullMission($missionId, User $user)
    {
        $mission = ExplorerMission::find($missionId);

        $missionPropositions = ExplorerMissionProposition::where('id_mission', "=", $missionId)->get();

        $this->cancelMission($mission);
        foreach ($missionPropositions as $missionProposition) {
            $this->cancelMissionProposition($missionProposition->id, $user);
        }
    }

    public function markMissionTaken(ExplorerMissionProposition $proposition)
    {
        $mission = $proposition->mission;
        $mission->status = ExplorerMissionStatusEnum::TAKEN;
        $mission->save();

        $propositionsToCancel = ExplorerMissionProposition::where([
            ['id_mission', '=', $mission->id],
            ['id', '<>', $proposition->id]
        ])->get();

        foreach ($propositionsToCancel as $missionProposition) {
            $this->missionPropositionTakenByAnother($missionProposition);
        }
    }

    private function missionPropositionTakenByAnother(ExplorerMissionProposition $missionProposition)
    {
        $missionProposition->status = ExplorerMissionPropositionStatusEnum::TAKEN_BY_OTHER;
        $missionProposition->save();

        $this->explorerMessengerConversationService->addMissionTakenByOtherSystemMessage($missionProposition->conversation);
    }

    private function findOrCreateMission($missionParams)
    {
        if (array_key_exists('id', $missionParams) && $missionParams['id'] !== null) {
            $mission = ExplorerMission::find($missionParams['id']);

            if ($mission !== null) {
                return $mission;
            }
        }

        $mission = ExplorerMission::where([
            ['name', '=', $missionParams['name']],
            ['created_by', '=', Auth::user()->id],
            ['status', '=', ExplorerMissionStatusEnum::OPEN],
        ])->first();

        if ($mission == null) {
            $mission = $this->newMission($missionParams);
        }

        return $mission;
    }

    public function updateMissionPropositionStatus(ExplorerMissionProposition $missionProposition, $status)
    {
        $missionProposition->status = $status;
        $missionProposition->save();
    }

    private function checkUserIsMissionPropositionClient(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->client_id === $user->id;
    }

    private function cancelMission(ExplorerMission $explorerMission)
    {
        $explorerMission->status = ExplorerMissionStatusEnum::CANCELED;
        $explorerMission->save();
    }

    private function closeMission(ExplorerMission $explorerMission)
    {
        $explorerMission->status = ExplorerMissionStatusEnum::CLOSED;
        $explorerMission->save();
    }

    public function getMissionsProposedByClient(User $user, $filter)
    {
        return ExplorerMission::where([
            ['created_by', '=', $user->id],
            ['status', '=', ExplorerMissionStatusEnum::OPEN],
            ['name', 'LIKE', '%' . $filter . '%']
        ])->get();
    }
}
