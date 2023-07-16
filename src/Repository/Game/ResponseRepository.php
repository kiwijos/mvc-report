<?php

namespace App\Repository\Game;

use App\Entity\Game\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Response>
 *
 * @method Response|null find($id, $lockMode = null, $lockVersion = null)
 * @method Response|null findOneBy(array $criteria, array $orderBy = null)
 * @method Response[]    findAll()
 * @method Response[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Response::class);
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public function save(Response $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public function remove(Response $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
