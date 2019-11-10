<?php

namespace App\Gates;

use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Easy Sms gateway
 * @package App\Sms
 */
class EasySms implements GateInterface, LoggerAwareInterface
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

    /** @var LoggerInterface */
    protected $logger;

    /**
     * @param string $login
     * @param string $password
     * @param string $connectId
     * @param string $smsid
     */
    public function __construct(string $login, string $password, string $connectId, string $smsid)
    {
        $this->login    = $login;
        $this->password = $password;
        $this->client = new Client([
            'base_uri' => str_replace('{connect_id}', $connectId, self::URL)
        ]);
        $this->smsid = $smsid;
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
        return 'easy';
    }

    /**
     * @inheritDoc
     */
    public function send(string $phone, string $message): bool
    {
        // format "7xxxxxxxxxx", only Russia?!
        $to = substr(preg_replace('/\D+/', '', '+7' . $phone), -11);
        $this->logger->info("Easy: $to $message");

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
