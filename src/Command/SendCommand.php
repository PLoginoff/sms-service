<?php

namespace App\Command;

use App\Service\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SendCommand extends Command
{
    protected static $defaultName = 'sms:send';

    protected function configure(): void
    {
        $this
            ->setDescription('send sms to number')
            ->addArgument('number', InputArgument::REQUIRED, 'a phone number')
            ->addArgument('message', InputArgument::REQUIRED, 'your message')
            ->addOption('gate', 'G', InputOption::VALUE_OPTIONAL, 'intel,sms,fake');
    }

    protected $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     * @throws \Exception
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $phone    = $input->getArgument('number');
        $message  = $input->getArgument('message');
        $gateName = $input->getOption('gate');

        $gate     = $this->registry->get($gateName);
        $io->note("send to $phone via " . $gate->getName());
        $status = $gate->send($phone, $message);
        $io->success($status ? 'Sent.' : 'Error :-(');
    }
}
