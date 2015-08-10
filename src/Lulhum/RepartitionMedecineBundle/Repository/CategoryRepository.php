<?php
// src/Lulhum/RepartitionMedecineBundle/Repository/CategoryRepository.php

namespace Lulhum\RepartitionMedecineBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Lulhum\RepartitionMedecineBundle\Entity\StageProposal;

class CategoryRepository extends EntityRepository
{

    public function findByProposalIdQB($proposalId)
    {
        $queryBuilder = $this->createQueryBuilder('c');
        if($proposalId !== '') {
            $queryBuilder->join('c.stageCategories', 's')
                         ->join('s.proposals', 'p', 'WITH', 'p.id = :id')
                         ->setParameter('id', $proposalId);
        }
        
        return $queryBuilder;
    }
}