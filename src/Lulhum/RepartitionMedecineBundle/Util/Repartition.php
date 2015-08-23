<?php
// src/Lulhum/RepartitionMedecine/Util/Repartition.php

namespace Lulhum\RepartitionMedecineBundle\Util;

use Doctrine\ORM\EntityManager;

class Repartition
{

    private $em;

    private $validator;

    public function __construct(EntityManager $em, StageValidator $validator) {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function finalizeGroupRepartition($promotion, $mode=null) {
        if(is_null($mode)) {
            $mode = $this->em->getRepository('LulhumRepartitionMedecineBundle:Parameter')->findOneByName('groupRepartitionMode')->getValue();
        }

        $userRepository = $this->em->getRepository('LulhumUserBundle:User');
        $userListGroups = $userRepository->getRepartitionGroupsByPromotion($promotion, array('repartitionGroupRequestedAt' => 'desc'));

        $openPlaces = array();
        foreach($userListGroups as $key => $userListGroup) {
            if($key != 'limit' && $key !='none') {
                if(count($userListGroup) > $userListGroups['limit']) {
                    if($mode != 'manual') {
                        $toPop = count($userListGroup) - $userListGroups['limit'];
                        if($mode == 'random') {
                            shuffle($userListGroup);
                        }
                        foreach($userListGroup as $user) {
                            if(!$user->getRepartitionGroupForce()) {
                                $userListGroups['none'][]=$user;
                                $toPop--;
                                if($toPop == 0) {
                                    break;
                                }
                            }
                        }
                    }
                }
                else {
                    $openPlaces = array_merge($openPlaces, array_fill(0, $userListGroups['limit'] - count($userListGroup), $key));
                }
            }
        }

        shuffle($userListGroups['none']);
        foreach($userListGroups['none'] as $user) {
            if(count($openPlaces) == 0) {
                break;
            }
            else {
                $user->setRepartitionGroup(array_shift($openPlaces));
                $this->em->persist($user);
            }
        }

    }

    public function resetGroupRepartition($promotion)
    {
        $userRepository = $this->em->getRepository('LulhumUserBundle:User');
        $users = $userRepository->findByPromotion($promotion);
        foreach($users as $user) {
            $user->resetRepartitionGroup();
            $this->em->persist($user);
        }
    }

    public function closeRepartition($deadline, $method = 'manual')
    {
        $proposals = $this->em->getRepository('LulhumRepartitionMedecineBundle:StageProposal')->findByDeadline($deadline);
        foreach($proposals as $proposal) {
            if($method === 'autovalid' && !$proposal->getLocked()) {
                foreach($proposal->getStages() as $stage) {
                    if($this->validator->isValid($stage)) {
                        $stage->setLocked(true);
                        $this->em->persist($stage);
                    }
                }
            }
            $proposal->setLocked(true);
            $proposal->setDeadline(null);
            $this->em->persist($proposal);
        }
    }
              
}