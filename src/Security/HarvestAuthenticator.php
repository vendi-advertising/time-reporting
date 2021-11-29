<?php

namespace App\Security;

use App\Entity\User;
use App\Service\HarvestApiFetcher;
use App\Service\HarvestOauth;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class HarvestAuthenticator extends AbstractAuthenticator
{
    private HarvestOauth $harvestOauth;
    private HarvestApiFetcher $fetcher;
    private EntityManagerInterface $manager;
    private RouterInterface $router;
    private int $harvestAccountId;

    public function __construct(HarvestOauth $harvestOauth, HarvestApiFetcher $fetcher, EntityManagerInterface $manager, RouterInterface $router, int $harvestAccountId)
    {
        $this->harvestOauth = $harvestOauth;
        $this->fetcher = $fetcher;
        $this->manager = $manager;
        $this->router = $router;
        $this->harvestAccountId = $harvestAccountId;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/auth';
    }

    public function authenticate(Request $request): PassportInterface
    {
        $code = $request->get('code');
        $scopes = explode(' ', $request->get('scope'));

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
                    // $userId = $harvestOauth->getUserId($tokens);
                    $user = $this->fetcher->getUser($tokens);

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
                static function ($credentials, User $user) {
                    return true;
                },
                ''
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->router->generate('time'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        dd('failure');
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
