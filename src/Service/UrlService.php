<?php

namespace App\Service;

use App\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UrlService
{
    private EntityManagerInterface $em;
    private Security $security;
    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function addUrl(string $longUrl, string $domain): Url
    {
        $url = new Url();
        $user = $this->security->getUser();

        $url->setHash($this->generateHash());
        $url->setShortUrl($_SERVER['HTTP_ORIGIN'] . "/" . $url->getHash());
        $url->setLongUrl($longUrl);
        $url->setCreatedAt(new \DateTime);
        $url->setDomain($domain);
        if ($user) {
            $url->setUser($user);
        }

        $this->em->persist($url);
        $this->em->flush();

        return $url;
    }

    public function generateHash(int $offset = 0, int $length = 8): string
    {
        return substr(md5(uniqid(mt_rand(), true)), $offset, $length);
    }

    public function parseUrl(string $url): ?string
    {
        $domain = parse_url($url, PHP_URL_HOST);

        if (!$domain) {
            return false;
        }

        if (!filter_var(gethostbyname($domain), FILTER_VALIDATE_IP)) {
            return false;
        }

        return $domain;
    }
}
