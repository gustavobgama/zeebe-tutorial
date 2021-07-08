<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZeebeClient\CreateProcessInstanceRequest;
use ZeebeClient\GatewayClient;

class CreateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:create {--id=1 : Order identifier}
                                         {--value=10 : Order value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order Create';

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

        $processRequest = new CreateProcessInstanceRequest([
            'bpmnProcessId' => 'order-process',
            'version' => 1,
            'variables' => json_encode([
                'orderId' => (int) $this->option('id'),
                'orderValue' => (float) $this->option('value'),
            ]),
        ]);

        [$rsp, $status] = $client->CreateProcessInstance($processRequest)->wait();

        if ($status->code === 0) {
            $this->info('Order created');
        }
    }
}
