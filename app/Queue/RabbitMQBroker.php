<?php

namespace App\Queue;

use Illuminate\Queue\Jobs\JobName;
use VladimirYuldashev\LaravelQueueRabbitMQ\Queue\Jobs\RabbitMQJob as BaseJob;

class RabbitMQBroker extends BaseJob
{
    public function fire()
    {

        $payload = $this->payload();

        [$class, $method] = JobName::parse($payload['job']);

        ($this->instance = $this->resolve($class))->{$method}($this, $payload['data']);
    }
}
