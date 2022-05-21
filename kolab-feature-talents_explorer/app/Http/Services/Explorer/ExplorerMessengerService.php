<?php

namespace App\Http\Services\Explorer;

use App\Enum\ExplorerMissionMessagesTypesEnum;
use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExplorerMessengerService
{
    /**
     * @var ExplorerQuoteService
     */
    private $explorerQuoteService;

    /**
     * @var ExplorerMissionService
     */
    private $explorerMissionService;

    /**
     * @var ExplorerDriveService
     */
    private $explorerDriveService;

    /**
     * @var ExplorerMessengerConversationService
     */
    private $explorerMessengerConversationService;

    public function __construct(ExplorerQuoteService $explorerQuoteService, ExplorerMissionService $explorerMissionService, ExplorerDriveService $explorerDriveService, ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $this->explorerQuoteService = $explorerQuoteService;
        $this->explorerMissionService = $explorerMissionService;
        $this->explorerDriveService = $explorerDriveService;
        $this->explorerMessengerConversationService = $explorerMessengerConversationService;
    }


    ///////////////////////////////
    /// PUBLIC
    ///////////////////////////////

    //CONVERSATION LIST

    /**
     * @param User $user
     * @return array
     */
    public function getUserConversationsList(User $user): array
    {
        $userPropositionList = $this->getUserMissionPropositionsList($user);
        $conversationList = [];

        foreach ($userPropositionList as $userProposition) {
            $conversation = $userProposition->conversation;
            //$this->explorerMessengerConversationService->updateUserLastCheckDate($conversation, $user);

            $conversationList[] = $conversation;
        }

        usort($conversationList, [$this, 'sortConv']);

        return $conversationList;
    }

    /**
     * @throws Exception
     */
    function sortConv($a, $b): int
    {
        $a = new \DateTime($a['lastMessage']['updated_at']);
        $b = new \DateTime($b['lastMessage']['updated_at']);

        if ($a == $b) {
            return 0;
        }

        return ($a > $b) ? -1 : 1;
    }

//CONVERSATION

    /**
     * @throws Exception
     */
    public
    function getConversationMessages(User $user, $conversationId)
    {
        $conversation = ExplorerMissionConversation::find($conversationId);
        $proposition = $conversation->proposition;

        if ($this->checkUserPropositionAccess($proposition, $user)) {
            return ExplorerMissionConversationMessage::where('id_conversation', $conversationId)
                ->orderBy('created_at', 'ASC')->get()->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('Y-m-d');
                });
        } else {
            throw new Exception("User Doesn't have access to the conversation");
        }
    }

    /**
     * @param $conversationId
     * @param $message
     * @return bool
     */
    public
    function newUserMessage($conversationId, $message): bool
    {
        $conversation = ExplorerMissionConversation::find($conversationId);
        $links = $this->searchLinks($message);
        if (count($links) <= 0) {
            return $this->explorerMessengerConversationService->newConversationMessage($conversationId, ExplorerMissionMessagesTypesEnum::USER_MESSAGE, $message);
        } else {
            $drive = $conversation->proposition->drive;
            foreach ($links as $link) {
                $driveLink = $this->explorerDriveService->addLink($drive, $link);
                $this->explorerMessengerConversationService->driveLinkMessage($conversationId, $driveLink);
            }
            if ($conversation->proposition->freelance_id === \Auth::user()->id) {
                $this->explorerMissionService->workFinishedMissionProposition($conversation->proposition);
            }
            return true;
        }
    }

// PROPOSITION

    /**
     * @param User $user
     * @param $propositionId
     * @return ExplorerMissionProposition|ExplorerMissionProposition[]|Collection|Model|null
     * @throws Exception
     */
    public
    function getMissionProposition(User $user, $propositionId)
    {
        $proposition = ExplorerMissionProposition::find($propositionId);

        if ($this->checkUserPropositionAccess($proposition, $user)) {
            return $proposition;
        } else {
            throw new Exception("User Doesn't have access to the conversation");
        }
    }

// DRIVE

    /**
     * @throws Exception
     */
    public
    function getMissionPropositionDrive(User $user, $propositionId)
    {
        $proposition = $this->getMissionProposition($user, $propositionId);
        return $proposition->drive()->getResults();
    }

// QUOTE

    /**
     * @param $conversationId
     * @param $quoteInfos
     * @return bool
     * @throws Exception
     */
    public
    function newPropositionQuote($conversationId, $quoteInfos): bool
    {
        $conversation = ExplorerMissionConversation::find($conversationId);
        $proposition = $conversation->proposition;

        if (!$this->checkUserHasPropositionFreelanceAccess($proposition, \Auth::user())) {
            throw new Exception('Access Unauthorized for User');
        }

        $quote = $this->explorerQuoteService->newQuote($quoteInfos, $proposition);
        $this->explorerMissionService->quoteAddedMissionProposition($proposition);

        return $this->explorerMessengerConversationService->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::USER_QUOTE, '', $quote);
    }


///////////////////////////////
/// PRIVATE
///////////////////////////////

    /**
     * @param User $user
     * @return Builder[]|ExplorerMissionProposition[]|Collection
     */
    private
    function getUserMissionPropositionsList(User $user)
    {
        return ExplorerMissionProposition::where('client_id', $user->id)
            ->orWhere('freelance_id', $user->id)->get();
    }

    /**
     * Check if user is part of the proposition has client or as freelancer
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param User $user
     * @return bool
     */
    private
    function checkUserPropositionAccess(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->freelance_id == $user->id || $explorerMissionProposition->client_id == $user->id;
    }

    /**
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param User $user
     * @return bool
     */
    private
    function checkUserHasPropositionFreelanceAccess(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->freelance_id == $user->id;
    }

    /**
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param string $status
     * @return bool
     */
    private
    function updatePropositionStatus(ExplorerMissionProposition $explorerMissionProposition, string $status = ExplorerMissionPropositionStatusEnum::WAITING_QUOTE): bool
    {
        $explorerMissionProposition->status = $status;

        return $explorerMissionProposition->save();
    }

    /**
     * Search and return link in message
     * @param $text
     * @return array|mixed
     */
    private
    function searchLinks($text)
    {
        $pattern = '~[a-zA-Z]+://\S+~';

        if ($num_found = preg_match_all($pattern, $text, $out)) {
            return ($out[0]);
        }

        return [];
    }
}
