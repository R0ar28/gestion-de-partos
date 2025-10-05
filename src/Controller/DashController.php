<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dash')]
final class DashController extends AbstractController
{
    #[Route('/', name: 'app_dash')]
    public function index(): Response
    {

       return $this->redirectToRoute('app_birth');

    }
}
