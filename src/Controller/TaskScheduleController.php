<?php

namespace App\Controller;

use App\Entity\TaskSchedule;
use App\Form\TaskScheduleType;
use App\Repository\TaskScheduleRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

#[Route('/task/schedule')]
final class TaskScheduleController extends AbstractController
{
    #[Route(name: 'app_task_schedule_index', methods: ['GET'])]
    public function index(TaskScheduleRepository $taskScheduleRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('task_schedule/index.html.twig', [
            'task_schedules' => $taskScheduleRepository->findByUser($user),
        ]);
    }

    #[Route('/new', name: 'app_task_schedule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $taskSchedule = new TaskSchedule();
        $form = $this->createForm(TaskScheduleType::class, $taskSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repeatEvery = $form->get('repeatEvery')->getData();
            if ($repeatEvery > 0) {
                $interval = new DateInterval("P{$repeatEvery}D");
                $taskSchedule->setRepeatEvery($interval);
            }

            $platformChoice = $form->get('targetPlatform')->getData();
            $pageName = $form->get('pageName')->getData();
            if ($platformChoice == 'facebook') { $taskSchedule->setFacebookPage($pageName); }
            if ($platformChoice == 'instagram') { $taskSchedule->setInstagramPage($pageName); }

            $taskSchedule->setOwner($user);

            $em->persist($taskSchedule);
            $em->flush();

            return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_schedule/new.html.twig', [
            'task_schedule' => $taskSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_schedule_show', methods: ['GET'])]
    public function show(TaskSchedule $taskSchedule): Response
    {
        if ($taskSchedule->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('task_schedule/show.html.twig', [
            'task_schedule' => $taskSchedule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_schedule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskSchedule $taskSchedule, EntityManagerInterface $entityManager): Response
    {
        if ($taskSchedule->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        $form = $this->createForm(TaskScheduleType::class, $taskSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repeatEvery = $form->get('repeatEvery')->getData();
            if ($repeatEvery > 0) {
                $interval = new DateInterval("P{$repeatEvery}D");
                $taskSchedule->setRepeatEvery($interval);
            }

            $platformChoice = $form->get('targetPlatform')->getData();
            $pageName = $form->get('pageName')->getData();
            if ($platformChoice == 'facebook') { $taskSchedule->setFacebookPage($pageName); }
            if ($platformChoice == 'instagram') { $taskSchedule->setInstagramPage($pageName); }

            $entityManager->flush();

            return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_schedule/edit.html.twig', [
            'task_schedule' => $taskSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_schedule_delete', methods: ['POST'])]
    public function delete(Request $request, TaskSchedule $taskSchedule, EntityManagerInterface $entityManager): Response
    {
        if ($taskSchedule->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$taskSchedule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($taskSchedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
    }
}
