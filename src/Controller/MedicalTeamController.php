<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MedicalTeamController extends AbstractController
{
    #[Route('/medical/team', name: 'app_medical_team')]
    public function index(): Response
    {
        return $this->render('medical_team/index.html.twig', [
            'controller_name' => 'MedicalTeamController',
        ]);
    }
}
