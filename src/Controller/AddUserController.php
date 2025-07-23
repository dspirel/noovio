<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;

final class AddUserController extends AbstractController
{
    #[Route('/admin/add/user', name: 'app_add_user')]
    public function index(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user, ['is_edit' => false]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newUser = $form->getData();
            $plainPassword = $newUser->getPassword();
            $hashedPassword = $passwordHasher->hashPassword($newUser, $plainPassword);
            $newUser->setPassword($hashedPassword);
            //$newUser->setFacebookIdentifer('test');

            $entityManager->persist($newUser);
            $entityManager->flush();

            // ... perform some action, such as saving the task to the database

            return $this->redirectToRoute('app_home');
        }

        // $noovio = $entityManager->getRepository(User::class)->findOneByUsername('noovio');
        // $noovio->setRoles(['ROLE_ADMIN']);

        // $entityManager->persist($noovio);
        // $entityManager->flush();

        return $this->render('add_user/index.html.twig', [
            'form' => $form,
        ]);
    }
}
