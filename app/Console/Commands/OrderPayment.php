<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZeebeClient\ActivatedJob;
use ZeebeClient\ActivateJobsRequest;
use ZeebeClient\ActivateJobsResponse;
use ZeebeClient\CompleteJobRequest;
use ZeebeClient\GatewayClient;

class OrderPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:payment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay the order';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new GatewayClient('zeebe:26500', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);

        while (true) {
            $activeJobs = $client->ActivateJobs(new ActivateJobsRequest([
                'type'              => 'initiate-payment',
                'worker'            => 'initiate-payment',
                'maxJobsToActivate' => 1,
                'timeout'           => 60,
            ]));

            /** @var ActivateJobsResponse $response */
            foreach ($activeJobs->responses() as $response) {
                /** @var ActivatedJob $job */
                foreach ($response->getJobs() as $job) {
                    dump("Initiate payment for order: " . $job->getVariables());
                    sleep(rand(1, 4));

                    $completeRequest = new CompleteJobRequest([
                        'jobKey' => $job->getKey(),
                    ]);
                    $client->CompleteJob($completeRequest)->wait();
                }
            }
            sleep(1);
        }
    }
}
