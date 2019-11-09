<?php

namespace App\Gates;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class FakeGate implements GateInterface, LoggerAwareInterface
{
    protected $log;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(string $log)
    {
        $this->log = $log;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
