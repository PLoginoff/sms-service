<?php

namespace App\Gates;

use App\Gates\GateInterface;
use TB\Etc\SMSIntel\SmsIntel as SmsIntelWrapper;

class SmsIntel implements GateInterface
{
    /** @var SmsIntelWrapper */
    protected $gate;

    /** @var string */
    protected $smsid;

    public function __construct(SmsIntelWrapper $smsIntel, string $smsid)
    {
        $this->gate  = $smsIntel;
        $this->smsid = $smsid;
    }

    public function getName(): string
    {
        return 'intel';
    }

    public function send($phone, $message): bool
    {
        $to = substr(preg_replace('/\D+/', '', '+7' . $phone), -10); // only russia?
        $result = $this->gate->send(['text' => $message, 'smsid' => $this->smsid], [$to]);
        return (bool) $result['code'];
    }
}
