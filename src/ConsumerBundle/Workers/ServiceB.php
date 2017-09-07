<?php

namespace ConsumerBundle\Workers;

use AppBundle\Entity\Account;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Acl\Exception\Exception;
use Symfony\Component\Serializer\Serializer;

class ServiceB implements ConsumerInterface
{
    private $serializer;
    private $em;
    private $logger;

    /**
     * ServiceB constructor.
     * @param Serializer $serializer
     * @param EntityManager $em
     * @param Logger $logger
     */
    public function __construct(Serializer $serializer, EntityManager $em, Logger $logger)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->logger = $logger;
    }
    
    /**
     * @param AMQPMessage $msg
     * @return bool
     */
    public function execute(AMQPMessage $msg)
    {
        $message = $this->serializer->deserialize($msg->getBody(), 'AppBundle\Entity\Message', 'json');
        $this->em->persist($message);

        $repository = $this->em->getRepository(Account::class);
        $account = $repository->findOneById(1);

        if (!$account) {
            $account = new Account();
            $account->setBalance(0);
            $account->setCurrency('EUR');
            $this->em->persist($account);
            $this->em->flush();
        }

        $initialBalance = $account->getBalance();
        $newBalance = $initialBalance + $message->getAmount();
        $account->setBalance($newBalance);

        try {
            $this->em->flush();
            $this->logger->info('Processed '.money_format('%i', $message->getAmount()).' EUR');
        } catch (Exception $e) {
            $this->logger->crit('Could not add '.money_format('%i', $message->getAmount()).' EUR');
            $this->logger->crit($e->getMessage());
        }

        $this->em->clear();
        return true;
    }
}