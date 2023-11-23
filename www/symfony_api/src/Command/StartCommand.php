<?php
// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// the "name" and "description" arguments of AsCommand replace the
// static $defaultName and $defaultDescription properties
#[AsCommand(
    name: 'start:app',
    description: 'Creates the project.',
    hidden: false,
    aliases: ['app:start:app']
)]
class StartCommand extends Command
{
    protected function configure()
    {
        $this
        // the name of the command (the part after "bin/console")
        ->setName('start:app')

        // the short description shown while running "php bin/console list"
        ->setDescription('Creates a new user.')

        // the full command description shown when running the command with
        // the "--help" option
        ->setHelp('This command allows you to create a user...')
    ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * 
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //$process = new Process(['php', 'bin/console', 'make:fixture'], "/var/www/symfony_api/");
        $process = new Process(['sh', 'init.sh'], "/var/www/symfony_api/");
        //$process->setPty(true);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $output->writeln([
                'doctrine:database:create',
                '============',
                'doctrine:database:migrate',
                '============',
                'fixture:load',
            ]);
        }
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        
        return Command::SUCCESS;
    }
}