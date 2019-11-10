<?php

namespace App\Gates;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use TB\Etc\SMSIntel\SmsIntel as SmsIntelWrapper;

/**
 * Class SmsIntel
 * @package App\Gates
 */
class SmsIntel implements GateInterface, LoggerAwareInterface
{
    /** @var SmsIntelWrapper */
    protected $gate;

    /** @var string */
    protected $smsid;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * SmsIntel constructor.
     * @param SmsIntelWrapper $smsIntel
     * @param string $smsid
     */
    public function __construct(SmsIntelWrapper $smsIntel, string $smsid)
    {
        $this->gate  = $smsIntel;
        $this->smsid = $smsid;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'intel';
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
    public function send(string $phone, string $message): bool
    {
        // format 9260613031, so it's only Russia?
        $to = substr(preg_replace('/\D+/', '', '+7' . $phone), -10); // only russia?
        $this->logger->info("Intel: $to $message");
        $result = $this->gate->send(['text' => $message, 'smsid' => $this->smsid], [$to]);
        return (bool) $result['code'];
    }
}
