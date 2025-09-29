<?php

namespace App\Controller;

use App\Entity\Party;
use App\Entity\PartyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/party')]
final class PartyController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_party')]
    public function index(): Response
    {
        return $this->render('party/index.html.twig', [
            'controller_name' => 'PartyController',
        ]);
    }

    #[Route('/find/by/party/type', name: 'search_party_by_party_type', methods: ['GET'])]
    public function searchPartyByPartyType(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $partyType = $em->getRepository(PartyType::class)->find($id);

        $parties = $em->getRepository(Party::class)->findBy(['partyType' => $partyType]);

        return $this->json(['success' => true, 'data' => $parties]);
    }
}
