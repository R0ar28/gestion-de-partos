<?php

namespace App\Controller;

use App\Entity\IdentificatorType;
use App\Entity\Party;
use App\Entity\PartyType;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

        $roles = $em->getRepository(Role::class)->findAll();

        $partiesCount = [];

        foreach ($partyType as $party) {
            if($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_SUPER_ADMIN')){
                $parties = $em->getRepository(Party::class)->findBy(['partyType' => $party]);
            }else{
                $parties = $em->getRepository(Party::class)->findBy(['partyType' => $party, 'activeInd' => true]);
            }


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
            'roles' => $roles,
        ]);
    }

    #[Route('/new', name: 'app_new_party', methods: ['POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $this->entityManager;

        $partyData = $request->get('party');
        $name = $partyData['party_name'] ?? null;
        $identificationTypeId = $partyData['party_id_type'] ?? null;
        $identification = $partyData['party_identifier'] ?? null;
        $partyTypeId = $partyData['party_type'] ?? null;

        $userData = $request->request->all()['user'] ?? null; // devuelve array o null
        $user = null;

        if ($userData && !empty($userData['user_name']) && !empty($userData['user_email'])) {
            $userName = $userData['user_name'] ?? null;
            $userEmail = $userData['user_email'] ?? null;
            $avatarBase64 = $userData['user_avatar'] ?? null;
            $roles = $request->request->all()['user-roles'] ?? [];

            if ($userName && $userEmail) {
                $user = new User();
                $user->setName($userName);
                $user->setEmail($userEmail);
                $user->setAvatar($avatarBase64);
                $user->setNamePerson($name);
                $user->setActiveInd(true);
                $password = $passwordHasher->hashPassword($user, '1234');
                $user->setPassword($password);
                $user->setEntityUserId($this->getUser()->getUserIdentifier());
                foreach ($roles as $role){
                    $roleEntity = $em->getRepository(Role::class)->find($role);
                    $user->addRole($roleEntity);
                }


                $em->persist($user);
                $em->flush();
            }else{
                $this->addFlash('error', 'Error al crear el usuario');
                return $this->redirectToRoute('app_party');
            }
        }

        $identificationType = $em->getRepository(IdentificatorType::class)->find($identificationTypeId);
        $partyType = $em->getRepository(PartyType::class)->find($partyTypeId);

        $party = new Party();
        $party->setName($name);
        $party->setPartyType($partyType);
        $party->setIdentificationType($identificationType);
        $party->setIdentification($identification);
        $party->setActiveInd(true);
        $party->setEntityUserId($this->getUser()->getUserIdentifier());

        if ($user) {
            $party->setUser($user);
        }

        $em->persist($party);
        $em->flush();

        $this->addFlash('success', 'Entidad creada correctamente');
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

        foreach ($parties as $party) {
            $partyData[] = [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'identification' => $party->getIdentification(),
            ];
        }

        return $this->json(['success' => true, 'data' => $partyData]);
    }

    #[Route('/edit/get', name: 'edit_party_get', methods: ['GET'])]
    public function editGet(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');

        $party = $em->getRepository(Party::class)->find($id);

        $dataParty = [
            'party' => [
                'id' => $party->getId(),
                'name' => $party->getName(),
                'identification' => $party->getIdentification(),
                'identificationType' => $party->getIdentificationType()->getId(),
                'partyType' => $party->getPartyType()->getId(),
            ],
            'user' => [
                'id' => $party->getUser()?->getId(),
                'name' => $party->getUser()?->getName(),
                'email' => $party->getUser()?->getEmail(),
                'avatar' => $party->getUser()?->getAvatar(),
                'roles' => $party->getUser()?->getRolesId(),
            ]
        ];


        return $this->json(['success' => true, 'data' => $dataParty]);
    }

    #[Route('/edit', name: 'edit_party', methods: ['POST'])]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $this->entityManager;

        $partyData = $request->get('party');
        $id = $partyData['party_id'];
        $name = $partyData['party_name'] ?? null;
        $identificationTypeId = $partyData['party_id_type'] ?? null;
        $identification = $partyData['party_identifier'] ?? null;
        $partyTypeId = $partyData['party_type'] ?? null;


        $userData = $request->get('user');
        if ($userData && !empty($userData['user_name']) && !empty($userData['user_email'])) {
            $userName = $userData['user_name'] ?? null;
            $userEmail = $userData['user_email'] ?? null;
            $avatarBase64 = $userData['user_avatar'] ?? null;
            $roles = $request->request->all()['user-roles'] ?? [];
            $userId = $userData['user_id'] ?? null;

            if ($userName && $userEmail && $userId) {
                $user = $em->getRepository(User::class)->find($userId);
                $user->setName($userName);
                $user->setEmail($userEmail);
                $user->setAvatar($avatarBase64);
                $user->setNamePerson($name);
                $user->setEntityUserId($this->getUser()->getUserIdentifier());
                $rolesActuals = $user->getRolesId();

                foreach ($rolesActuals as $role){
                    $roleEntity = $em->getRepository(Role::class)->find($role);
                    $user->removeRole($roleEntity);
                }
                foreach ($roles as $role){
                    $roleEntity = $em->getRepository(Role::class)->find($role);
                    $user->addRole($roleEntity);
                }

                $em->flush();
            }else{
                $this->addFlash('error', 'Error al crear el usuario');
                return $this->redirectToRoute('app_party');
            }
        }

        $identificationType = $em->getRepository(IdentificatorType::class)->find($identificationTypeId);
        $partyType = $em->getRepository(PartyType::class)->find($partyTypeId);

        $party = $em->getRepository(Party::class)->find($id);
        $party->setName($name);
        $party->setPartyType($partyType);
        $party->setIdentificationType($identificationType);
        $party->setIdentification($identification);
        $party->setActiveInd(true);
        $party->setEntityUserId($this->getUser()->getUserIdentifier());
        $em->flush();

        return $this->redirectToRoute('app_party');
    }

    #[Route('/delete', name: 'delete_party', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {

        $em = $this->entityManager;

        $id = $request->get('id');
        $type = filter_var($request->get('type'), FILTER_VALIDATE_BOOLEAN);

        $party = $em->getRepository(Party::class)->find($id);

        $party->setActiveInd($type);
        $party->setEntityUserId($this->getUser()->getUserIdentifier());
        $party->setUpdatedAt(new \DateTime());
        $em->flush();

        $em = $this->entityManager;

        return $this->json(['status' => true]);
    }
}
