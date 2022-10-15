<?php

namespace App\Security;

use App\Entity\User;
use App\Service\Fetchers\UserFetcher;
use App\Service\HarvestApiFetcher;
use App\Service\HarvestOauth;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class HarvestAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly HarvestOauth $harvestOauth,
        private readonly EntityManagerInterface $manager,
        private readonly RouterInterface $router,
        private readonly int $harvestAccountId,
        private readonly LoggerInterface $logger,
        private readonly UserFetcher $userFetcher
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/auth';
    }

    public function authenticate(Request $request): Passport
    {
        $code = $request->get('code');
        $scopes = explode(' ', (string) $request->get('scope'));

        return new Passport(
            new UserBadge(
                $code,
                function ($identifier) use ($scopes) {

                    $foundValidScope = false;
                    foreach ($scopes as $scope) {
                        $scopeParts = explode(':', $scope);
                        if (2 !== count($scopeParts)) {
                            continue;
                        }

                        if ('harvest' !== $scopeParts[0]) {
                            continue;
                        }

                        if ($this->harvestAccountId === (int)$scopeParts[1]) {
                            $foundValidScope = true;
                            break;
                        }
                    }

                    if (!$foundValidScope) {
                        throw new UserNotFoundException('Your Harvest account does not have access to this application');
                    }

                    // TODO: This can throw for stale authorization code
                    $tokens = $this->harvestOauth->getTokens($identifier);
                    $this->logger->debug('Tokens');
                    $user = $this->userFetcher->getUserByToken($tokens);

                    if (!$user) {
                        throw new UserNotFoundException('Could not find user');
                    }

                    $user->setHarvestAccessToken($tokens->accessToken);
                    $user->setHarvestRefreshToken($tokens->refreshToken);

                    $now = new \DateTimeImmutable();
                    $expires = $now->add(new \DateInterval('PT'.$tokens->expiresIn.'S'));
                    $user->setHarvestAccessTokenExpiration($expires);

                    $this->manager->persist($user);
                    $this->manager->flush();

                    return $user;
                }
            ),
            new CustomCredentials(
                static fn($credentials, User $user) => true,
                ''
            ),
            [
                (new RememberMeBadge())->enable(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('time'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        dd($exception, 'failure');
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
