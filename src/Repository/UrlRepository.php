<?php

namespace App\Repository;

use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Url|null find($id, $lockMode = null, $lockVersion = null)
 * @method Url|null findOneBy(array $criteria, array $orderBy = null)
 * @method Url[]    findAll()
 * @method Url[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Url::class);
    }

    public function findNotCrawledUrls(): ?Url
    {
        return $this->createQueryBuilder('url')
            ->andWhere('url.crawled_at IS NULL')
            ->orderBy('url.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findNotCrawledUrlsByRequest(int $requestId)
    {
        return $this->createQueryBuilder('url')
            ->andWhere('url.crawled_at IS NULL')
            ->andWhere('url.request = :requestId')
            ->setParameter('requestId', $requestId)
            ->orderBy('url.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Url[]
     */
    public function findUrlsByRequest(int $requestId, int $maxResults = 100)
    {
        return $this->createQueryBuilder('url')
            ->andWhere('url.request = :requestId')
            ->setParameter('requestId', $requestId)
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getArrayResult();
    }
}
