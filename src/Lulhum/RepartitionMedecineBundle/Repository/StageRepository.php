<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageFilter;

class StageRepository extends EntityRepository
{

    public function filteredFindQB(StageFilter $filter)
    {        
        $queryBuilder = $this->createQueryBuilder('s');
        if(!$filter->getStageProposals()->isEmpty()) {
            $queryBuilder->join('s.proposal', 'p', 'WITH', 'p.id IN(:ids)')
                         ->setParameter('ids', $filter->getStageProposals()->map(function($ob) {return $ob->getId();})->toArray());
        }
        elseif(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty() || !$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('s.proposal', 'p');
        }
        if(!$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('p.period', 't', 'WITH', 't.id IN(:ids)')
                         ->setParameter('ids', $filter->getPeriods()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('p.category', 'c');
            if(!$filter->getCategoriesOr()->isEmpty()) {
                $queryBuilder->join('c.categories', 'cor', 'WITH', 'cor.id IN(:ids)')
                             ->setParameter('ids', $filter->getCategoriesOr()->map(function($ob) {return $ob->getId();})->toArray());
            }
            if(!$filter->getCategoriesAnd()->isEmpty()) {
                $queryBuilder->join('c.categories', 'cand');
                foreach($filter->getCategoriesAnd() as $category) {
                    $queryBuilder->andWhere('cand.id = :id')
                                 ->setParameter('id', $category->getId());
                }
            }
        }
        
        return $queryBuilder;
    }

    public function filteredFind(StageFilter $filter)
    {
        return $this->filteredFindQB($filter)->getQuery()->getResult();
    }

}