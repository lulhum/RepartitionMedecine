<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageCategoryRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter;

class StageCategoryRepository extends EntityRepository
{

    public function filteredFindQB(StageCategoryFilter $filter, $max = null, $offset = null)
    {        
        $queryBuilder = $this->createQueryBuilder('s');
        if(!$filter->getLocations()->isEmpty()) {
            $queryBuilder->join('s.location', 'l', 'WITH', 'l.id IN(:locs)')
                         ->setParameter('locs', $filter->getLocations()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesOr()->isEmpty()) {
            $queryBuilder->join('s.categories', 'cor', 'WITH', 'cor.id IN(:catsor)')
                         ->setParameter('catsor', $filter->getCategoriesOr()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('s.categories', 'cand');
            foreach($filter->getCategoriesAnd() as $category) {
                $queryBuilder->andWhere('cand.id = :catsand')
                             ->setParameter('catsand', $category->getId());
            }
        }
        if(!is_null($max)) {
            $queryBuilder->setMaxResults($max);
            if(!is_null($offset)) {
                $queryBuilder->setFirstResult($offset);
            }
        }
        
        return $queryBuilder;
    }

    public function filteredFind(StageCategoryFilter $filter, $max = null, $offset = null)
    {
        return $this->filteredFindQB($filter, $max, $offset)->getQuery()->getResult();
    }

    public function filteredFindCount(StageCategoryFilter $filter)
    {
        return $this->filteredFindQB($filter)
                    ->select('COUNT(s)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }
        
}
