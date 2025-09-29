<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

#[Route('/profile')]
final class ProfileController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}', name: 'app_profile', methods: ["GET"])]
    public function index($id): Response
    {
        $em = $this->entityManager;

        $userExist = $em->getRepository(User::class)->find($id);

        $roles = $em->getRepository(Role::class)->findAll();

        $userData = [
            'id' => $userExist->getId(),
            'nameUser' => $userExist->getName(),
            'userEmail' => $userExist->getEmail(),
            'personName' => $userExist->getNamePerson(),
            'avatar' => $userExist->getAvatar(),
            'rolesString' => $userExist->getRoles(),
            'RoleId' => $userExist->getRolesId(),
            'status' => $userExist->isActiveInd(),
        ];

        return $this->render('profile/index.html.twig', [
            'user' => $userData,
            'roles' => $roles
        ]);
    }


    #[Route('/avatar/{id}', name: 'profile_avatar', methods: ["GET"])]
    public function edit($id): Response
    {

        if ($id == 'none') {

            $path = "media/user.png";

        } else {

            $path = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/static/avatar/" . $id;
        }

        // Generate response
        $response = new Response();

        // Set headers
        $response->headers->set('Cache-Control', 'private');
        $response->headers->set('Content-type', mime_content_type($path));
        $response->headers->set('Content-Disposition', 'inline; filename="' . $id . '";');
        $response->headers->set('Content-length', filesize($path));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');


        // Send headers before outputting anything
        $response->sendHeaders();

        $response->setContent(file_get_contents($path));

        return $response;

    }

    #[Route('/avatar/update', name: 'profile_avatar_update', methods: ['POST'])]
    public function updateAvatarProfile(Request $request, Environment $twig): Response
    {
        $em = $this->entityManager;

        $user = $this->getUser();

        $avatar = $request->files->get('avatar');

        $path = $small_path = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/static/avatar/" . $user->getAvatar();
        @unlink($path);

        $token = hash('sha512', $user->getId() . date('dmYHis'));
        $small_path = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/static/avatar/";

        $nameFile = $token . '.png';

        $avatar->move($small_path, $nameFile);
        $user->setAvatar($nameFile);
        $em->flush();

        $this->addFlash(
            'success',
            'Avatar actualizado correctamente'
        );


        return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);

    }

    #[Route('/password/update', name: 'profile_password_update', methods: ['POST'])]
    public function updatePassword(Request $request): Response
    {

        $user = $this->getUser();
        $passOld = $request->get('passOld');
        $passNew = $request->get('passNew');

        if ($this->passwordHasher->isPasswordValid($user, $passOld)) {
            $this->changePass($user, $passNew);
            $this->addFlash('success', 'Contraseña Actualizada');
        } else {
            $this->addFlash('error', 'La contraseña actual es incorrecta');
        }

        return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
    }

    #[Route('/update', name: 'profile_update', methods: ['POST'])]
    public function updateProfile(Request $request, Environment $twig): Response
    {

        $em = $this->entityManager;
        $user = $this->getUser();
        $profile = $request->get('profile');

        $user->setNamePerson($profile['fullName']);
        $em->persist($user);
        $em->flush();


        $this->addFlash(
            'success',
            'Datos del perfil actualizados'
        );

        return $this->redirectToRoute('app_profile', ['id' => $user->getId()]);
    }
}
