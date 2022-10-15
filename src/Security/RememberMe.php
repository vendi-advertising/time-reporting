<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\TimeReportingException;
use App\Service\HarvestOauth;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\RememberMe\AbstractRememberMeHandler;
use Symfony\Component\Security\Http\RememberMe\RememberMeDetails;

class RememberMe extends AbstractRememberMeHandler
{
    public function __construct(
        private readonly HarvestOauth $harvestOauth,
        private readonly EntityManagerInterface $manager,
        UserProviderInterface $userProvider,
        RequestStack $requestStack,
        array $options = [],
        LoggerInterface $logger = null
    ) {
        parent::__construct($userProvider, $requestStack, $options, $logger);
    }

    protected function processRememberMe(RememberMeDetails $rememberMeDetails, UserInterface $user): void
    {
        if (!$user instanceof User) {
            throw new TimeReportingException('The remembered user was not a User entity');
        }

        if (!$user->getHarvestRefreshToken()) {
            return;
        }

        $tokens = $this->harvestOauth->refreshTokens($user);
        $user->setHarvestAccessToken($tokens);
        $this->manager->persist($user);
        $this->manager->flush();
    }

    public function createRememberMeCookie(UserInterface $user): void
    {
        $this->createCookie(
            new RememberMeDetails(User::class, $user->getUserIdentifier(), time() + $this->options['lifetime'], 'test')
        );
    }

    private function generateHash(string $tokenValue): string
    {
        return hash_hmac('sha256', $tokenValue, (string)$this->secret);
    }
}