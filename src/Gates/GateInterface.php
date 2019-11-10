<?php

namespace App\Gates;

/**
 * Interface GateInterface
 * @package App\Gates
 */
interface GateInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string $phone
     * @param string $text
     * @return bool
     */
    public function send(string $phone, string $text): bool;
}
