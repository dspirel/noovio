<?php

namespace App\Command;

use App\Entity\WebhookSchedule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Lock\LockFactory;

class SendScheduledWebhooksCommand extends Command
{
    protected static $defaultName = 'app:send-scheduled-webhooks';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-scheduled-webhooks')
            ->setDescription('Sends scheduled webhooks that are due.')
            ->addOption('loop', null, InputOption::VALUE_NONE, 'Run the command in a continuous loop');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpClient = HttpClient::create();

        if ($input->getOption('loop')) {
            while (true) {
                $start = new \DateTime();
                $this->processWebhooks($httpClient, $output);
                $end = new \DateTime();
                $duration = $end->getTimestamp() - $start->getTimestamp();
                if ($duration < 60) {
                    sleep(60 - $duration);
                }
            }
        } else {
            $this->processWebhooks($httpClient, $output);
        }

        return Command::SUCCESS;
    }

    private function processWebhooks($httpClient, OutputInterface $output): void
    {
        $repository = $this->entityManager->getRepository(WebhookSchedule::class);
        $now = new \DateTime();
        $webhooks = $repository->findBy(['nextRunTime' => $now], ['nextRunTime' => 'ASC']);

        foreach ($webhooks as $webhook) {
            if ($webhook->getNextRunTime() <= $now) {
                $response = $httpClient->request('POST', $webhook->getWebhookUrl(), [
                    'headers' => ['Content-Type' => 'application/json'],
                    'body' => $webhook->getJsonData(),
                ]);

                $output->writeln(sprintf('Webhook sent to %s with status %d', $webhook->getWebhookUrl(), $response->getStatusCode()));

                $webhook->calculateNextRunTime();
                $this->entityManager->persist($webhook);
            }
        }

        $this->entityManager->flush();
    }
}
