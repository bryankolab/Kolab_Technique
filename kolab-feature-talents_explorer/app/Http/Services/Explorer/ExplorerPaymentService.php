<?php

namespace App\Http\Services\Explorer;

use App\Models\ExplorerMissionQuote;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;


class ExplorerPaymentService
{
    /**
     * @var ExplorerQuoteService
     */
    private $explorerQuoteService;

    public function __construct(ExplorerQuoteService $explorerQuoteService)
    {
        $this->explorerQuoteService = $explorerQuoteService;
    }

    /**
     * @throws ApiErrorException
     */
    public function createExplorerQuoteCheckoutSession(ExplorerMissionQuote $quote): Response
    {
        Stripe::setApiKey(getenv('STRIPE_SECRET'));
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Devis mission "' . $quote->proposition->mission->name . '"',
                    ],
                    'unit_amount' => $quote->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('app_explorer_messenger'),
            'cancel_url' => route('app_explorer_messenger')
        ]);

        $this->explorerQuoteService->saveStripeSessionId($quote, $session->id);

        return new Response('', 303, ['Location' => $session->url]);
    }

    public function markQuotePaidByStripeSessionId($stripeCSId)
    {
        $this->explorerQuoteService->payQuoteByStripeSessionId($stripeCSId);
    }
}
