<?php

namespace App\Gates;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class FakeGate implements GateInterface, LoggerAwareInterface
{
    /** @var string log path */
    protected $log;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param string $log
     */
    public function __construct(string $log)
    {
        $this->log = $log;
    }

    /**
     * @inheritDoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'fake';
    }

    /**
     * @inheritDoc
     */
    public function send(string $phone, string $text): bool
    {
        $this->logger->info("Easy: $phone $text");
        file_put_contents($this->log, date(DATE_ATOM) . " $phone: $text\n", FILE_APPEND);
        return true;
    }
}
