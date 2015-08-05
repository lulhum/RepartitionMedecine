<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageCategoryRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageCategoryFilter;

class StageCategoryRepository extends EntityRepository
{

    public function filteredFind(StageCategoryFilter $filter)
    {        
        $queryBuilder = $this->createQueryBuilder('s');
        if(!$filter->getLocations()->isEmpty()) {
            $queryBuilder->join('s.location', 'l', 'WITH', 'l.id IN(:ids)')
                         ->setParameter('ids', $filter->getLocations()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesOr()->isEmpty()) {
            $queryBuilder->join('s.categories', 'cor', 'WITH', 'cor.id IN(:ids)')
                         ->setParameter('ids', $filter->getCategoriesOr()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('s.categories', 'cand');
            foreach($filter->getCategoriesAnd() as $category) {
                $queryBuilder->andWhere('cand.id = :id')
                             ->setParameter('id', $category->getId());
            }
        }
        
        return $queryBuilder->getQuery()->getResult();
    }
}
