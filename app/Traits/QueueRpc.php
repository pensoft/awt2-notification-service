<?php
namespace App\Traits;

use App\Exceptions\QueueReplayOnlyInJob;
use App\Queue\RabbitMQBroker;
use PhpAmqpLib\Message\AMQPMessage;

trait QueueRpc
{
    /**
     * @param string $msg
     * @return void
     */
    public function sendReply(string $msg = ''): void
    {
        if($this->job instanceof RabbitMQBroker) {
            if ($this->job->getRabbitMQMessage()->has('reply_to')) {
                $channel = $this->job->getRabbitMQMessage()->getChannel();
                $correlationId = $this->job->getRabbitMQMessage()->get('correlation_id');
                $AMQPMessage = new AMQPMessage(
                    $msg,
                    ['correlation_id' => $correlationId]
                );

                $channel->basic_publish(
                    $AMQPMessage,
                    '',
                    $this->job->getRabbitMQMessage()->get('reply_to')
                );
            }

            return;
        }

        throw new QueueReplayOnlyInJob('Send Reply Method can be executed only in job implemented RabbitMQJob');
    }
}
