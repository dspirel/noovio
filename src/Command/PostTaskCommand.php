<?php

namespace App\Command;

use App\Entity\WebhookSchedule;
use App\Repository\WebhookScheduleRepository;
use DateInterval;
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
        /** @var WebhookSchedule $task */
        $task = $this->em->getRepository(WebhookSchedule::class)->findNextRunnableTask();

        if ($task){
            $response = $this->httpClient->request('POST', 'https://studio-vision.app.n8n.cloud/webhook-test/453dc032-b809-419f-a68d-7010a366f02f', [
            'json' => [
                'data' => $task->getData()
                ], //TODO get signed urls, remove repeatEvery,webhook url from data
                    //replace facebookPage with token for that page
            ]);
        }
        //should first confirm post, or create posts before for approval
        // IDEA: Schedule->create post->create post->create post

        if ($response->getStatusCode() == 200) {
            $daysToAdd = $task->getData()['repeatEvery'];
            $updatedTaskRunAt = $task->getNextRunAt()->add(new DateInterval("P{$daysToAdd}D"));
            $task->setNextRunAt($updatedTaskRunAt);
            $this->em->flush();
        }
        // $formattedDate = $updateTaskRunAt->format('Y-m-d H:i:s');
        // $statusCode = $response->getStatusCode();
        // $output->writeln("{$statusCode}");
        return Command::SUCCESS;
    }


}
