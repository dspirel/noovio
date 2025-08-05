<?php

namespace App\Controller;

use App\Entity\TaskSchedule;
use App\Form\TaskScheduleType;
use App\Repository\TaskScheduleRepository;
use App\Repository\TaskPostRepository;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\AddPostsToScheduleType;

#[Route('/task/schedule')]
final class TaskScheduleController extends AbstractController
{
    #[Route(name: 'app_task_schedule_index', methods: ['GET'])]
    public function index(TaskScheduleRepository $taskScheduleRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $schedules = $taskScheduleRepository->findByUser($user);

        //could break if php v > 8.2 --- use DTO's
        foreach ($schedules as $schedule) {
            $schedule->postCount = count($schedule->getTaskPosts());
        }

        //dd($schedules);
        return $this->render('task_schedule/index.html.twig', [
            'task_schedules' => $schedules,
        ]);
    }

    #[Route('/{id}/posts',name: 'app_task_schedule_show_posts', methods: ['GET'])]
    public function showSchedulePosts(TaskSchedule $taskSchedule): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($taskSchedule->getOwner() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $schedulePosts = $taskSchedule->getTaskPosts();

        //dd($schedules);
        return $this->render('task_schedule/show_posts.html.twig', [
            'schedule_posts' => $schedulePosts,
        ]);
    }

    #[Route('/{id}/add-post', name: 'app_task_schedule_add_post', methods: ['GET', 'POST'])]
    public function addPostToSchedule(Request $request, TaskSchedule $taskSchedule, TaskPostRepository $taskPostRepository, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($taskSchedule->getOwner() !== $user) {
            throw $this->createAccessDeniedException();
        }

        $userPosts = $taskPostRepository->findByUser($user);

        $form = $this->createForm(AddPostsToScheduleType::class, null, [
            'available_posts' => $userPosts
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedPosts = $form->get('taskPosts')->getData(); // This is an array of TaskPost objects
            foreach ($selectedPosts as $post) {
                $post->addTaskSchedule($taskSchedule);
            }

            $em->flush();

            $this->addFlash('success', count($selectedPosts) . ' post(s) selected.');

            return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_schedule/add_posts.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // public function index(TaskPostRepository $taskPostRepository): Response
    // {
    //     /** @var User $user */
    //     $user = $this->getUser();

    //     return $this->render('task_post/index.html.twig', [
    //         'task_posts' => $taskPostRepository->findByUser($user),
    //     ]);
    // }

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
