<?php

namespace GustavoGama\ZeebeTutorialPhp;

use Symfony\Component\Console\{
    Command\Command,
    Input\InputInterface,
    Output\OutputInterface
};
use ZeebeClient\{
    ActivatedJob,
    ActivateJobsRequest,
    ActivateJobsResponse,
    CompleteJobRequest,
    GatewayClient
};

class OrderShipment extends Command
{
    protected static $defaultName = 'order:shipment';
    private GatewayClient $client;

    protected function configure(): void
    {
        $this->setDescription('Ship the order');
    }

    public function __construct(GatewayClient $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $activeJobs = $this->client->ActivateJobs(new ActivateJobsRequest([
                'type'              => 'ship-with-insurance',
                'worker'            => 'order-ship',
                'maxJobsToActivate' => 1,
                'timeout'           => 60,
            ]));

            /** @var ActivateJobsResponse $response */
            foreach ($activeJobs->responses() as $response) {
                /** @var ActivatedJob $job */
                foreach ($response->getJobs() as $job) {
                    $message = "Ship order with insurance: " . $job->getVariables();
                    $output->writeln("<info>{$message}</info>");
                    sleep(rand(1, 4));

                    $completeRequest = new CompleteJobRequest([
                        'jobKey' => $job->getKey(),
                    ]);
                    $this->client->CompleteJob($completeRequest)->wait();
                }
            }

            $activeJobs = $this->client->ActivateJobs(new ActivateJobsRequest([
                'type'              => 'ship-without-insurance',
                'worker'            => 'order-ship',
                'maxJobsToActivate' => 1,
                'timeout'           => 60,
            ]));

            /** @var ActivateJobsResponse $response */
            foreach ($activeJobs->responses() as $response) {
                /** @var ActivatedJob $job */
                foreach ($response->getJobs() as $job) {
                    $message = "Ship order withot insurance: " . $job->getVariables();
                    $output->writeln("<info>{$message}</info>");
                    sleep(rand(1, 4));

                    $completeRequest = new CompleteJobRequest([
                        'jobKey' => $job->getKey(),
                    ]);
                    $this->client->CompleteJob($completeRequest)->wait();
                }
            }

            sleep(1);
        }
    }
}
