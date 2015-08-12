<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lulhum\UserBundle\Entity\User;

/**
 * Stage
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\RepartitionMedecineBundle\Repository\StageRepository")
 */
class Stage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked=false;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\UserBundle\Entity\User", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false)
     */     
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false)
     */     
    private $proposal;

    private $checkRequirementsCache=null;

    public function __construct(User $user=null, StageProposal $proposal=null)
    {
        if(!is_null($user) && !is_null($proposal)) {
            $proposal->addStage($this);
            $user->addStage($this);
        }
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return Stage
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean 
     */
    public function getLocked()
    {
        return $this->locked;
    }

    /**
     * Set user
     *
     * @param \Lulhum\UserBundle\Entity\User $user
     * @return Stage
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Lulhum\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set proposal
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageProposal $proposal
     * @return Stage
     */
    public function setProposal(StageProposal $proposal)
    {
        $this->proposal = $proposal;

        return $this;
    }

    /**
     * Get proposal
     *
     * @return \Lulhum\RepartitionMedecineBundle\Entity\StageProposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    public function __toString()
    {
        return $this->user->getFullname().' - '.$this->proposal;
    }

}
