<?php

namespace Celtic34fr\CalendarCore\Traits;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;

/** classe de gestion de la pagination au travers de l'ORM Doctrine */
trait DbPaginateTrait
{
    private function paginateDoctrine(Query $dql, int $currentPageNo = 1, int $limit = 10): array
    {
        if ($currentPageNo > 0 && $limit > 0) {
            $paginator = new Paginator($dql);
            $paginator->getQuery()
                ->setFirstResult($limit * ($currentPageNo - 1)) // Offset
                ->setMaxResults($limit) // Limit
            ;
            $datas = $paginator;
            $pages = ceil($paginator->count() / $limit);
            $page = $currentPageNo;
        } else {
            $datas = $dql->getResult();
            $pages = 0;
            $page = 0;
        }
        return [
            'datas' => $datas,
            'pages' => $pages,
            'page' => $page,
        ];
    }

    private function paginateManual(array $results, int $currentPageNo = 1, int $limit = 10): array
    {
        $datas = $results;
        $pages = ceil(sizeof($results) / $limit);
        if (!empty($results) && $currentPageNo > 0 && $limit > 0) {
            $datas = array_slice($results, $limit * ($currentPageNo - 1), $limit);
        }
        return [
            'datas' => $datas,
            'pages' => $pages,
            'page' => $currentPageNo,
        ];
    }
}
