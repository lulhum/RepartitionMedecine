<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Lulhum\UserBundle\Entity\User;

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
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Period", inversedBy="proposals", cascade={"persist", "merge"})
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

    private function setId($id)
    {
        $this->id = $id;
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
        if(is_null($this->name) && !is_null($this->getCategory()) && !is_null($this->getPeriod())) {

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
        if(is_null($this->description) && !is_null($this->category) && !is_null($this->category->getDescription())) {

            return $this->category->getDescription();
        }
        
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

    public function __toString()
    {
        try {
            
            return $this->getName();
        }
        catch(\Exception $e) {
            echo $e->getMessage();
            
            return '#';
        }
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

    public function getSortedStages()
    {
        $iterator = $this->stages->getIterator();
        $iterator->uasort(function($a, $b) {
            if($a->getUser()->getFullname() === $b->getUser()->getFullname()) return 0;
            return $a->getUser()->getFullname() < $b->getUser()->getFullname() ? -1 : 1;
        });
        
        return new ArrayCollection(iterator_to_array($iterator));
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

    public function getPromotion()
    {
        if(!$this->hasRequirementType('promotion')) {
            return null;
        }

        return $this->getRequirementsByType('promotion')->first()->getParams();
    }

    public function countPlaces()
    {
        if($this->hasRequirementType('maxPlaces')) {
            
            return (int)($this->getRequirementsByType('maxPlaces')->first()->getParams()) - $this->getStages()->count();
        }
        
        return PHP_INT_MAX;
    }

    public function hasUser(User $user)
    {
        return $this->stages->exists(function($key, $stage) use (&$user) {
            return $stage->getUser()->getId() === $user->getId();
        });
    }

    public function getStagesByUser(User $user)
    {
        return $this->stages->filter(function($stage) use (&$user) {
            return $stage->getUser()->getId() === $user->getId();
        });
    }

    public function isValidForUser(User $user, \Lulhum\RepartitionMedecineBundle\Util\StageValidator $validator, $notValidOnWarning = false)
    {
        $stage = new Stage($user, $this);
        $valid = $validator->isValid($stage, $notValidOnWarning);
        $this->removeStage($stage);
        $user->removeStage($stage);

        return $valid;
    }

    public function getValidity(User $user, \Lulhum\RepartitionMedecineBundle\Util\StageValidator $validator)
    {
        $stage = new Stage($user, $this);
        $validity = $validator->getValidity($stage);
        $this->removeStage($stage);
        $user->removeStage($stage);

        return $validity;
    }

    public function __clone()
    {
        if($this->id) {
            $this->setId(null);
            $requirements = $this->getRequirements();
            $this->requirements = new ArrayCollection();
            foreach($requirements as $requirement) {
                $cloneRequirement = clone $requirement;
                $this->requirements->add($cloneRequirement);
                $cloneRequirement->setProposal($this);
            }
        }
    }
            
}
