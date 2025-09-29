<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentType;
use App\Form\DocumentTypeType;
use App\Repository\DocumentTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/document/type')]
final class DocumentTypeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(name: 'app_document_type', methods: ['GET'])]
    public function index(DocumentTypeRepository $documentTypeRepository): Response
    {
        return $this->render('document_type/index.html.twig', [
            'document_types' => $documentTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_document_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $em = $this->entityManager;

        $name = $request->get('name');

        $documentType = new DocumentType();
        $documentType->setName($name);
        $em->persist($documentType);
        $em->flush();

        return $this->redirectToRoute('app_document_type');
    }

    #[Route('/edit', name: 'app_document_type_edit', methods: ['POST'])]
    public function edit(Request $request): Response
    {

        $em = $this->entityManager;

        $id = $request->get('id');

        $name = $request->get('name');

        $documentType = $em->getRepository(DocumentType::class)->find($id);

        if ($documentType != null) {
            $documentType->setName($name);
            $em->persist($documentType);
            $em->flush();
        }

        $em->flush();

        return $this->redirectToRoute('app_document_type');
    }

    #[Route('/delete', name: 'app_document_type_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $em = $this->entityManager;

        $id = $request->get('id');
        try {
            $documentType = $em->getRepository(DocumentType::class)->find($id);

            $document = $em->getRepository(Document::class)->findBy(['documentType' => $documentType]);

            if (count($document) > 0) {
                return $this->json(['status' => 'error', 'message' => 'No se puede eliminar el tipo de documento porque tiene documentos asociados.']);
            } else {
                $em->remove($documentType);
                $em->flush();
                return $this->json(['status' => 'success', 'message' => 'Tipo de documento eliminado correctamente.']);
            }

        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => 'Ocurrio un problema al eliminar el tipo de documento.']);
        }
    }


}
