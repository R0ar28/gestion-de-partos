<?php

namespace App\Security;

use App\Entity\Company;
use App\Entity\CompanyStatus;
use App\Entity\EventLog;
use App\Entity\LoginAttempt;
use App\Entity\SectionUser;
use App\Entity\TypeEventLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginAuthenticator extends AbstractLoginFormAuthenticator implements PasswordAuthenticatedUserInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    private $session;
    private $router;
    private $entityManager;

    public function __construct(private UrlGeneratorInterface $urlGenerator, RequestStack $requestStack, RouterInterface $router, EntityManagerInterface $entityManager)
    {
        $this->session = $requestStack->getSession();
        $this->router = $router;
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->get('email', '');
        $password = $request->get('password', '');

        $request->getSession()
            ->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        $em = $this->entityManager;
        $session = $request->getSession();
        $ip = $request->getClientIp();

        $session->set('login_attempts', 0);
        $session->set('alertPasswordExpired','La alerta expiró');

        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();

        $event = new EventLog();
        $event->setUser($user);
        $event->setCreatedAt(new \DateTime());
        $event->setTypeEventLog($this->entityManager->getReference(TypeEventLog::class, 1));
        $event->setDescription($user->getName() . ' inició sesión');
        $event->setEntityId($user->getId());
        $event->setIndView(1);
        $event->setTypeEntity('Login');
        $this->entityManager->persist($event);
        $this->entityManager->flush();

        $loginAttempt = new LoginAttempt();
        $loginAttempt->setUser($user);
        $loginAttempt->setCreatedAt(new \DateTime());
        $loginAttempt->setIpAddress($ip);
        $loginAttempt->setSuccessInd(true);
        $this->entityManager->persist($loginAttempt);
        $this->entityManager->flush();




        return new RedirectResponse($this->urlGenerator->generate('app_dash'));

    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $attempts = $this->session->get('login_attempts', 0);
        $ip = $request ? $request->getClientIp() : null;

        $attempts++;
        $this->session->set('login_attempts', $attempts);

        if ($exception instanceof CustomUserMessageAuthenticationException) {
            $message = $exception->getMessageKey();
        } else {
            $message = 'Credenciales inválidas.';
        }

        if ($exception->getMessage() === 'The presented password is invalid.') {
            $message = 'La contraseña ingresada es incorrecta.';
        }

        $loginAttempt = new LoginAttempt();
        $loginAttempt->setCreatedAt(new \DateTime());
        $loginAttempt->setIpAddress($ip);
        $loginAttempt->setSuccessInd(false);
        $this->entityManager->persist($loginAttempt);
        $this->entityManager->flush();

        $this->session->getFlashBag()->add('error', $message);

        return new RedirectResponse($this->router->generate('app_login'));
    }


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function getPassword(): ?string
    {
        return null;
    }
}
