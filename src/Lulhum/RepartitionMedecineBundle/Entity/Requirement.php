<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Requirement
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\RepartitionMedecineBundle\Repository\RequirementRepository")
 */
class Requirement
{

    const TYPES = array(
        'maxPlaces' => 'Nombre de places maximum',
        'promotion' => 'Promotion',
        'maxChoicesInPeriod' => 'Maximum de choix pour cette période',
        'maxStagesInCategory' => 'Maximum de stages dans la catégorie',
        'maxStagesInStageCategory' => 'Maximum de stages avec ce modèle',
    );
    
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
     * @var string
     *
     * @ORM\Column(name="params", type="string", length=255)
     */
    private $params;

    /**
     * @var boolean
     *
     * @ORM\Column(name="strict", type="boolean")
     */
    private $strict;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal", inversedBy="requirements")
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

    public function __toString()        
    {
        return self::TYPES[$this->getType()].': '.$this->getParams();
    }

    public function isValid(Stage $stage)
    {
        if(!$this->strict) {
            
            return true;
        }

        if($this->type === 'maxPlaces') {
            
            return $this->proposal->getStages()->count() <= (int)$this->params;
        }

        if($this->type === 'promotion') {

            return $stage->getUser()->getPromotion() === $this->params;
        }
    }            
        
}
