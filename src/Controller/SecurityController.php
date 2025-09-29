<?php

namespace App\Controller;


use App\Entity\Company;
use App\Entity\User;
use App\Service\GeneratePasswordService;
use App\Service\SendEmail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    private SendEmail $sendEmail;
    private Environment $twig;
    private EntityManagerInterface $entityManager;

    public function __construct(SendEmail $sendEmail, Environment $twig, EntityManagerInterface $entityManager){
        $this->sendEmail = $sendEmail;
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dash');
        }

        // Obtener el error de login (si existe) y el último nombre de usuario ingresado
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $attempts = $request->getSession()->get('login_attempts', 0);

        return $this->render('security/login.html.twig', [
            'error'        => $error,
            'attempts'     => $attempts,
            'lastUsername' => $lastUsername,
        ]);
    }
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Request $request): RedirectResponse
    {
        return new RedirectResponse('/');
    }

    #[Route(path: '/sendNewPassword', name: 'app_send_new_password', methods: ['POST'])]
    public function sendNewPassword(Request $request, UserPasswordHasherInterface $passwordHasher, GeneratePasswordService $generatePasswordService): Response
    {
        $email = $request->request->get('forgotPassEmail');

        if (!$email) {
            $this->addFlash('error', 'El correo es requerido.');
            return $this->redirectToRoute('app_login');
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !$user->isActiveInd()) {
            $this->addFlash('error', 'Usuario no encontrado.');
            return $this->redirectToRoute('app_login');
        }


        $now = new \DateTime();
        if ($user->getNextPasswordRecoveryAt() && $now < $user->getNextPasswordRecoveryAt()) {
            $this->addFlash('error', 'Ya se envió un correo de recuperación. Espere 10 minutos para solicitar uno nuevo.');
            return $this->redirectToRoute('app_login');
        }

        $newPassword = $generatePasswordService->generateRandomKey();
        $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($hashedPassword);

        $expiresAt = new \DateTime();
        $expiresAt->modify('+10 minutes');
        $user->setNextPasswordRecoveryAt($expiresAt);

        $this->entityManager->flush();

        $subject = 'Restablecer Contraseña';
        $content = sprintf(
            '<p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">Hola,</p>
        <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Tu nueva contraseña temporal es: <strong>%s</strong>
        </p>
        <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Esta contraseña es válida solamente durante <strong>10 minutos</strong>. Por favor, inicia sesión lo antes posible y cambia tu contraseña para que puedas seguir utilizando la plataforma sin inconvenientes.
        </p>
        <p style="color:black; font-size:16px; line-height:24px; margin:0 0 12px 0;">
            Si no cambias la contraseña dentro de este período, deberás solicitar una nueva.
        </p>',
            htmlspecialchars($newPassword, ENT_QUOTES, 'UTF-8')
        );

        $resetUrl = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $htmlContents = $this->twig->render('security/email.html.twig', [
            'title' => $subject,
            'messages' => $content,
            'resetUrl' => $resetUrl
        ]);

        $this->sendEmail->sendNotificacion($email, $subject, $htmlContents);

        $this->addFlash('success', 'La nueva contraseña fue enviada exitosamente.');

        return $this->redirectToRoute('app_login');
    }


    #[Route('/check-email', name: 'check_email', methods: ['GET'])]
    public function checkEmail(Request $request, EntityManagerInterface $em): JsonResponse {
        $email = $request->query->get('email');

        if (!$email) {
            return new JsonResponse(['exists' => false, 'error' => 'Email no proporcionado.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        return new JsonResponse(['exists' => $user !== null]);
    }

    #[Route('/check-dni', name: 'check_dni', methods: ['GET'])]
    public function checkDni(Request $request, EntityManagerInterface $em): JsonResponse {
        $dni = $request->query->get('dni');

        if (!$dni) {
            return new JsonResponse(['exists' => false, 'error' => 'RUT no proporcionado.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(Company::class)->findOneBy(['companyDni' => $dni]);

        return new JsonResponse(['exists' => $user !== null]);
    }

}
