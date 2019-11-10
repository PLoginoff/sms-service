<?php

namespace App\Gates;

interface GateInterface
{
    public function getName(): string;

    public function send($phone, $text): bool;
}
