<?php

use Psr\Container\ContainerInterface;
use ZeebeClient\GatewayClient;

return [
    GatewayClient::class => static function (ContainerInterface $container): GatewayClient {
        return new GatewayClient(
            "{$_ENV['ZEEBE_URL']}:{$_ENV['ZEEBE_PORT']}",
            [
                'credentials' => \Grpc\ChannelCredentials::createInsecure(),
            ]
        );
    },
];
