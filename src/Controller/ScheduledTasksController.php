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

final class ScheduledTasksController extends AbstractController
{
    #[Route('/scheduled/tasks', name: 'app_scheduled_tasks', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $scheduledItems = $user->getWebhookSchedules();

        return $this->render('scheduled_tasks/index.html.twig', [
            'scheduled_items' => $scheduledItems,
        ]);
    }

    #[Route('/scheduled/tasks/new', name: 'app_scheduled_tasks_new', methods: ['GET', 'POST'])]
    public function new(Request $request, GoogleCloudStorageService $gcs): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $images = $gcs->listUserImages($user->getUsername());

        $form = $this->createForm(FacebookPostScheduleType::class, null,[
            'images' => $images
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
            return $this->redirectToRoute('app_scheduled_tasks', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('scheduled_tasks/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
