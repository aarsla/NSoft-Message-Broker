<?php

namespace ConsumerBundle\Tests\Workers;

use AppBundle\Entity\Account;
use AppBundle\Entity\Message;
use ProducerBundle\Workers\ServiceA;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WorkersTest extends KernelTestCase
{
    private $service;
    private $em;

    public function setUp()
    {
        self::bootKernel();

        $serializer = static::$kernel->getContainer()->get('serializer');
        $producer = static::$kernel->getContainer()->get('old_sound_rabbit_mq.service_a_producer');

        $this->service = new ServiceA($serializer, $producer);
        $this->em = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    private function resetBalance() {
        $repository = $this->em->getRepository(Account::class);
        $account = $repository->findOneById(1);

        $account->setBalance(0);
        $account->setCurrency('EUR');
        $this->em->persist($account);
        $this->em->flush();

        // Truncate message database table
        $connection = $this->em->getConnection();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $platform = $connection->getDatabasePlatform();
        $truncateSql = $platform->getTruncateTableSQL('message');
        $connection->executeUpdate($truncateSql);
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');

        $this->em->clear();
    }

    public function testProducers()
    {
        $this->resetBalance();

        $checkBalance = 0;
        $testAmount = 15.35;

        for ($i = 0; $i < 10; $i++) {
            $message = new Message();
            $message->setAmount($testAmount);
            $this->service->process($message);

            $checkBalance = $checkBalance + $testAmount;
        }

        sleep(1);

        $repository = $this->em->getRepository(Account::class);
        $account = $repository->findOneById(1);
        $actualBalance = $account->getBalance();

        dump($account);

        $this->assertEquals($checkBalance, $actualBalance);
    }
}