<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZeebeClient\DeployProcessRequest;
use ZeebeClient\GatewayClient;
use ZeebeClient\ProcessRequestObject;

class DeployProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the informed process';

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

        $process = new ProcessRequestObject([
            'name' => 'order',
            'definition' => file_get_contents(storage_path('app/order.bpmn'))
        ]);

        $deployRequest = new DeployProcessRequest([
            'processes' => [$process]
        ]);

        [$rsp, $status] = $client->DeployProcess($deployRequest)->wait();

        if ($status->code === 0) {
            $this->info('Process deployed');
        }
    }
}
