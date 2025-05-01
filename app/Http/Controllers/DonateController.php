<?php

namespace App\Http\Controllers;

use App\Domains\Stripe;
use App\DTOs\StripeCheckoutResponseDTO;
use Illuminate\Http\Request;

final class DonateController extends Controller {
    public function callback(Request $request) {
        $sessionId = $request->get('session_id');

        $stripe  = new Stripe();
        $details = $stripe->getSessionDetails($sessionId);

        $DTO = new StripeCheckoutResponseDTO(
            id: $details->id,
            amount: $details->amount_total,
            status: $details->status,
            paymentStatus: $details->payment_status,
            customerEmail: $details->customer_details->email,
            customerName: $details->customer_details->name
        );

        return view('pages.donate-callback', ['DTO' => $DTO]);
    }
}
