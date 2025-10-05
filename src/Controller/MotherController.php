<?php

namespace App\Controller;

use App\Entity\Birth;
use App\Entity\Mother;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/mother')]
final class MotherController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_mother')]
    public function index(): Response
    {

        $em = $this->entityManager;

        $mothers = $em->getRepository(Mother::class)->findBy(['activeInd' => true]);

        return $this->render('mother/index.html.twig', [
            'mothers' => $mothers,
        ]);
    }

    #[Route('/{id}', name: 'app_mother_show', methods: ['GET'])]
    public function show(Request $request): Response
    {

        $em = $this->entityManager;

        $id = $request->get('id');

        $mother = $em->getRepository(Mother::class)->find($id);

        $births = $em->getRepository(Birth::class)->findBy(['mother' => $mother]);


        return $this->render('mother/show.html.twig', [
            'mother' => $mother,
        ]);
    }

    #[Route('/new', name: 'app_mother_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $name = $request->get('name');
        $rut = $request->get('rut');
        $address = $request->get('address');
        $emergencyContact = $request->get('emergencyContact');
        $birthDate = $request->get('birthDate');

        if ($id != null) {
            $mother = $em->getRepository(Mother::class)->find($id);
            $mother->setUpdatedAt(new \DateTime());
        } else {
            $mother = new Mother();
            $mother->setCreatedAt(new \DateTime());
        }

        $mother->setName($name);
        $mother->setRut($rut);
        $mother->setAddress($address);
        $mother->setEntityUserId($this->getUser());

        if ($birthDate) {
            $birthDateObj = \DateTime::createFromFormat('Y-m-d', $birthDate);
            $mother->setBirthDate($birthDateObj);
        }

        $mother->setEmergencyContact($emergencyContact);
        $mother->setActiveInd(true);

        $em->persist($mother);
        $em->flush();

        return $this->redirectToRoute('app_mother');
    }


    #[Route('/delete', name: 'app_mother_delete', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {

        $em = $this->entityManager;

        $id = $request->get('id');

        $mother = $em->getRepository(Mother::class)->find($id);

        $mother->setActiveInd(false);
        $mother->setUpdatedAt(new \DateTime());
        $mother->setEntityUserId();

        $em->flush();


        return $this->json(['success' => true,]);
    }

    #[Route('/search/dni', name: 'mother_search_dni', methods: ['GET'])]
    public function searchDni(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $dni = $request->get('dni');

        $mother = $em->getRepository(Mother::class)->findOneBy(['rut' => $dni]);

        if($mother) {
            return $this->json([
                'exist' => true,

            ]);
        }else{
            return $this->json([
                'exist' => false,
            ]);
        }
    }
}
