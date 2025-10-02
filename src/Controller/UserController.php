<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $em = $this->entityManager;
        $roles = $em->getRepository(Role::class)->findAll();
        $memberCount = [];

        foreach ($roles as $itemRole) {
            $memberCount[$itemRole->getId()] = [
                'users' => [],
                'name' => $itemRole->getTextName(),
                'id' => $itemRole->getId()
            ];

            $userChild = $em->getRepository(User::class)->getUserByRole($itemRole->getName(), 1);

            foreach ($userChild as $item) {
                $memberCount[$itemRole->getId()]['users'][] = $item;
            }
        }

        return $this->render('user/index.html.twig', [
            'roles' => $roles,
            'memberCount' => $memberCount
        ]);
    }

    #[Route('/create', name: 'app_user_create', methods: ['POST'])]
    public function detail(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $em = $this->entityManager;

        $userData = $request->request->all()['user'] ?? null;
        $roles = $request->request->all()['user-roles'] ?? [];

        $userName = $userData['user_name'] ?? null;
        $userEmail = $userData['user_email'] ?? null;
        $avatarBase64 = $userData['user_avatar'] ?? null;
        $name = $userData['name_person'] ?? null;
        $id = $userData['user_id'] ?? null;

        if ($userName && $userEmail) {
            if ($id != null) {
                // Editar
                $user = $em->getRepository(User::class)->find($id);
                if (!$user) {
                    $this->addFlash('error', 'Usuario no encontrado');
                    return $this->redirectToRoute('app_user');
                }

                // Limpiar roles actuales antes de asignar los nuevos
                foreach ($user->getRolesEntity() as $roleEntity) {
                    $user->removeRole($roleEntity);
                }

                $msg = 'Usuario actualizado correctamente';
            } else {
                // Crear
                $user = new User();
                $password = $passwordHasher->hashPassword($user, '1234');
                $user->setPassword($password);
                $em->persist($user);

                $msg = 'Usuario creado correctamente';
            }

            // Setear datos comunes
            $user->setNameUser($userName);
            $user->setEmail($userEmail);
            $user->setAvatar($avatarBase64);
            $user->setNamePerson($name);
            $user->setActiveInd(true);
            $user->setEntityUserId($this->getUser()->getUserIdentifier());

            // Asignar roles
            foreach ($roles as $roleId) {
                $roleEntity = $em->getRepository(Role::class)->find($roleId);
                if ($roleEntity) {
                    $user->addRole($roleEntity);
                }
            }

            $em->flush();

            $this->addFlash('success', $msg);
            return $this->redirectToRoute('app_user');
        } else {
            $this->addFlash('error', 'Faltan datos para crear el usuario');
            return $this->redirectToRoute('app_user');
        }
    }


    #[Route('/edit/get', name: 'app_user_edit_get', methods: ['GET'])]
    public function editGet(Request $request): Response
    {
        $em = $this->entityManager;

        $id = $request->get('id');
        $user = $em->getRepository(User::class)->find($id);

        $userData = [
            'nameUser'   => $user->getName(),
            'userEmail'  => $user->getEmail(),
            'personName' => $user->getNamePerson(),
            'avatar'     => $user->getAvatar(),
            'roles'      => $user->getRolesId(),  // en minúscula para seguir convención
            'status'     => $user->isActiveInd(),
        ];

        return $this->json(['status' => true, 'data' => $userData]);
    }

    #[Route('/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request): JsonResponse
    {
        $em = $this->entityManager;

        $id = $request->get('id');
        $type = filter_var($request->get('type'), FILTER_VALIDATE_BOOLEAN);

        $user = $em->getRepository(User::class)->find($id);

        $user->setActiveInd(false);
        $user->setEntityUserId($this->getUser()->getUserIdentifier());
        $user->setUpdatedAt(new \DateTime());
        $em->flush();

        return $this->json(['status' => true]);
    }

}
