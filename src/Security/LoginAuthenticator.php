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

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Su usuario no está registrado.');
        }

        if (!$user->isActiveInd() || ($user->getExpiresAt() && $user->getExpiresAt() <= new \DateTime()) ) {
            throw new CustomUserMessageAuthenticationException('Su cuenta está desactivada en la plataforma. Por favor, comuníquese con el administrador.');
        }

        $restrictedRoles = ['ROLE_SUPERVISOR', 'ROLE_AUDITOR', 'ROLE_ASSISTANT'];
        $userRoles = $user->getRoles();

        if (in_array('ROLE_USER_COMPANY', $userRoles, true)) {
            $companies        = $user->getCompany()->toArray();
            $companyCount     = count($companies);
            $hasActiveCompany = false;
            $hasConfigCompany = false;

            foreach ($companies as $company) {

                $parent = $company->getParentCompany();
                if ($parent) {
                    $parentStatus = $this->entityManager->getRepository(CompanyStatus::class)
                        ->findCurrentStatusEntityByCompanyId($parent->getId());
                    $pId = (int) $parentStatus->getStatusType()->getId();

                    if ($pId === 2) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tu compañía principal se encuentra actualmente en configuración.'
                        );
                    }
                    if ($pId !== 3) {
                        throw new CustomUserMessageAuthenticationException(
                            'La compañía principal asociada a tu cuenta no está activa.'
                        );
                    }
                }

                $currentStatus = $this->entityManager->getRepository(CompanyStatus::class)
                    ->findCurrentStatusEntityByCompanyId($company->getId());
                $statusId = (int)$currentStatus->getStatusType()->getId();

                if ($statusId === 3) {
                    $hasActiveCompany = true;
                    break;
                }
                if ($statusId === 2) {
                    $hasConfigCompany = true;
                }
            }

            if (! $hasActiveCompany) {
                if ($companyCount === 1) {
                    if ($hasConfigCompany) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tu compañía se encuentra actualmente en configuración.'
                        );
                    } else {
                        throw new CustomUserMessageAuthenticationException(
                            'La compañía asociada a tu cuenta no está activa.'
                        );
                    }
                } else {
                    if ($hasConfigCompany) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tus compañías se encuentran actualmente en configuración.'
                        );
                    } else {
                        throw new CustomUserMessageAuthenticationException(
                            'Las compañías asociadas a tu cuenta no están activas.'
                        );
                    }
                }
            }
        }
        elseif (array_intersect($restrictedRoles, $userRoles)) {
            $sectionUsers     = $user->getSectionUser()->toArray();
            $companies        = [];
            foreach ($sectionUsers as $su) {
                $c = $su->getSection()->getCompany();
                $companies[$c->getId()] = $c;
            }
            $companies     = array_values($companies);
            $companyCount  = count($companies);
            $hasActive     = false;
            $hasConfig     = false;

            foreach ($companies as $company) {

                $parent = $company->getParentCompany();
                if ($parent) {
                    $ps = $this->entityManager->getRepository(CompanyStatus::class)
                        ->findCurrentStatusEntityByCompanyId($parent->getId());
                    $pid = (int)$ps->getStatusType()->getId();

                    if ($pid === 2) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tu compañía principal se encuentra actualmente en configuración.'
                        );
                    }
                    if ($pid !== 3) {
                        throw new CustomUserMessageAuthenticationException(
                            'La compañía principal asociada a tu cuenta no está activa.'
                        );
                    }
                }

                $cs = $this->entityManager->getRepository(CompanyStatus::class)
                    ->findCurrentStatusEntityByCompanyId($company->getId());
                $sid = (int)$cs->getStatusType()->getId();

                if ($sid === 3) {
                    $hasActive = true;
                    break;
                }
                if ($sid === 2) {
                    $hasConfig = true;
                }
            }

            if (!$hasActive) {
                if ($companyCount === 1) {
                    if ($hasConfig) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tu compañía se encuentra actualmente en configuración.'
                        );
                    } else {
                        throw new CustomUserMessageAuthenticationException(
                            'La compañía asociada a tu cuenta no está activa.'
                        );
                    }
                } else {
                    if ($hasConfig) {
                        throw new CustomUserMessageAuthenticationException(
                            'Lo sentimos, tus compañías se encuentran actualmente en configuración.'
                        );
                    } else {
                        throw new CustomUserMessageAuthenticationException(
                            'Las compañías asociadas a tu cuenta no están activas.'
                        );
                    }
                }
            }
        }

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

        if (($user->getPasswordExpiresAt() instanceof \DateTimeInterface &&
            new \DateTimeImmutable() > $user->getPasswordExpiresAt()) || (
                $user->getNextPasswordRecoveryAt() instanceof \DateTimeInterface
            ))
        {
            $session->getFlashBag()->add(
                'info', 'Tu contraseña ha expirado. Por favor, actualiza tu contraseña.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('app_user_change_password')
            );
        }

        $event = new EventLog();
        $event->setUser($user);
        $event->setCreatedAt(new \DateTime());
        $event->setTypeEventLog($this->entityManager->getReference(TypeEventLog::class, 6));
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

        $session->set('companyId', 0);
        $session->set('hasChildCompanies', false);
        $session->set('hasMultipleCompanies', false);

        $companies = $user->getCompany();

        if ($companies && count($companies) > 0) {
            if (count($companies) > 1) {
                $session->set('hasMultipleCompanies', true);
            } else {
                $singleCompany = $companies[0];

                if ($singleCompany->isActiveInd() == 1) {

                    $session->set('companyId', $singleCompany->getId());
                    $hasChildren = $em->getRepository(Company::class)->hasActiveChild($singleCompany->getId());
                    $session->set('hasChildCompanies', $hasChildren);

                }
            }
        }

        if (in_array('ROLE_ASSISTANT', $user->getRoles(), true) && count($user->getRoles()) === 1 ) {
            return new RedirectResponse($this->urlGenerator->generate('app_assistant_index'));
        }else{
            return new RedirectResponse($this->urlGenerator->generate('app_home'));
        }

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
