<?php

namespace App\Service;

use App\Message\SmsSend;
use Enqueue\MessengerAdapter\Exception\RequeueMessageException;
use Symfony\Component\Messenger\DependencyInjection\MessengerPass;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Psr\Log\LoggerInterface;
use App\Service\GateRegistry;

final class Consumer implements MessageHandlerInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /** @var GateRegistry */
    private $registry;

    public function __construct(LoggerInterface $logger, GateRegistry $registry)
    {
        $this->logger   = $logger;
        $this->registry = $registry;
    }

    public function __invoke(SmsSend $smsSend)
    {
        $gate = $this->registry->select();

        if (!$gate) {
            $this->logger->info("We don't have any gate... sleep for 5 minutes");
            sleep(5 * 60);
            throw new \Exception('again');
        }

        try {
            $status = $gate->send($smsSend->getPhone(), $smsSend->getText());
            if (! $status) {
                $this->logger->warning('Some problems with gate ' . $gate->getName());
                throw new \Exception('Some problems with gate ' . $gate->getName());
            }
        } catch (\Exception $exception) {
            $this->registry->disable($gate);
            $this->logger->error($exception->getMessage());
        }
    }
}
