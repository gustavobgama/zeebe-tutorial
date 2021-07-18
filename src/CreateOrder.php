<?php

namespace GustavoGama\ZeebeTutorialPhp;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Input\InputOption
};
use ZeebeClient\{
    CreateProcessInstanceRequest,
    GatewayClient
};

class CreateOrder extends Command
{
    protected static $defaultName = 'order:create';
    private GatewayClient $client;

    protected function configure(): void
    {
        $this->setDescription('Order Create')
            ->addOption(
                'id',
                null,
                InputOption::VALUE_REQUIRED,
                'Order identifier',
                1
            )
            ->addOption(
                'value',
                null,
                InputOption::VALUE_REQUIRED,
                'Order value',
                10
            );
    }

    public function __construct(GatewayClient $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $processRequest = new CreateProcessInstanceRequest([
            'bpmnProcessId' => 'order-process',
            'version' => 1,
            'variables' => json_encode([
                'orderId' => (int) $input->getOption('id'),
                'orderValue' => (float) $input->getOption('value'),
            ]),
        ]);

        [, $status] = $this->client->CreateProcessInstance($processRequest)->wait();

        if ($status->code === 0) {
            $output->writeln('<info>Order created</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<error>Order not created</error>');
        return Command::FAILURE;
    }
}
