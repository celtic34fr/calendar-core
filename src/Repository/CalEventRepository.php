<?php

namespace Celtic34fr\CalendarCore\Repository;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\Parameter;
use Celtic34fr\CalendarCore\Enum\EventEnums;
use Celtic34fr\CalendarCore\Traits\DbPaginateTrait;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalEvent>
 *
 * @method CalEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalEvent[]    findAll()
 * @method CalEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalEventRepository extends ServiceEntityRepository
{
    use DbPaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalEvent::class);
    }

    /**
     * @param CalEvent $entity
     * @param boolean $flush
     * @return void
     */
    public function save(CalEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param CalEvent $entity
     * @param boolean $flush
     * @return void
     */
    public function remove(CalEvent $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param integer $currentPage
     * @param integer $limit
     * @param string $type
     * @return array
     */
    public function findAllPaginate(int $currentPage = 1, int $limit =10, string $type = "array"): array
    {
        if (strtoupper($type) != "ARRAY" || strtoupper($type) != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder("rdv")
            ->orderBy('rdv.time_at', 'ASC')
            ->getQuery();
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

    /**
     * @param integer $currentPage
     * @param integer $limit
     * @param DateTime|null $from
     * @param string $type
     * @return array
     */
    public function findAllPaginateFromDate(int $currentPage = 1, int $limit = 10,
            DateTime $from = null, string $type = "array"): array
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder("rdv")
            ->where('rdv.start_at >= :from')
            ->orderBy('rdv.start_at', 'ASC')
            ->setParameter('from', $from->format("Y-m-d"))
            ->getQuery()
        ;
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

    /**
     * @param Parameter $category
     * @return void
     */
    public function findEventsByCategory(Parameter $category)
    {
        return $this->createQueryBuilder('ce')
            ->where("ce.nature = :nature")
            ->setParameter('nature', $category)
            ->orderBy('ce.start_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param integer $currentPage
     * @param integer $limit
     * @param DateTime|null $from
     * @param string $type
     * @return void
     */
    public function findAllRendezVousPaginate(int $currentPage = 1, int $limit = 10, DateTime $from = null,
        string $type = "array")
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder('rdv')
            ->where('rdv.nature = :nature')
            ->andWhere('rdv.time_at >= :from')
            ->setParameter('nature', EventEnums::ContactTel->_toString())
            ->setParameter('from', $from->format("Y-m-d"))
            ->getQuery()
            ;
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

    /**
     * @param DateTime $from
     * @param DateTime $to
     * @param string $type
     * @return void
     */
    public function findAllEventFromToDate(DateTime $from, DateTime $to, string $type = "array")
    {
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder('rdv')
        ->where('rdv.time_at >= :from')
        ->andWhere("rdv.time_at <= :to")
        ->setParameter('from', $from->format("Y-m-d"))
        ->setParameter('to', $to->format("Y-m-d"))
        ->getQuery()
        ->getResult()
        ;

        $result = [
            'datas' => $qb,
            'page' => 1,
            'pages' => 1,
        ];
        return $this->formatEvents($result, $type);
    }

//    /**
//     * @return CalEvent[] Returns an array of CalEvent objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CalEvent
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    private function formatEvents(array $events, string $type)
    {
        switch ($type) {
            case "JSON":
                $events['datas'] = json_encode($events['datas']);
                break;
        }
        return $events;

    }
}
