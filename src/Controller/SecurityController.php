<?php

namespace App\Controller;

use League\Uri\QueryString;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(Request $request): Response
    {
        // NOOP
    }

    #[Route('/auth', name: 'app_auth')]
    public function auth(Request $request): Response
    {
        // NOOP
    }

    #[Route('/login', name: 'app_login')]
    public function login(RouterInterface $router, string $harvestOAuthClientId): Response
    {
        $remoteUrl = Uri::createFromString('https://id.getharvest.com/oauth2/authorize');
        $query = QueryString::build(
            [
                ['client_id', $harvestOAuthClientId],
                ['response_type', 'code'],
                ['redirect_uri', $router->generate('app_auth', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)],
            ]
        );
        $newUri = UriModifier::appendQuery($remoteUrl, $query);

        return $this->render(
            'security/index.html.twig',
            [
                'remote_url' => $newUri,
            ]
        );
    }
}
