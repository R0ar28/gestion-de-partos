<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentType;
use App\Entity\Party;
use App\Entity\PartyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/document')]
final class DocumentController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_document')]
    public function index(): Response
    {

        $em = $this->entityManager;

        $user = $this->getUser();

        if($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_SUPER_ADMIN')){
            $documents = $em->getRepository(Document::class)->findBy(['activeInd' => true]);
        }else{
            $party = $em->getRepository(Party::class)->findOneBy(['user' => $user]);

            $documents = $em->getRepository(Document::class)->findBy(['activeInd' => true, 'party' => $party]);
        }

        $documentsType = $em->getRepository(DocumentType::class)->findAll();
        $party = $em->getRepository(Party::class)->findBy(['activeInd' => true]);
        $partyType = $em->getRepository(PartyType::class)->findAll();

        return $this->render('document/index.html.twig', [
            'documents' => $documents,
            'documentsType' => $documentsType,
            'parties' => $party,
            'partyTypes' => $partyType,
        ]);
    }
}
