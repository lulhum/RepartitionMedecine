<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Requirement
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Requirement
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
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var array
     *
     * @ORM\Column(name="params", type="array")
     */
    private $params;

    /**
     * @var boolean
     *
     * @ORM\Column(name="strict", type="boolean")
     */
    private $strict;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal")
     * @ORM\JoinColumn(nullable=false)
     */     
    private $proposal;


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
     * Set type
     *
     * @param string $type
     * @return Requirement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set params
     *
     * @param array $params
     * @return Requirement
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return array 
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set strict
     *
     * @param boolean $strict
     * @return Requirement
     */
    public function setStrict($strict)
    {
        $this->strict = $strict;

        return $this;
    }

    /**
     * Get strict
     *
     * @return boolean 
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Set proposal
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageProposal $proposal
     * @return Requirement
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
}
