<?php

// src/Lulhum/UserBundle/Entity/UserRepository.php

namespace Lulhum\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function getRepartitionGroupsByPromotion($promotion, $orderBy = array('lastname' => 'asc', 'firstname' => 'asc'))
    {
        $userList = $this->findBy(
            array('promotion' => $promotion),
            $orderBy
        );
        $userListGroups['A'] = array();
        $userListGroups['B'] = array();
        $userListGroups['none'] = array();
        foreach($userList as $user) {
            if($user->getRepartitionGroup() === 'A') {
                $userListGroups['A'][] = $user;
            }
            elseif($user->getRepartitionGroup() === 'B') {
                $userListGroups['B'][] = $user;
            }
            else {
                $userListGroups['none'][] = $user;
            }
        }
        $userListGroups['limit'] = ceil(count($userList)/2);

        return $userListGroups;
    }

    public function filteredFindQB($filter, $max = null, $offset = null)
    {
        $queryBuilder = $this->createQueryBuilder('u');
        if(count($filter->getPromotions()) > 0) {
            $queryBuilder->andWhere('u.promotion IN(:promotions)')
                         ->setParameter('promotions', $filter->getPromotions());
        }
        if(!is_null($filter->getGroup())) {
            $queryBuilder->andWhere('u.repartitionGroup = :group')
                        ->setParameter('group', $filter->getGroup());
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
    
}
