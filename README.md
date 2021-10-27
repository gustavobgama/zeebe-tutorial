# Description

PHP implementation of the Camunda official [Zeebe tutorial](https://docs.camunda.io/docs/product-manuals/zeebe/deployment-guide/getting-started/index/). Instead of using the `zbctl` client as in the tutorial this repository has a PHP gRPC integration with Zeebe. Integration was very easy because of this [client](https://github.com/radek-baczynski/zeebe-php-client). The tutorial refers to the following business workflow:

![Business workflow](https://docs.camunda.io/assets/images/tutorial-3.0-complete-process-ccad27bdd9f510d4fd1314ae560ffff0.png)

# Installation

## Pre requisites

* [Docker](https://docs.docker.com/engine/install/)
* [Docker compose](https://docs.docker.com/compose/install/)

## Zeebe and Simple Monitor up and running

Clone the [docker-compose Zeebe](https://github.com/camunda-community-hub/zeebe-docker-compose) project and start it using the `simple-monitor` profile:

```shell
$ git clone git@github.com:camunda-community-hub/zeebe-docker-compose.git
$ cd zeebe-docker-compose/simple-monitor && docker-compose up -d
```

You can check the Zeebe Simple Monitor running [here](http://localhost:8082). You can use this tool to follow the execution of every order and see at what step the proccess is at any time.

## Install the PHP client implementation

```shell
$ git clone https://github.com/gustavobgama/zeebe-tutorial.git
$ cd zeebe-tutorial && docker-compose up -d
```

# Step by step

## Deploy the process workflow

```shell
$ docker-compose up process-deploy
```

## Run the mocks for some tasks

Simulate the following tasks `Initiate payment`, `Shipping without insurance` and `Shipping with insurance`.

```shell
$ docker-compose up order-payment order-shipment
```

## Create an order more expensive than 100

```shell
$ docker-compose run --rm php ./console order:create --id=10 --value=110
```

## Receive payment for the created order

```shell
$ docker-compose run --rm php ./console order:payment-received --id=10
```
## Create an order cheaper than 100

```shell
$ docker-compose run --rm php ./console order:create --id=11 --value=90
```

## Receive payment for the created order

```shell
$ docker-compose run --rm php ./console order:payment-received --id=11
```

## Check the results at the monitoring tool

Access the monitoring tool [http://localhost:8082](http://localhost:8082) and check both orders and its execution flow.