<?php

namespace App\Controller;

use App\Entity\TaskPost;
use App\Form\TaskPostType;
use App\Repository\TaskPostRepository;
use App\Service\GoogleCloudStorageService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;

#[Route('/task/post')]
final class TaskPostController extends AbstractController
{
    #[Route(name: 'app_task_post_index', methods: ['GET'])]
    public function index(TaskPostRepository $taskPostRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('task_post/index.html.twig', [
            'task_posts' => $taskPostRepository->findByUser($user),
        ]);
    }

    #[Route('/generate', name: 'app_task_post_generate', methods: ['GET', 'POST'])]
    public function generate(TaskPostRepository $taskPostRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('task_post/index.html.twig', [
            'task_posts' => $taskPostRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_post_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, GoogleCloudStorageService $gcs): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $images = $gcs->listUserImages($user->getUsername());

        $taskPost = new TaskPost();
        $form = $this->createForm(TaskPostType::class, $taskPost, [
            'images' => $images
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskPost->setOwner($user);
            $taskPost->setPosted(false);
            // dd($taskPost);

            $entityManager->persist($taskPost);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_post/new.html.twig', [
            'task_post' => $taskPost,
            'form' => $form,
        ]);
    }
    //TODO: ADD POST TO SCHEDULE!!!
    #[Route('/{id}', name: 'app_task_post_show', methods: ['GET'])]
    public function show(TaskPost $taskPost): Response
    {
        if ($taskPost->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('task_post/show.html.twig', [
            'task_post' => $taskPost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_post_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskPost $taskPost, EntityManagerInterface $entityManager, GoogleCloudStorageService $gcs): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($taskPost->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $images = $gcs->listUserImages($user->getUsername());

        $form = $this->createForm(TaskPostType::class, $taskPost, [
            'images' => $images
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_post/edit.html.twig', [
            'task_post' => $taskPost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_post_delete', methods: ['POST'])]
    public function delete(Request $request, TaskPost $taskPost, EntityManagerInterface $entityManager): Response
    {
        if ($taskPost->getOwner() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }
        if ($this->isCsrfTokenValid('delete'.$taskPost->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($taskPost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
