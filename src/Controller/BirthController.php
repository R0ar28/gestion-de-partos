<?php

namespace App\Controller;

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


        return $this->render('birth/index.html.twig', [
            'controller_name' => 'BirthController',
        ]);
    }
}
