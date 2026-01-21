<?php

namespace App\Services;

interface PaymentGateway
{
    public function charge(array $payload): array;
}

