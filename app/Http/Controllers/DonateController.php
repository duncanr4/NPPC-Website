<?php

namespace App\Http\Controllers;

use App\Domains\Stripe;
use App\DTOs\StripeCheckoutResponseDTO;
use Illuminate\Http\Request;

final class DonateController extends Controller {
    public function callback(Request $request) {
        $sessionId = $request->get('session_id');

        if (! $sessionId) {
            abort(404);
        }

        try {
            $stripe  = new Stripe();
            $details = $stripe->getSessionDetails($sessionId);
        } catch (\Exception $e) {
            abort(404);
        }

        $DTO = new StripeCheckoutResponseDTO(
            id: $details->id,
            amount: $details->amount_total,
            status: $details->status,
            paymentStatus: $details->payment_status,
            customerEmail: $details->customer_details?->email,
            customerName: $details->customer_details?->name
        );

        return view('pages.donate-callback', ['DTO' => $DTO]);
    }
}
