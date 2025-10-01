<?php

namespace App\Controller;

use App\Entity\IdentificatorType;
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

        $em = $this->entityManager;

        $partyType = $em->getRepository(PartyType::class)->findAll();
        $identificationType = $em->getRepository(IdentificatorType::class)->findAll();

        $partiesCount = [];

        foreach ($partyType as $party) {
            $parties = $em->getRepository(Party::class)->findBy(['partyType' => $party]);

            $partiesCount[$party->getId()] = [
                'name' => $party->getName(),
                'count' => count($parties),
                'parties' => $parties,
            ];
        }

        return $this->render('party/index.html.twig', [
            'parties' => $partiesCount,
            'partyTypes' => $partyType,
            'identificationTypes' => $identificationType,
        ]);
    }

    #[Route('/new', name: 'app_new_party', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $em = $this->entityManager;

        $name = $request->get('party-name');
        $identificationTypeId = $request->get('party-id-type');
        $identification = $request->get('party-identifier');
        $partyTypeId = $request->get('party-type');

        $identificationType = $em->getRepository(IdentificatorType::class)->find($identificationTypeId);
        $partyType = $em->getRepository(PartyType::class)->find($partyTypeId);

        $party = new Party();
        $party->setName($name);
        $party->setPartyType($partyType);
        $party->setIdentificationType($identificationType);
        $party->setIdentification($identification);
        $party->setActiveInd(True);
        $party->setEntityUserId($this->getUser()->getUserIdentifier());
        $em->persist($party);
        $em->flush();

        return $this->redirectToRoute('app_party');
    }

    #[Route('/find/by/party/type', name: 'search_party_by_party_type', methods: ['GET'])]
    public function searchPartyByPartyType(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $partyType = $em->getRepository(PartyType::class)->find($id);

        $parties = $em->getRepository(Party::class)->findBy(['partyType' => $partyType]);

        $partyData = [];

        foreach($parties as $party){
            $partyData[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'identification' => $party->getIdentification(),
            ];
        }

        return $this->json(['success' => true, 'data' => $partyData]);
    }
}
