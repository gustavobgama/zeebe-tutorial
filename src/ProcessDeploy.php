<?php

namespace GustavoGama\ZeebeTutorialPhp;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface,
    Input\InputArgument
};
use ZeebeClient\{
    DeployProcessRequest,
    GatewayClient,
    ProcessRequestObject
};

class ProcessDeploy extends Command
{
    protected static $defaultName = 'process:deploy';
    private GatewayClient $client;

    protected function configure(): void
    {
        $this->setDescription('Deploy the informed process')
            ->addArgument(
                'filename',
                InputArgument::OPTIONAL,
                'The BPMN file to deploy',
                'order.bpmn'
            );
    }


    public function __construct(GatewayClient $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__ . '/../' . $input->getArgument('filename');
        $process = new ProcessRequestObject([
            'name' => 'order',
            'definition' => file_get_contents($filePath)
        ]);

        $deployRequest = new DeployProcessRequest([
            'processes' => [$process]
        ]);

        [, $status] = $this->client->DeployProcess($deployRequest)->wait();

        if ($status->code === 0) {
            $output->writeln('<info>Process deployed</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<error>Process not deployed</error>');
        return Command::FAILURE;
    }
}
