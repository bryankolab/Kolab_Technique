<?php

namespace App\Http\Services\Explorer;


use App\Enum\ExplorerMissionQuoteStatusEnum;
use App\Models\ExplorerMission;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use Illuminate\Support\Facades\Date;

class ExplorerQuoteService
{
    /**
     * @var ExplorerMissionService
     */
    private $explorerMissionService;
    /**
     * @var ExplorerKolabService
     */
    private $explorerKolabService;

    public function __construct(ExplorerMissionService $explorerMissionService, ExplorerKolabService $explorerKolabService)
    {
        $this->explorerMissionService = $explorerMissionService;
        $this->explorerKolabService = $explorerKolabService;
    }

    /**
     * @param $quoteInfos
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @return ExplorerMissionQuote
     */
    public function newQuote($quoteInfos, ExplorerMissionProposition $explorerMissionProposition): ExplorerMissionQuote
    {
        $quote = new ExplorerMissionQuote();

        $quote->deadline = $quoteInfos['deadline'];
        $quote->quote_line = json_encode($quoteInfos['quoteItems']);
        $quote->price = $quoteInfos['price'];
        //$quote->service_fee = $quoteInfos['service_fee'];
        $quote->comments = $quoteInfos['comment'];
        $quote->status = ExplorerMissionQuoteStatusEnum::WAITING_PAYMENT;
        $quote->id_proposition = $explorerMissionProposition->id;

        $quote->save();

        return $quote;
    }

    public function payQuote(ExplorerMissionQuote $quote)
    {
        try {
            $this->explorerKolabService->createKolabProjectFromExplorerProposition($quote->proposition);
            $this->explorerKolabService->createKolabPlanningTask($quote);
            $this->updateQuoteStatus($quote, ExplorerMissionQuoteStatusEnum::PAID);
            $this->explorerMissionService->quotePaidMissionProposition($quote->proposition);
        } catch (\Exception $e) {
        }

    }

    public
    function updateQuoteStatus(ExplorerMissionQuote $quote, $status): ExplorerMissionQuote
    {
        $quote->status = $status;

        if ($status == ExplorerMissionQuoteStatusEnum::PAID) {
            $quote->quote_paid_date = new \DateTime();
        }

        $quote->save();
        return $quote;
    }

    private
    function findQuote($quoteId)
    {
        return ExplorerMissionQuote::find($quoteId);
    }

    public function saveStripeSessionId(ExplorerMissionQuote $quote, $stripeCSId)
    {
        $quote->stripe_cs_id = $stripeCSId;
        $quote->init_payment_date = new \DateTime();
        $quote->save();
    }

    public function payQuoteByStripeSessionId($stripeCSId)
    {
        $quote = ExplorerMissionQuote::where('stripe_cs_id', '=', $stripeCSId)->first();

        $this->payQuote($quote);
    }
}
