<?php

namespace App\Domains;

use App\Enum\StripeDonationInterval;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

final class Stripe {
    private StripeClient $client;

    public function __construct() {
        $this->client = new StripeClient(config('stripe.sk'));
    }

    public function getSessionDetails(string $sessionId) {
        return $this->client->checkout->sessions->retrieve($sessionId, [
            'expand' => ['line_items'],
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function createPaymentSession(StripeDonationInterval $interval, ?int $amount) {
        $mode = $interval === StripeDonationInterval::OneTime ? 'payment' : 'subscription';

        if ($amount) {
            $lineItems = [
                [
                    'quantity'   => 1,
                    'price_data' => [
                        'currency'     => 'usd',
                        'product_data' => ['name' => config('stripe.donation_name')],
                        'unit_amount'  => $amount,
                    ],
                ],
            ];

            if ($interval !== StripeDonationInterval::OneTime) {
                $lineItems[0]['price_data']['recurring'] = ['interval' => $interval->value];
            }
        } else {
            $lineItems = [
                [
                    'price'    => 'price_1QxABWEbGVuFzunH8cSynvNt',
                    'quantity' => 1,
                ],
            ];
        }

        return $this->client->checkout->sessions->create([
            'mode'        => $mode,
            'line_items'  => $lineItems,
            'success_url' => 'https://nationalpoliticalprisonercoalition.org/donate-callback?session_id={CHECKOUT_SESSION_ID}',
        ]);
    }
}
