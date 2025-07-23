<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Entity\User;

final class AddUserController extends AbstractController
{
    #[Route('/add/user', name: 'app_add_user')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $noovio = $entityManager->getRepository(User::class)->findOneByUsername('noovio');
        $noovio->setRoles(['ROLE_ADMIN']);

        $entityManager->persist($noovio);
        $entityManager->flush();

        return $this->render('add_user/index.html.twig', [
            'controller_name' => $noovio->getUsername(),
        ]);
    }
}
