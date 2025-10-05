<?php

namespace App\Controller;

use App\Entity\Birth;
use App\Entity\BirthType;
use App\Entity\MedicalTeam;
use App\Entity\Mother;
use App\Entity\ParticipationType;
use App\Entity\PhaseType;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/birth')]
final class BirthController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_birth')]
    public function index(): Response
    {
        $em = $this->entityManager;

        $birth = $em->getRepository(Birth::class)->findBirthWithPhase();

        $birthTypes = $em->getRepository(BirthType::class)->findAll();

        $medicalTeam = $em->getRepository(MedicalTeam::class)->findBy(['activeInd' => true]);
        $mothers = $em->getRepository(Mother::class)->findBy(['activeInd' => true]);

        $phaseTypes = $em->getRepository(PhaseType::class)->findAll();

        $participationType = $em->getRepository(ParticipationType::class)->findAll();

        $rooms = $em->getRepository(Room::class)->findAll();

        return $this->render('birth/index.html.twig', [
            'births' => $birth,
            'birthTypes' => $birthTypes,
            'phasesTypes' => $phaseTypes,
            'medicalTeams' => $medicalTeam,
            'participationType' => $participationType,
            'mothers' => $mothers,
            'rooms' => $rooms,
        ]);
    }
}
