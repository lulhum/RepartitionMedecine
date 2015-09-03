<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/PeriodRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Types\Type;

class PeriodRepository extends EntityRepository
{

    public function findBetweenQB(\DateTime $start, \DateTime $stop)
    {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->where($queryBuilder->expr()->between('p.start', ':mystart', ':mystop'))
                     ->orWhere($queryBuilder->expr()->between('p.stop', ':mystart', ':mystop'))
                     ->addOrderBy('p.start')
                     ->addOrderBy('p.stop')
                     ->setParameter('mystart', $start, Type::DATETIME)
                     ->setParameter('mystop', $stop, Type::DATETIME);
        
        return $queryBuilder;
    }

    public function findBetween(\DateTime $start, \DateTime $stop)
    {
        return $this->findBetweenQB($start, $stop)->getQuery()->getResult();
    }

    public function findCurrents()
    {
        $currentTime = new \DateTime();
        if($currentTime->format('m')<=6) {            
            $currentTime->sub(new \DateInterval('P1Y'));
        }
        $start = new \DateTime();
        $start->setDate((int)$currentTime->Format('Y'), 10, 1);
        $start->setTime(0, 0, 0);
        $stop = new \DateTime();
        $stop->setDate((int)$currentTime->add(new \DateInterval('P1Y'))->Format('Y'), 9, 30);
        $stop->setTime(0, 0, 0);      
        
        return $this->findBetween($start, $stop);
    }

}
