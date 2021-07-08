# Description

PHP implementation of the Camunda official [Zeebe tutorial](https://docs.camunda.io/docs/product-manuals/zeebe/deployment-guide/getting-started/index/). Instead of using the `zbctl` client as in the tutorial this repository has a PHP gRPC integration with Zeebe. Integration was very easy because of this [client](https://github.com/radek-baczynski/zeebe-php-client).  

# Installation

## Pre requisites

[Docker](https://docs.docker.com/engine/install/) and [docker compose](https://docs.docker.com/compose/install/) are pre requisites and must be installed.

## Zeebe and Operate up and running

Clone the [docker-compose Zeebe](https://github.com/camunda-community-hub/zeebe-docker-compose) project and start it using the `operate` profile:

```shell
$ git clone git@github.com:camunda-community-hub/zeebe-docker-compose.git
$ cd zeebe-docker-compose/operate && docker-compose up -d
```

You can check the Operate running [here](http://localhost:8080) (demo/demo).

## Install the PHP client implementation

```shell
$ git clone https://github.com/gustavobgama/zeebe-tutorial.git
$ cd zeebe-tutorial && docker-compose up -d
```

# Step by step

## Deploy the order process

```shell
$ docker-compose exec php php artisan process:deploy
```

## Start the workers (order payment and shipment): 

```shell
$ docker-compose exec php php artisan order:payment
$ docker-compose exec php php artisan order:shipment
```

## Create an order

```shell
$ docker-compose exec php php artisan order:create --id=10 --value=100
```

## Receive payment received message (for the created order)

```shell
$ docker-compose exec php php artisan order:payment-received --id=10
```