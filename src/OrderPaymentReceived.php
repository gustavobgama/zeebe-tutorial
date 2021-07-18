<?php

namespace GustavoGama\ZeebeTutorialPhp;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Input\InputOption
};
use ZeebeClient\{
    GatewayClient,
    PublishMessageRequest
};

class OrderPaymentReceived extends Command
{
    protected static $defaultName = 'order:payment-received';
    private GatewayClient $client;

    protected function configure(): void
    {
        $this->setDescription('Order payment received')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                'Order identifier',
                1
            );
    }

    public function __construct(GatewayClient $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processRequest = new PublishMessageRequest([
            'name' => 'payment-received',
            'correlationKey' => (int) $input->getOption('id')
        ]);

        [, $status] = $this->client->PublishMessage($processRequest)->wait();

        if ($status->code === 0) {
            $output->writeln('<info>Order payment received</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<error>Order payment fail</error>');
        return Command::FAILURE;
    }
}
