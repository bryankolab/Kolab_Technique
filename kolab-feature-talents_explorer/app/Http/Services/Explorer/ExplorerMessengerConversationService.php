<?php

namespace App\Http\Services\Explorer;

use App\Enum\ExplorerMissionMessagesTypesEnum;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\ExplorerDriveLink;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\User;
use Exception;

class ExplorerMessengerConversationService
{
    public function createConversation(ExplorerMissionProposition $missionProposition): ExplorerMissionConversation
    {
        $conversation = new ExplorerMissionConversation();

        $conversation->id_proposition = $missionProposition->id;

        $conversation->save();
        $this->initConversation($conversation);
        return $conversation;
    }

    /**
     * @throws Exception
     */
    public function updateUserLastCheckDate(ExplorerMissionConversation $explorerMissionConversation, User $user)
    {
        $this->checkUserAccess($explorerMissionConversation, $user);
        if ($user->hasRole('talent')) {
            $explorerMissionConversation->last_freelancer_check = new \DateTime();
        } else {
            $explorerMissionConversation->last_client_check = new \DateTime();
        }

        $explorerMissionConversation->save();
    }

    /**
     * Init new conversation
     * @param ExplorerMissionConversation $conversation
     * @return void
     */
    private function initConversation(ExplorerMissionConversation $conversation)
    {
        $this->addMissionSummaryMessage($conversation);
        $this->addCreateQuoteSystemMessage($conversation);
    }

    /**
     * Add Mission summary system message into conversation
     * @param ExplorerMissionConversation $conversation
     * @return void
     */
    private function addMissionSummaryMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::USER_MISSION_PROPOSAL);
    }

    /**
     * @param $conversationId
     * @param $messageType
     * @param string|null $message
     * @param ExplorerMissionQuote|null $quote
     * @return bool
     */
    public function newConversationMessage($conversationId, $messageType, ?string $message = '', ExplorerMissionQuote $quote = null): bool
    {
        $conversationMessage = new ExplorerMissionConversationMessage();

        $conversationMessage->message = $message;
        $conversationMessage->id_conversation = $conversationId;
        $conversationMessage->type = $messageType;

        if ($quote != null) {
            $conversationMessage->id_quote = $quote->id;
        }
        return $conversationMessage->save();
    }

    public function cancelMissionPropositionMessage(ExplorerMissionConversation $missionConversation)
    {
        $this->newConversationMessage($missionConversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CANCELED);
    }

    public function closeMissionMessage(ExplorerMissionConversation $missionConversation)
    {
        $this->newConversationMessage($missionConversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CLOSED);
    }

    /**
     * Add a message linked to à Link
     * @param $conversationId
     * @param ExplorerDriveLink $explorerDriveLink
     * @return bool
     */
    public function driveLinkMessage($conversationId, ExplorerDriveLink $explorerDriveLink): bool
    {
        $conversationMessage = new ExplorerMissionConversationMessage();

        $conversationMessage->message = '';
        $conversationMessage->id_conversation = $conversationId;
        $conversationMessage->type = ExplorerMissionMessagesTypesEnum::USER_DRIVE_LINK;

        $conversationMessage->id_drive_link = $explorerDriveLink->id;
        return $conversationMessage->save();
    }

    public function addCreateQuoteSystemMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_QUOTE_CREATION);
    }

    public function addQuotePaidSystemMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_QUOTE_PAID);
    }

    public function addMissionFinishedSystemMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_FINISHED);
    }

    public function addMissionCanceledSystemMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CANCELED);
    }

    public function checkUserAccess(ExplorerMissionConversation $explorerMissionConversation, User $user)
    {
        $proposition = $explorerMissionConversation->proposition;
        if ($proposition->client_id != $user->id && $proposition->freelance_id != $user->id) {
            throw new Exception("Access denied to this conversation");
        }
    }

    public function addMissionTakenByOtherSystemMessage(ExplorerMissionConversation $conversation)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_TAKEN_BY_OTHER);
    }
}
