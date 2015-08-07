<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageProposalRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalFilter;

class StageProposalRepository extends EntityRepository
{

    public function filteredFindQB(StageProposalFilter $filter)
    {        
        $queryBuilder = $this->createQueryBuilder('s');
        if(!$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('s.period', 'p', 'WITH', 'p.id IN(:ids)')
                         ->setParameter('ids', $filter->getPeriods()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getStageCategories()->isEmpty()) {
            $queryBuilder->join('s.category', 'c', 'WITH', 'c.id IN(:ids)')
                         ->setParameter('ids', $filter->getStageCategories()->map(function($ob) {return $ob->getId();})->toArray());
        }
        elseif(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('s.category', 'c');
        }
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
        
        return $queryBuilder;
    }

    public function filteredFind(StageProposalFilter $filter)
    {
        return $this->filteredFindQB($filter)->getQuery()->getResult();
    }
}