<?php

namespace App\Repository\Game;

use App\Entity\Game\Connection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Connection>
 *
 * @method Connection|null find($id, $lockMode = null, $lockVersion = null)
 * @method Connection|null findOneBy(array $criteria, array $orderBy = null)
 * @method Connection[]    findAll()
 * @method Connection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConnectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Connection::class);
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public function save(Connection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public function remove(Connection $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Retrieves the highest ID.
     *
     * @return int The highest ID.
     */
    public function findHighestId(): ?int
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('MAX(c.id) as highestId');
        $result = $qb->getQuery()->getSingleScalarResult();

        return intval($result);
    }
}
