<?php


namespace App\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendNewBlogNotificationEmail extends Command
{
    protected static $defaultName = 'app:send-notification-about-new-articles';

    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    public function configure(): void
    {
        $this
            ->setDescription('Sends email to all users and notifies about updates.');
        $this
            ->setHelp('This command will executes scheduled.');

    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Emails were send to addresses:'
        ]);
    }
}