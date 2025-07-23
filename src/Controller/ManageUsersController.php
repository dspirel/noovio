<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ManageUsersController extends AbstractController
{
    #[Route('/aadmin/manage/users', name: 'app_manage_users', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.username != :excludedUsername')
            ->setParameter('excludedUsername', 'noovio')
            ->getQuery()
            ->getResult();

        return $this->render('manage_users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/aadmin/delete/user/{id}', name: 'app_delete_user', methods: ['GET'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->render('home/index.html.twig');
    }

    #[Route('/aadmin/user/{id}', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        int $id
    ): Response {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData()) {
                $plainPassword = $form->get('password')->getData();
                if (!empty($plainPassword)) {
                    $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                    $user->setPassword($hashedPassword);
                }
            }


            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_manage_users');
        }

        return $this->render('manage_users/edit.html.twig', [
            'form' => $form,
            'user' => $user,
        ]);
    }
}
