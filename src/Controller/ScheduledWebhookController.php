<?php

namespace App\Controller;

use App\Entity\WebhookSchedule;
use App\Form\ScheduledWebhookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ScheduledWebhookController extends AbstractController
{
    #[Route('/scheduled-webhook/new', name: 'scheduled_webhook_new')]
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

            return $this->redirectToRoute('homepage'); // Adjust as needed
        }

        return $this->render('scheduled_webhook/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
