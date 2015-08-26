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
            $queryBuilder->join('s.proposal', 'p', 'WITH', 'p.id IN(:props)')
                         ->setParameter('props', $filter->getStageProposals()->map(function($ob) {return $ob->getId();})->toArray());
        }
        elseif(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty() || !$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('s.proposal', 'p');
        }
        if(!$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('p.period', 't', 'WITH', 't.id IN(:periods)')
                         ->setParameter('periods', $filter->getPeriods()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('p.category', 'c');
            if(!$filter->getCategoriesOr()->isEmpty()) {
                $queryBuilder->join('c.categories', 'cor', 'WITH', 'cor.id IN(:catsor)')
                             ->setParameter('catsor', $filter->getCategoriesOr()->map(function($ob) {return $ob->getId();})->toArray());
            }
            if(!$filter->getCategoriesAnd()->isEmpty()) {
                $queryBuilder->join('c.categories', 'cand');
                foreach($filter->getCategoriesAnd() as $category) {
                    $queryBuilder->andWhere('cand.id = :catsand')
                                 ->setParameter('catsand', $category->getId());
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