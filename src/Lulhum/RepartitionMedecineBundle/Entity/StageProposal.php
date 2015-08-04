<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * StageProposal
 *
 * @ORM\Table()
 * @ORM\Entity
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
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Period")
     * @ORM\JoinColumn(nullable=false)
     */   
    private $period;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageCategory")
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
}
