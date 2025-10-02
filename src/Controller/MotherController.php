<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MotherController extends AbstractController
{
    #[Route('/mother', name: 'app_mother')]
    public function index(): Response
    {
        return $this->render('mother/index.html.twig', [
            'controller_name' => 'MotherController',
        ]);
    }
}
