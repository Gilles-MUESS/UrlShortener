<?php

namespace App\Controller;

use App\Repository\UrlRepository;
use App\Service\UrlService;
use App\Entity\User;
use App\Service\UrlStatisticService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @method User getUser()
 */
final class UrlController extends AbstractController
{
    private UrlService $urlService;
    private UrlStatisticService $urlStatisticService;
    public function __construct(UrlService $urlService, UrlStatisticService $urlStatisticService)
    {
        $this->urlService = $urlService;
        $this->urlStatisticService = $urlStatisticService;
    }
    #[Route('/url', name: 'app_url')]
    public function index(): Response
    {
        return $this->render('url/index.html.twig', [
            'controller_name' => 'UrlController',
        ]);
    }

    #[Route('/ajax/shorten', name: 'url_add')]
    public function add(Request $request): Response
    {
        $longUrl = $request->request->get('urlInput');

        if (!$longUrl) {
            return $this->json([
                'statusCode' => 400,
                'statusText' => 'MISSING_ARG_URL'
            ]);
        }

        $domain = $this->urlService->parseUrl($longUrl);

        if (!$domain) {
            return $this->json([
                'statusCode' => 400,
                'statusText' => 'INVALID_ARG_URL'
            ]);
        }

        $url = $this->urlService->addUrl($longUrl, $domain);

        return $this->json([
            'statusCode' => 200,
            'shortUrl' => $url->getShortUrl(),
            'longUrl' => $url->getLongUrl(),
        ]);
    }

    #[Route('/{hash}', name: 'url_view')]
    public function view(string $hash, UrlRepository $urlRepository): Response
    {
        $url = $urlRepository->findOneBy(['hash' => $hash]);

        if (!$url) {
            return $this->redirectToRoute('app_home');
        }

        if ($url->getUser()) {
            $urlStatistic = $this->urlStatisticService->findOneByUrlAndDate($url, new \DateTime());
            $this->urlStatisticService->incrementUrlStatistic($urlStatistic);
        }

        return $this->redirect($url->getLongUrl());
    }

    #[Route('/user/links', name: 'url_list')]
    public function list(UrlRepository $urlRepository): Response
    {
        $user = $this->getUser();

        if (!$user || $user->getUrls()->isEmpty()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('url/list.html.twig', [
            'urls' => $user->getUrls(),
        ]);
    }
}
