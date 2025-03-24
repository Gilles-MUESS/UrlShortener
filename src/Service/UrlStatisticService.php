<?php

namespace App\Service;

use App\Entity\Url;
use App\Entity\UrlStatistic;
use App\Repository\UrlStatisticRepository;
use Doctrine\ORM\EntityManagerInterface;

class UrlStatisticService
{
    private EntityManagerInterface $em;
    private UrlStatisticRepository $urlStatisticRepository;
    public function __construct(EntityManagerInterface $em, UrlStatisticRepository $urlStatisticRepository)
    {
        $this->em = $em;
        $this->urlStatisticRepository = $urlStatisticRepository;
    }

    public function findOneByUrlAndDate(Url $url, \DateTimeInterface $date): UrlStatistic
    {
        $urlStatistic = $this->urlStatisticRepository->findOneBy(['url' => $url, 'date' => $date]);

        if (!$urlStatistic) {
            $urlStatistic = new UrlStatistic();
            $urlStatistic->setUrl($url);
            $urlStatistic->setDate($date);
        }

        return $urlStatistic;
    }

    public function incrementUrlStatistic(UrlStatistic $urlStatistic): UrlStatistic
    {
        $urlStatistic->setClicks($urlStatistic->getClicks() + 1);

        $this->em->persist($urlStatistic);
        $this->em->flush();

        return $urlStatistic;
    }
}
