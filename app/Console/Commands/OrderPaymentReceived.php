<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use ZeebeClient\GatewayClient;
use ZeebeClient\PublishMessageRequest;

class OrderPaymentReceived extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:payment-received {--id=1 : Order identifier}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order payment received';

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

        $processRequest = new PublishMessageRequest([
            'name' => 'payment-received',
            'correlationKey' => $this->option('id')
        ]);

        [$rsp, $status] = $client->PublishMessage($processRequest)->wait();

        if ($status->code === 0) {
            $this->info('Order payment received');
            return;
        }

        $this->error('Order payment fail');
    }
}
