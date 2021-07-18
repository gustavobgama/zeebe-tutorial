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

class OrderPayment extends Command
{
    protected static $defaultName = 'order:payment';
    private GatewayClient $client;

    protected function configure(): void
    {
        $this->setDescription('Pay the order');
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
                'type'              => 'initiate-payment',
                'worker'            => 'initiate-payment',
                'maxJobsToActivate' => 1,
                'timeout'           => 60,
            ]));

            /** @var ActivateJobsResponse $response */
            foreach ($activeJobs->responses() as $response) {
                /** @var ActivatedJob $job */
                foreach ($response->getJobs() as $job) {
                    $message = "Initiate payment for order: " . $job->getVariables();
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
