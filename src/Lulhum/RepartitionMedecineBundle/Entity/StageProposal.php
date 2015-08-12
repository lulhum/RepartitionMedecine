<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * StageProposal
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\RepartitionMedecineBundle\Repository\StageProposalRepository")
 */
class StageProposal
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
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Period", cascade={"persist", "merge"})
     * @ORM\JoinColumn(nullable=false)
     */   
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageCategory", inversedBy="proposals", cascade={"merge"})
     * @ORM\JoinColumn(nullable=false)
     */  
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description=null;

    /**
     * @var boolean
     *
     * @ORM\Column(name="locked", type="boolean")
     */
    private $locked=true;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\DeadlineBundle\Entity\Deadline")
     * @ORM\JoinColumn(nullable=true)
     */  
    private $deadline;

    /**
     * @ORM\OneToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Requirement", mappedBy="proposal", cascade={"persist", "remove"})
     */  
    private $requirements;

    /**
     * @ORM\OneToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Stage", mappedBy="proposal", cascade={"remove"})
     */  
    private $stages;

    public function __construct()
    {
        $this->requirements = new ArrayCollection();
        $this->stages = new ArrayCollection();
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
     * Set period
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\Period $period
     * @return StageProposal
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return \Lulhum\RepartitionMedecineBundle\Entity\Period
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return StageProposal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        if(is_null($this->name) && !is_null($this->getCategory())) {

            return $this->getCategory().' - '.$this->getPeriod();
        }
        
        return $this->name;
    }

    /**
     * Set category
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageCategory $category
     * @return StageProposal
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Lulhum\RepartitionMedecineBundle\Entity\StageCategory
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return StageProposal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     * @return StageProposal
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
     * Set deadline
     *
     * @param \Lulhum\DeadlineBundle\Entity\Deadline $deadline
     * @return StageProposal
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \Lulhum\DeadlineBundle\Entity\Deadline
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    public function setRequirements(ArrayCollection $requirements)
    {
        foreach($requirements as $requirement) {
            $requirement->setProposal($this);
        }

        $this->requirements = $requirements;

        return $this;
    }

    public function getRequirements()
    {
        return $this->requirements;
    }

    public function addRequirement(Requirement $requirement)
    {
        $requirement->setProposal($this);
        $this->requirements[] = $requirement;

        return $this;
    }

    public function removeRequirement(Requirement $requirement)
    {
        $this->requirements->removeElement($requirement);

        return $this;
    }

    public function newPeriod()
    {
        return new Period();
    }

    public function setNewPeriod(Period $period)
    {
        if(is_null($period->getName())) {

            return $this;
        }
        
        return $this->setPeriod($period);
    }

    public function __toString() {
        return $this->getName();
    }     

    public function setStages(ArrayCollection $stages)
    {
        foreach($stages as $stage) {
            $stage->setProposal($this);
        }

        $this->stages = $stages;

        return $this;
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function addStage(Stage $stage)
    {
        $stage->setProposal($this);
        $this->stages[] = $stage;

        return $this;
    }

    public function removeStage(Stage $stage)
    {
        $this->stages->removeElement($stage);

        return $this;
    }

    public function hasRequirementType($type)
    {
        return $this->requirements->exists(function($key, $requirement) use (&$type) {
            return $requirement->getType() === $type;
        });
    }

    public function getRequirementsByType($type)
    {
        return $this->requirements->filter(function($requirement) use (&$type) {
            return $requirement->getType() === $type;
        });
    }
        
}
