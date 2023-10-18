<?php

namespace Celtic34fr\CalendarCore\Repository;

use Celtic34fr\CalendarCore\Entity\CalType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<CalType>
 *
 * @method CalType|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalType|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalType[]    findAll()
 * @method CalType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalTypeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalType::class);
    }

    public function save(CalType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CalType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}