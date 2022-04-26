<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class FormLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private UrlGeneratorInterface $urlGenerator;
    private PasaiaLdapService $pasaiaLdapSrv;
    private UserRepository $userRepository;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UserRepository $userRepository,
        \App\Security\PasaiaLdapService $pasaiaLdapSrv
    )
    {
        $this->urlGenerator = $urlGenerator;
        $this->pasaiaLdapSrv = $pasaiaLdapSrv;
        $this->userRepository = $userRepository;
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username', '');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        $request->getSession()->set(Security::LAST_USERNAME, $username);
        $result = $this->pasaiaLdapSrv->checkCredentials($username, $password);

//        return new Passport(
//            new UserBadge($username),
//            new PasswordCredentials($request->request->get('password', '')),
//            [
//                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
//            ]
//        );

        if ($result) {
            $dbUser = $this->userRepository->findOneBy(['username' => $username]);
            // todo: check if the user is withing ldap groups
            if (!$dbUser) {
                // User is not present in the Database, let's create it
                $this->pasaiaLdapSrv->createDbUserFromLdapData($username);
            } else {
                // The User exists in the database, let's update it's data
                $this->pasaiaLdapSrv->updateDbUserDataFromLdapByUsername($username);
            }
        } else {
            throw new UserNotFoundException();
        }

        return new SelfValidatingPassport(new UserBadge($username), [new RememberMeBadge()]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        // return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('vue'));

    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
