<?php

namespace Celtic34fr\CalendarCore\Repository;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Celtic34fr\CalendarCore\Entity\Parameter;
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
    public function findAllPaginate(int $currentPage = 1, int $limit = 10, string $type = "ARRAY"): array
    {
        if (strtoupper($type) != "ARRAY" || strtoupper($type) != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder("ce")
            ->orderBy('ce.start_at', 'ASC')
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
            DateTime $from = null, string $type = "ARRAY"): array
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder("ce")
            ->where('ce.start_at >= :from')
            ->orderBy('ce.start_at', 'ASC')
            ->setParameter('from', $from->format("Y-m-d"))
            ->getQuery()
        ;
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

    /**
     * @param Parameter $category
     * @param integer $currentPage
     * @param integer $limit
     * @param DateTime|null $from
     * @param string $type
     * @return array
     */
    public function findByCategoryPaginate(Parameter $category, int $currentPage = 1, int $limit = 10,
        DateTime $from = null, string $type = "ARRAY"): array
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";

        $qb =  $this->createQueryBuilder('ce')
            ->where("ce.nature = :nature")
            ->andWhere('ce.start_at >= :from')
            ->setParameter('nature', $category)
            ->setParameter('from', $from->format("Y-m-d"))
            ->orderBy('ce.start_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

    /**
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @param string $type
     * @return array
     */
    public function findEventStartBetweenDate(DateTime $from = null, DateTime $to = null, string $type = "ARRAY"):array
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";

        $qb = $this->createQueryBuilder('ce')
        ->where('ce.start_at >= :from')
        ->setParameter('from', $from->format("Y-m-d"))
        ;
        if ($to) $qb
            ->andWhere("ce.start_at <= :to")
            ->setParameter('to', $to->format("Y-m-d"));

        $qb->getQuery()
        ->getResult()
        ;

        $result = [
            'datas' => $qb ?? [],
            'page' => 1,
            'pages' => 1,
        ];
        return $this->formatEvents($result, $type);
    }

    /**
     * @param DateTime|null $from
     * @param DateTime|null $to
     * @param string $type
     * @return array
     */
    public function findEventEndBetweenDate(DateTime $from = null, DateTime $to = null, string $type = "ARRAY"): array
    {
        if (!$from) $from = new DateTime('now');
        $type = strtoupper($type);
        if ($type != "ARRAY" && $type != "JSON") $type = "ARRAY";

        $qb = $this->createQueryBuilder('ce')
        ->where('ce.end_at >= :from')
        ->setParameter('from', $from->format("Y-m-d"))
        ;
        if ($to) $qb
            ->andWhere("ce.end_at <= :to")
            ->setParameter('to', $to->format("Y-m-d"));

        $qb->getQuery()
        ->getResult()
        ;

        $result = [
            'datas' => $qb ?? [],
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
