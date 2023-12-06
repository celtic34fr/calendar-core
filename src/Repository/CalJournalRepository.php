<?php

namespace Celtic34fr\CalendarCore\Repository;

use Celtic34fr\CalendarCore\Entity\CalJournal;
use Celtic34fr\CalendarCore\Traits\DbPaginateTrait;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CalJournal>
 *
 * @method CalJournal|null find($id, $lockMode = null, $lockVersion = null)
 * @method CalJournal|null findOneBy(array $criteria, array $orderBy = null)
 * @method CalJournal[]    findAll()
 * @method CalJournal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CalJournalRepository extends ServiceEntityRepository
{
    use DbPaginateTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CalJournal::class);
    }

    public function save(CalJournal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CalJournal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllPaginate(int $currentPage = 1, int $limit =10, string $type = "array"): array
    {
        if (strtoupper($type) != "ARRAY" || strtoupper($type) != "JSON") $type = "ARRAY";
        $qb = $this->createQueryBuilder("rdv")
            ->orderBy('rdv.time_at', 'ASC')
            ->getQuery();
        $results = $this->paginateDoctrine($qb, $currentPage, $limit);
        return $this->formatEvents($results, $type);
    }

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
}