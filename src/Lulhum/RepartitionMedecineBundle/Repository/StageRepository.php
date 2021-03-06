<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageFilter;

class StageRepository extends EntityRepository
{

    public function filteredFindQB(StageFilter $filter, $max = null, $offset = null)
    {        
        $queryBuilder = $this->createQueryBuilder('s');
        if(!$filter->getStageProposals()->isEmpty()) {
            $queryBuilder->join('s.proposal', 'p', 'WITH', 'p.id IN(:props)')
                         ->setParameter('props', $filter->getStageProposals()->map(function($ob) {return $ob->getId();})->toArray());
        }
        elseif(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty() || !$filter->getPeriods()->isEmpty() || count($filter->getPromotions()) != 0) {
            $queryBuilder->join('s.proposal', 'p');
        }        
        if(!$filter->getPeriods()->isEmpty()) {
            $queryBuilder->join('p.period', 't', 'WITH', 't.id IN(:periods)')
                         ->setParameter('periods', $filter->getPeriods()->map(function($ob) {return $ob->getId();})->toArray());
        }
        if(!$filter->getStageCategories()->isEmpty() || !$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty()) {
            if(!$filter->getStageCategories()->isEmpty()) {
                $queryBuilder->join('p.category', 'c', 'WITH', 'c.id IN(:stagecats)')
                             ->setParameter('stagecats', $filter->getStageCategories()->map(function($ob) {return $ob->getId();})->toArray());
            }
            else {
                $queryBuilder->join('p.category', 'c');
            }
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
        if(count($filter->getPromotions()) != 0) {
            $queryBuilder->join('p.requirements', 'r', 'WITH', 'r.type = \'promotion\' AND r.params IN(:proms)')
                         ->setParameter('proms', $filter->getPromotions());
        }
        if(!is_null($filter->getGroup())) {
            $queryBuilder->join('p.requirements', 'r', 'WITH', 'r.type = \'group\' AND r.params = :group')
                         ->setParameter('group', $filter->getGroup());
        }
        if(!is_null($filter->getLocked())) {
            $queryBuilder->andWhere('s.locked = :locked')
                         ->setParameter('locked', $filter->getLocked());
        }        
        if(!$filter->getUsers()->isEmpty()) {
            $queryBuilder->join('s.user', 'u', 'WITH', 'u.id IN(:users)')
                         ->setParameter('users', $filter->getUsers()->map(function($ob) {return $ob->getId();})->toArray());
        }
        else {
            $queryBuilder->join('s.user', 'u');
        }
        
        $queryBuilder->addOrderBy('u.lastname')
                     ->addOrderBy('u.firstname');
        if(!is_null($max)) {
            $queryBuilder->setMaxResults($max);
            if(!is_null($offset)) {
                $queryBuilder->setFirstResult($offset);
            }
        }
        
        return $queryBuilder;
    }

    public function filteredFind(StageFilter $filter, $max = null, $offset = null)
    {
        return $this->filteredFindQB($filter, $max, $offset)->getQuery()->getResult();
    }

    public function filteredFindCount(StageFilter $filter)
    {
        return $this->filteredFindQB($filter)
                    ->select('COUNT(s)')
                    ->getQuery()
                    ->getSingleScalarResult();
    }

    public function periodsResume($periods, $order)
    {
        $queryBuilder = $this->createQueryBuilder('s')
                             ->join('s.proposal', 'p', 'WITH', 'p.period IN(:periods)')            
                             ->setParameter('periods', $periods)
                             ->join('p.period', 'period')
                             ->join('s.user', 'u')
                             ->join('p.category', 'c')
                             ->where('s.locked = 1');
        if($order === 'byuser') {
            $queryBuilder->addOrderBy('u.promotion')
                         ->addOrderBy('u.lastname')
                         ->addOrderBy('u.firstname')
                         ->addOrderBy('period.start')
                         ->addOrderBy('period.stop');
        }
        elseif($order === 'bycategory') {
            $queryBuilder->addOrderBy('u.promotion')
                         ->addOrderBy('c.name')
                         ->addOrderBy('u.lastname')
                         ->addOrderBy('u.firstname');
        }

        return $queryBuilder->getQuery()->getResult();
    }

}
