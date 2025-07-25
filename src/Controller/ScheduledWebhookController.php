<?php

namespace App\Controller;

use App\Entity\WebhookSchedule;
use App\Form\ScheduledWebhookType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ScheduledWebhookController extends AbstractController
{
    #[Route('/scheduled-webhook/new', name: 'scheduled_webhook_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $scheduledWebhook = new WebhookSchedule();
        $scheduledWebhook->setOwner($this->getUser());

        $form = $this->createForm(ScheduledWebhookType::class, $scheduledWebhook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($scheduledWebhook);
            $entityManager->flush();

            return $this->redirectToRoute('scheduled_webhook_index'); // Adjust as needed
        }

        return $this->render('scheduled_webhook/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/scheduled-webhooks', name: 'scheduled_webhook_index')]
    #[IsGranted('ROLE_USER')]
    public function index(UserRepository $userRepository): Response
    {
        $userSecurity = $this->getUser()->getUserIdentifier();
        $webhooks = $userRepository->findOneByUsername($userSecurity)->getWebhookSchedules();

        return $this->render('scheduled_webhook/index.html.twig', [
            'webhooks' => $webhooks,
        ]);
    }
}
