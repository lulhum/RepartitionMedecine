<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Lulhum\RepartitionMedecineBundle\Repository\StageCategoryRepository;

/**
 * StageCategory
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Lulhum\RepartitionMedecineBundle\Repository\StageCategoryRepository")
 */
class StageCategory
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Location", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */   
    private $location;

    /**
     * @ORM\ManyToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\Category", inversedBy="stageCategories", cascade={"persist"})
     */ 
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageProposal", mappedBy="category")
     */
    private $proposals;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->proposals = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return StageCategory
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
     * Set description
     *
     * @param string $description
     * @return StageCategory
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
     * Set location
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\Location $location
     * @return StageCategory
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \Lulhum\RepartitionMedecineBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set categories
     *
     * @param ArrayCollection $categories
     * @return StageCategory
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add category
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\Category $category
     * @return StageCategory
     */
    public function addCategory(Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\Category $category
     * @return StageCategory
     */
    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function newCategories() {
        return new ArrayCollection();
    }

    public function addNewCategory(Category $category) {
        return $this->addCategory($category);
    }

    public function removeNewCategory(Category $category) {
        return $this->removeCategory($category);        
    }

    public function newLocation()
    {
        return new Location();
    }

    public function setNewLocation($location)
    {
        if(is_null($location->getName())) {

            return $this;
        }
        
        return $this->setLocation($location);
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set proposals
     *
     * @param ArrayCollection $proposals
     * @return StageCategory
     */
    public function setProposals($proposals)
    {
        foreach($proposal as $proposals) {
            $proposal->setCategory($this);
        }
        $this->proposals = $proposals;

        return $this;
    }

    /**
     * Get proposals
     *
     * @return ArrayCollection
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * Add proposal
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageProposal $proposal
     * @return StageCategory
     */
    public function addProposal(StageProposal $proposal)
    {
        $proposal->setCategory($this);
        $this->proposals[] = $proposal;

        return $this;
    }

    /**
     * Remove proposal
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageProposal $proposal
     * @return StageCategory
     */
    public function removeProposal(StageProposal $proposal)
    {
        $this->proposals->removeElement($proposal);

        return $this;
    }

}
