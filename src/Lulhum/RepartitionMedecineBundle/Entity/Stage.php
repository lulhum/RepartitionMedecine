<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lulhum\UserBundle\Entity\User;

/**
 * Stage
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="Lulhum\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */     
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal", inversedBy="stages")
     * @ORM\JoinColumn(nullable=false)
     */     
    private $proposal;

    public function __construct(User $user, StageProposal $proposal)
    {
        $this->user = $user;
        $this->proposal = $proposal;
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
    public function setProposal($proposal)
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

    public function isValid() {
        return $this->proposal->isValid($this);
    }

}
