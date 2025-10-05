<?php

namespace App\Controller;

use App\Entity\MedicalTeam;
use App\Entity\MedicalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/medical/team')]
final class MedicalTeamController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/', name: 'app_medical_team')]
    public function index(): Response
    {

        $em = $this->entityManager;

        $medicalTeam = $em->getRepository(MedicalTeam::class)->findBy(['activeInd' => true]);
        $medicalType = $em->getRepository(MedicalType::class)->findAll();

        return $this->render('medical_team/index.html.twig', [
            'medicalTeam' => $medicalTeam,
            'medicalType' => $medicalType,
        ]);
    }
}
