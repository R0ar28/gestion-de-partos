<?php

namespace App\Controller;

use App\Entity\MedicalTeam;
use App\Entity\ParticipationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/participation/type')]
final class ParticipationTypeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/search/by-medical', name: 'search_participation_type_by_medical')]
    public function findParticipationTypeByMedical(Request $request): JsonResponse
    {

        $em = $this->entityManager;

        $id = $request->request->get('id');

        $participationType = $em->getRepository(ParticipationType::class)->findParticipationTypeByMedical($id);

        return $this->json($participationType);
    }
}
