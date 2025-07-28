<?php

namespace App\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:post:task',
    description: 'Fetch tasks and post if scheduled',
)]
class PostTaskCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private HttpClientInterface $httpClient
    )
    {
        parent::__construct();
    }

    // protected function configure(): void
    // {
    //     $this
    //         ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
    //         ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
    //     ;
    // }
    // php bin/console app:post:task
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
            $this->httpClient->request('POST', 'https://studio-vision.app.n8n.cloud/webhook-test/357299f6-534d-4a9a-9185-bdb043f5cbbf', [
            'json' => [
                'data' => 'hello',
            ],
        ]);
        return Command::SUCCESS;
    }
}
