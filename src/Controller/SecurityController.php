<?php

namespace App\Controller;

use App\Service\HarvestApiFetcher;
use App\Service\HarvestOauth;
use League\Uri\QueryString;
use League\Uri\Uri;
use League\Uri\UriModifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class SecurityController extends AbstractController
{
    #[Route('/auth', name: 'app_auth')]
    public function auth(Request $request, HarvestOauth $harvestOauth, HarvestApiFetcher $fetcher): Response
    {
        $code = $request->get('code');
        $scopes = explode(' ', $request->get('scope'));

        $foundValidScope = false;
        foreach ($scopes as $scope) {
            $scopeParts = explode(':', $scope);
            if (2 !== count($scopeParts)) {
                continue;
            }

            if ('harvest' !== $scopeParts[0]) {
                continue;
            }

            if ((int)$this->getParameter('app.harvest.account_id') === (int)$scopeParts[1]) {
                $foundValidScope = true;
                break;
            }
        }

        if (!$foundValidScope) {
            throw new AccessDeniedException('Your Harvest account does not have access to this application');
        }

        // TODO: This can throw for stale authorization code
        $tokens = $harvestOauth->getTokens($code);
//        $userId = $harvestOauth->getUserId($tokens);
        $user = $fetcher->getUser($tokens);

        dd($code, $scopes, $user);
    }

    #[Route('/login', name: 'app_login')]
    public function login(RouterInterface $router): Response
    {
        $remoteUrl = Uri::createFromString('https://id.getharvest.com/oauth2/authorize');
        $query = QueryString::build(
            [
                ['client_id', $this->getParameter('app.harvest.oauth.client_id')],
                ['response_type', 'code'],
                ['redirect_uri', $router->generate('app_auth', referenceType: UrlGeneratorInterface::ABSOLUTE_URL)],
            ]
        );
        $newUri = UriModifier::appendQuery($remoteUrl, $query);

        return $this->render(
            'security/index.html.twig',
            [
                'remote_url' => $newUri,
                'controller_name' => 'SecurityController',
                'harvest_client_id' => $this->getParameter('app.harvest.oauth.client_id'),
            ]
        );
    }
}
