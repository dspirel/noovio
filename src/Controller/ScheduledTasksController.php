<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use App\Entity\User;
use App\Form\FacebookPostScheduleType;
use App\Service\GoogleCloudStorageService;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\WebhookSchedule;
use Doctrine\ORM\EntityManagerInterface;

final class ScheduledTasksController extends AbstractController
{
    #[Route('/scheduled/tasks', name: 'app_scheduled_tasks', methods: ['GET'])]
    public function index(): Response
    {
        dd(new \DateTimeImmutable('now'));
        /** @var User $user */
        $user = $this->getUser();

        $scheduledItems = $user->getWebhookSchedules();

        return $this->render('scheduled_tasks/index.html.twig', [
            'scheduled_items' => $scheduledItems,
        ]);
    }

    #[Route('/scheduled/tasks/new', name: 'app_scheduled_tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GoogleCloudStorageService $gcs,EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $images = $gcs->listUserImages($user->getUsername());

        $form = $this->createForm(FacebookPostScheduleType::class, null,[
            'images' => $images
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $formData = $form->getData();

            $whs = new WebhookSchedule;

            $whs->setOwner($user);
            $whs->setName($formData['name']);

            $publishAt = $formData['publishAt'];
            $startTime = $formData['startTime'];
            $combinedDateTime = new \DateTimeImmutable(
                $publishAt->format('Y-m-d') . ' ' . $startTime->format('H:i:s'),
                new \DateTimeZone('Europe/Zagreb')
            );
            $whs->setNextRunAt($combinedDateTime);
            // $images = $formData['images'];
            $whs->setData([
                'facebookPage' => $formData['facebookPage'],
                'aiPrompt' => $formData['aiPrompt'],
                'images' => $formData['images'],
                'repeatEvery' => $formData['repeatEvery']
            ]);

            // dd($whs);
            $em->persist($whs);
            $em->flush();

            return $this->redirectToRoute('app_scheduled_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('scheduled_tasks/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

// array:4 [▼
//   "name" => "test"
//   "facebook_page" => "test pagge"
//   "ai_prompt" => "test prompt"
//   "images" => array:2 [▼
//     0 => "noovio/68835fd534492_uptest.jpg"
//     1 => "noovio/6883876e926be_Hades_Aug19_04.jpg"
//   ]
// ]
