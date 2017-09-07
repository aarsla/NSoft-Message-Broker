<?php

namespace ProducerBundle\Command;

use AppBundle\Entity\Message;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SpamProducerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('producer:spam')
            ->addOption('messages', 'm', InputOption::VALUE_REQUIRED, 'Number of messages to send', "1")
            ->setDescription('Spam (Service B) consumer')
            ->setHelp('The <info>producer:spam</info> command will start spamming consumer (Service B) with messages.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $numberOfMessages = $input->getOption('messages');

        if (!ctype_digit($numberOfMessages) || $numberOfMessages < 1 || $numberOfMessages > 10000) {
            $output->writeln([
                'Invalid messages number, please use digits 1 - 10000',
                '',
            ]);
            die;
        }

        $producer = $this->getContainer()->get('old_sound_rabbit_mq.service_a_producer');
        $serializer = $this->getContainer()->get('serializer');

        for ($i = 0; $i < $numberOfMessages; $i++) {
            $message = new Message();
            $message->setAmount(rand(1*100, 999*100)/100);
            $json = $serializer->serialize($message, 'json', ['groups' => ['producer']]);
            $producer->publish($json);

            $output->writeln([
                'Spamming message # '.$i.' with amount of '.$message->getAmount(),
            ]);
        }
    }
}
