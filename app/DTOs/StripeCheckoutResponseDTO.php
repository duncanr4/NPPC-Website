<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

final class StripeCheckoutResponseDTO extends Data {
    public function __construct(
        public string $id,
        public int $amount,
        public string $status,
        public string $paymentStatus,
        public string $customerEmail,
        public string $customerName,
    ) {
    }
}
