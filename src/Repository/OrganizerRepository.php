<?php

namespace Celtic34fr\CalendarCore\Repository;

use Celtic34fr\CalendarCore\Entity\Organizer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Organizer>
 *
 * @method Organizer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Organizer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Organizer[]    findAll()
 * @method Organizer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrganizerRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Organizer::class);
    }

    public function save(Organizer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Organizer $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}