<?php

namespace App\Http\Controllers\API\Explorer;

use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerMessengerConversationService;
use App\Http\Services\Explorer\ExplorerMessengerService;
use App\Http\Services\Explorer\ExplorerMissionService;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionProposition;
use Illuminate\Http\Request;

class ExplorerMessengerRestController extends Controller
{
    /*************************
     * Conversations
     ************************/

    public function getConversationsList(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        return response($explorerMessengerService->getUserConversationsList($user));
    }

    public function getConversationMessages($conversation_id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            return response($explorerMessengerService->getConversationMessages($user, $conversation_id));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function postConversationMessage($id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        try {
            $explorerMessengerService->newUserMessage($id, $requestParams['message']);
            return response('', 201);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function patchConversation($id, Request $request, ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            $explorerConversation = ExplorerMissionConversation::find($id);

            switch ($requestParams['patch_type']) {
                case 'update_last_check':
                    $explorerMessengerConversationService->updateUserLastCheckDate($explorerConversation, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }


    /*************************
     * Missions
     ************************/

    public function getMissionProposition(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            return response($explorerMessengerService->getMissionProposition($user, $request->get('proposition_id')));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function getMissionPropositionDrive(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            return response($explorerMessengerService->getMissionPropositionDrive($user, $request->get('proposition_id')));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function postNewMissionProposition(Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        try {
            $proposition = $explorerMissionService->newMissionProposition($requestParams['mission_proposition']);
            return response($proposition);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function patchMissionProposition($mission_proposition_id, Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            switch ($requestParams['patch_type']) {
                case 'mission_proposition_cancel':
                    $explorerMissionService->cancelMissionProposition($mission_proposition_id, $user);
                    break;
                case 'mission_proposition_close':
                    $explorerMissionService->closeMissionProposition($mission_proposition_id, $user);
                    break;
                case 'mission_full_cancel':
                    $missionProposition = ExplorerMissionProposition::find($mission_proposition_id);
                    $explorerMissionService->cancelFullMission($missionProposition->mission->id, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function patchMission($mission_id, Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            switch ($requestParams['patch_type']) {
                case 'mission_full_cancel':
                    $explorerMissionService->cancelFullMission($mission_id, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function getClientProposedMission(Request $request, ExplorerMissionService $explorerMissionService)
    {
        $params = $request->all();
        if (array_key_exists('q', $params)) {
            $filter = $params['q'];
        } else {
            $filter = '';
        }
        return response($explorerMissionService->getMissionsProposedByClient(\Auth::user(), $filter));
    }

    /*************************
     * Quotes
     ************************/

    public function postNewQuote($conversation_id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            $explorerMessengerService->newPropositionQuote($conversation_id, $requestParams['quote']);
            return response('', 201);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }


}
