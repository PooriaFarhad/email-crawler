<?php

namespace App\Repository;

use App\Entity\Request;
use App\Enum\EnumStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Request|null find($id, $lockMode = null, $lockVersion = null)
 * @method Request|null findOneBy(array $criteria, array $orderBy = null)
 * @method Request[]    findAll()
 * @method Request[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    public function findByIdAsArray(int $id)
    {
        return $this->createQueryBuilder('request')
            ->andWhere('request.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    public function findPendingRequest(): ?Request
    {
        return $this->createQueryBuilder('request')
            ->andWhere('request.status IN (:statuses)')
            ->setParameter('statuses', [EnumStatus::NEW, EnumStatus::PROCESSING])
            ->orderBy('request.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllPaginated(int $limit, int $offset)
    {
        return $this->createQueryBuilder('request')
            ->orderBy('request.id', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }
}
