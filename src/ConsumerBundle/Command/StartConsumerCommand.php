<?php

namespace ConsumerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StartConsumerCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('consumer:start')
            ->setDescription('Start (Service B) consumer')
            ->setHelp('The <info>consumer:start</info> command will start consuming messages (Service B).');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '================================',
            'Service B Consumer Started',
            '================================',
            'now go and send some messages...',
            '',
        ]);

        // Start consumer
        $command = $this->getApplication()->find('rabbitmq:consumer');
        $arguments = array(
            'command' => 'rabbitmq:consumer -w service_b',
            '-w' => true,
            'name' => 'service_b'
        );
        $dbRunInput = new ArrayInput($arguments);
        $command->run($dbRunInput, $output);
    }
}
