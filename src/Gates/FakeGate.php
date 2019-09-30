<?php

namespace App\Gates;

class FakeGate implements GateInterface
{
    protected $log;

    public function __construct(string $log)
    {
        $this->log = $log;
    }

    public function getName(): string
    {
        return 'fake';
    }

    public function send($phone, $text): bool
    {
        file_put_contents($this->log, date(DATE_ATOM). " $phone: $text\n", FILE_APPEND);
        return true;
    }
}
