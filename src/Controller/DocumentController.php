<?php

namespace App\Controller;

use App\Entity\Document;
use App\Entity\DocumentType;
use App\Entity\File;
use App\Entity\Party;
use App\Entity\PartyType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/document')]
final class DocumentController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'app_document')]
    public function index(): Response
    {

        $em = $this->entityManager;

        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $documents = $em->getRepository(Document::class)->findDocumentsBy(true);
        } else {
            $party = $em->getRepository(Party::class)->findOneBy(['user' => $user]);

            $documents = $em->getRepository(Document::class)->findDocumentsBy(true, $party->getId());
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

    #[Route('/new', name: 'app_new_document')]
    public function new(Request $request): Response
    {
        $em = $this->entityManager;

        $user = $this->getUser();

        $name = $request->get('document-name');
        $description = $request->get('description-document');
        $documentTypeId = $request->get('documentType');
        $partyId = $request->get('party');
        $fileUpload = $request->files->get('documentFile');


        if ($fileUpload instanceof UploadedFile) {

            $uploadPath = $this->getParameter('kernel.project_dir') . '/static/uploads/documents/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            $originalName = $fileUpload->getClientOriginalName();
            $hashedName = hash('ripemd160', uniqid()) . '.' . $fileUpload->guessExtension();

            $fileUpload->move($uploadPath, $hashedName);

            $file = new File();

            $file->setRoot('documents');
            $file->setNameFile($hashedName);
            $file->setName($originalName);
            $file->setSize(filesize($uploadPath . $hashedName));
            $file->setType($fileUpload->getClientMimeType());
            $em->persist($file);
            $em->flush();

            $documentType = $em->getRepository(DocumentType::class)->find($documentTypeId);

            $document = new Document();
            $document->setName($name);
            $document->setDocumentType($documentType);
            $document->setFile($file);
            $document->setActiveInd(True);
            $document->setDescriptionTxt($description);
            $document->setEntityUserId($user->getUserIdentifier());
            $document->setParty($em->getRepository(Party::class)->find($partyId));
            $em->persist($document);
            $em->flush();

            $this->addFlash('success', 'Archivo subido correctamente');
            return $this->redirectToRoute('app_document');

        }

        $this->addFlash('error', 'Error al subir el archivo');
        return $this->redirectToRoute('app_document');
    }

    #[Route('/edit/get', name: 'app_edit_get', methods: ['GET'])]
    public function editGet(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $document = $em->getRepository(Document::class)->find($id);

        $data = [];

        $status = 'error';

        if ($document != null) {

            $responsableUser = $em->getRepository(User::class)->find($document->getEntityUserId());

            $data = [
                'document' => [
                    'id' => $document->getId(),
                    'name' => $document->getName(),
                    'description' => $document->getDescriptionTxt(),
                    'documentType' => $document->getDocumentType()->getName(),
                    'createdAt' => $document->getCreatedAt()->format('Y-m-d H:i:s'),
                    'updatedAt' => $document->getUpdatedAt()?->format('Y-m-d H:i:s'),
                    'entityUser' => $responsableUser->getName(),
                ],
                'party' => [
                    'name' => $document->getParty()->getName(),
                    'partyType' => $document->getParty()->getPartyType()->getName(),
                    'identification' => $document->getParty()->getIdentification(),
                    'identificationType' => $document->getParty()->getIdentificationType()->getName(),
                ],
                'file' => [
                    'file' => $document->getFile()->getNameFile(),
                    'size' => $document->getFile()->getSize(),
                    'type' => $document->getFile()->getType(),
                ],
            ];

            $status = 'success';
        }


        return $this->json(['status' => $status, 'data' => $data]);
    }

    #[Route('/edit', name: 'app_edit_document', methods: ['POST'])]
    public function edit(Request $request): Response
    {
        $em = $this->entityManager;

        $user = $this->getUser();

        $id = $request->get('document-id-edit');
        $name = $request->get('document-name-edit');
        $description = $request->get('document-description-edit');

        $document = $em->getRepository(Document::class)->find($id);
        $document->setName($name);
        $document->setDescriptionTxt($description);
        $document->setUpdatedAt(new \DateTime());
        $document->setEntityUserId($user->getUserIdentifier());
        $em->flush();

        $this->addFlash('success', 'Documento editado correctamente');
        return $this->redirectToRoute('app_document');
    }

    #[Route('/delete', name: 'app_delete_document', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $document = $em->getRepository(Document::class)->find($id);

        if ($document != null) {
            $document->setActiveInd(False);
            $document->setUpdatedAt(new \DateTime());
            $document->setEntityUserId($this->getUser()->getUserIdentifier());
            $em->persist($document);
            $em->flush();
        }

        return $this->json(['status' => true]);
    }

    #[Route('/download', name: 'app_download_document', methods: ['GET'])]
    public function download(Request $request): Response
    {
        $em = $this->entityManager;

        $id = $request->get('id');
        $document = $em->getRepository(Document::class)->find($id);

        $path = $this->getParameter('kernel.project_dir') . '/static/uploads/'
            . $document->getFile()->getRoot() . '/'
            . $document->getFile()->getNameFile();

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return $this->file($path, $document->getName() . '.' . $extension);
    }


}
