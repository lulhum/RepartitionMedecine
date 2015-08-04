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

}