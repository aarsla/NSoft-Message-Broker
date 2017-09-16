<?php

namespace ConsumerBundle\Workers;

use AppBundle\Entity\Account;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bridge\Monolog\Logger;
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

        $repository = $this->em->getRepository(Account::class);
        $account = $repository->findOneById(1);

        if (!$account) {
            $account = new Account();
            $account->setBalance(0);
            $account->setCurrency('EUR');
            $this->em->persist($account);
            $this->em->flush();
        }
    }

    /**
     * @param AMQPMessage $msg
     * @return bool
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function execute(AMQPMessage $msg)
    {
        $message = $this->serializer->deserialize($msg->getBody(), 'AppBundle\Entity\Message', 'json');
        $this->em->persist($message);
        $this->em->flush();

        $this->em->getConnection()->beginTransaction();

        try {
            $repository = $this->em->getRepository(Account::class);
            // locks the underlying database rows for concurrent Read and Write Operations
            $account = $repository->find(1, LockMode::PESSIMISTIC_WRITE);

            $initialBalance = $account->getBalance();
            $newBalance = $initialBalance + $message->getAmount();
            $account->setBalance($newBalance);

            $this->em->persist($account);
            $this->em->flush();
            $this->em->getConnection()->commit();

            $this->logger->info('Processed '.money_format('%i', $message->getAmount()).' EUR');
        } catch (\Exception $e) {
            $this->em->getConnection()->rollback();
            $this->logger->critical('Could not add '.money_format('%i', $message->getAmount()).' EUR');
            $this->logger->critical($e->getMessage());
            throw $e;
        }

        return true;
    }
}