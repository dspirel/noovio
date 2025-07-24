<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/marketing/facebook')]
final class FacebookPostController extends AbstractController
{
    #[Route('/post', name: 'app_facebook_post')]
    public function index(): Response
    {
        return $this->render('facebook_post/index.html.twig', [
            'controller_name' => 'FacebookPostController',
        ]);
    }
}
