<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/StageProposalRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposalFilter;
use Lulhum\RepartitionMedecineBundle\Entity\Stage;
use Lulhum\RepartitionMedecineBundle\Util\StageValidator;
use Lulhum\UserBundle\Entity\User;

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
        if(!$filter->getCategoriesOr()->isEmpty() || !$filter->getCategoriesAnd()->isEmpty()) {
            $queryBuilder->join('c.categories', 'cat');
        }
        if(!$filter->getCategoriesOr()->isEmpty()) {
            foreach($filter->getCategoriesAnd() as $category) {
                $queryBuilder->orWhere('cat.id = :id')
                             ->setParameter('id', $category->getId());
            }
        }
        if(!$filter->getCategoriesAnd()->isEmpty()) {
            foreach($filter->getCategoriesAnd() as $category) {
                $queryBuilder->andWhere('cat.id = :id')
                             ->setParameter('id', $category->getId());
            }
        }
        
        return $queryBuilder;
    }

    public function filteredFind(StageProposalFilter $filter)
    {
        return $this->filteredFindQB($filter)->getQuery()->getResult();
    }

    public function findAllValidForUser(User $user, StageValidator $validator, $sort = array(), $search = null)
    {
        if(is_null($search)) {
            $proposals = $this->findByLocked(false);
        }
        else {
            $proposals = $this->search($search, array('locked' => false));
        }

        $proposals = array_filter($proposals, function($proposal) use (&$user, &$validator) {
            $stage = new Stage($user, $proposal);
            $response = $validator->isValid($stage);
            $proposal->removeStage($stage);
            $user->removeStage($stage);

            return $response;
        });

        if(count($sort) > 0) {
            uasort($proposals, function($a, $b) use (&$sort) {
                foreach($sort as $s) {
                    if($s === 'title' && $a->getName() != $b->getName()) {
                        return $a->getName() < $b->getName() ? -1 : 1;
                    }
                    if($s === 'period' && $a->getPeriod()->getStart() != $b->getPeriod()->getStart()) {
                        return $a->getPeriod()->getStart() < $b->getPeriod()->getStart() ? -1 : 1;
                    }
                    if($s === 'places' && $a->countPlaces() != $b->countPlaces()) {
                        return $a->countPlaces() < $b->countPlaces() ? -1 : 1;
                    }
                }
                return 0;            
            });
        }

        return $proposals;
    }

    public function search($search, $conditions)
    {
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->leftjoin('s.category', 'c')
                     ->leftjoin('s.stages', 'st')
                     ->leftjoin('s.period', 'p')
                     ->leftjoin('st.user', 'u')
                     ->leftjoin('c.categories', 'cat')
                     ->leftjoin('c.location', 'l')
                     ->orWhere('s.name LIKE :search')
                     ->orWhere('s.description LIKE :search')
                     ->orWhere('c.name LIKE :search')
                     ->orWhere('c.description LIKE :search')
                     ->orWhere('cat.name LIKE :search')
                     ->orWhere('cat.description LIKE :search')
                     ->orWhere('u.firstname LIKE :search')
                     ->orWhere('u.lastname LIKE :search')
                     ->orWhere('l.name LIKE :search')
                     ->orWhere('l.name LIKE :search')
                     ->orWhere('p.name LIKE :search')
                     ->orWhere('p.description LIKE :search')
                     ->setParameter('search', "%$search%");
        foreach($conditions as $key => $value) {
            if($key === 'locked') {
                $queryBuilder->andWhere('s.locked = :val');
            }
            else continue;
            $queryBuilder->setParameter('val', $value);
        }

        return $queryBuilder->getQuery()->getResult();
    }
}