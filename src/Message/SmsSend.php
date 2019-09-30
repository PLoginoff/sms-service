<?php

namespace App\Message;

class SmsSend
{
    protected $phone;

    protected $text;

    public function __construct(string $phone, string $text)
    {
        $this->phone = $phone;
        $this->text  = $text;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getText()
    {
        return $this->text;
    }

    public function __toString()
    {
        return json_encode(['phone' => $this->phone, 'text' => $this->text], JSON_UNESCAPED_UNICODE);
    }
}
