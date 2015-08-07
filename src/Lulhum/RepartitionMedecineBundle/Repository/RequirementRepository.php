<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/RequirementRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposal;

class RequirementRepository extends EntityRepository
{

    public function findByProposal(StageProposal $proposal)
    {        
        $queryBuilder = $this->createQueryBuilder('r');
        $queryBuilder->join('r.proposal', 'p', 'WITH', 'p.id IN(:id)')
                     ->setParameter('id', $proposal->getId());
        
        return $queryBuilder->getQuery()->getResult();
    }
}