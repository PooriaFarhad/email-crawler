<?php

namespace App\Repository;

use App\Entity\Email;
use App\Entity\Request;
use App\Entity\Url;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Email|null find($id, $lockMode = null, $lockVersion = null)
 * @method Email|null findOneBy(array $criteria, array $orderBy = null)
 * @method Email[]    findAll()
 * @method Email[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Email::class);
    }

    public function findPaginatedByRequest(int $requestId, int $limit, int $offset)
    {
        return $this->createQueryBuilder('email')
            ->select('email.id, email.email, url.url, request.host, request.id as request_id')
            ->join(Url::class, 'url', Join::WITH, 'url.id = email.url')
            ->join(Request::class, 'request', Join::WITH, 'request.id = url.request')
            ->andWhere('request.id = :requestId')
            ->setParameter('requestId', $requestId)
            ->orderBy('email.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
