<?php

namespace ProducerBundle\Workers;

use AppBundle\Entity\Message;
use OldSound\RabbitMqBundle\RabbitMQ\Producer as Producer;
use Symfony\Component\Serializer\Serializer;

class ServiceA
{
    private $serializer;
    private $producer;

    /**
     * ServiceA constructor.
     * @param Serializer $serializer
     * @param Producer $producer
     */
    public function __construct(Serializer $serializer, Producer $producer)
    {
        $this->serializer = $serializer;

        $producer->setContentType('application/json');
        $this->producer = $producer;
    }

    /**
     * @param Message $message
     */
    public function process(Message $message)
    {
        $json = $this->serializer->serialize($message, 'json', ['groups' => ['producer']]);
        $this->producer->publish($json);
    }
}