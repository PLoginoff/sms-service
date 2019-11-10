<?php

namespace App\Gates;

use GuzzleHttp\Client;

/**
 * Easy Sms gateway
 * @package App\Sms
 */
class EasySms implements GateInterface
{
    /** @var string */
    protected const URL = 'https://xml.smstec.ru/api/v1/easysms/{connect_id}/send_sms';

    /** @var string */
    protected $login;

    /** @var string */
    protected $password;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $smsid;

    public function __construct(string $login, string $password, string $connectId, string $smsid)
    {
        $this->login    = $login;
        $this->password = $password;
        $this->client = new Client([
            'base_uri' => str_replace('{connect_id}', $connectId, self::URL)
        ]);
        $this->smsid = $smsid;
    }

    public function getName(): string
    {
        return 'easy';
    }

    public function send($phone, $message): bool
    {
        $to = substr(preg_replace('/\D+/', '', '+7' . $phone), -11); // only russia?

        $response = $this->client->get('', [
            'debug' => $_ENV['APP_DEBUG'],
            'query' => [
                'login' => $this->login,
                'password' => $this->password,
                'text' => $message,
                'originator' => $this->smsid,
                'phone' => $to,
            ]
        ]);
        $text = $response->getBody()->getContents();
        return !preg_match('/error/ui', $text);
    }
}
