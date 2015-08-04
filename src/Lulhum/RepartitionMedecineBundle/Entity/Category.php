<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Category
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Lulhum\RepartitionMedecineBundle\Entity\StageCategory", mappedBy="categories")
     */ 
    private $stageCategories;


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
     * @return Category
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
     * @return Category
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
     * Set stageCategories
     *
     * @param ArrayCollection $stageCategories
     * @return Category
     */
    public function setStageCategories($stageCategories)
    {
        $this->stageCategories = $stageCategories;

        return $this;
    }

    /**
     * Get stageCategories
     *
     * @return ArrayCollection
     */
    public function getStageCategories()
    {
        return $this->stageCategories;
    }

    /**
     * Add stageCategory
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageCategory $stageCategory
     * @return StageCategory
     */
    public function addStageCategory(StageCategory $stageCategory)
    {
        $this->stageCategories[] = $stageCategory;

        return $this;
    }

    /**
     * Remove stageCategory
     *
     * @param \Lulhum\RepartitionMedecineBundle\Entity\StageCategory $stageCategory
     * @return StageCategory
     */
    public function removeStageCategory(StageCategory $stageCategory)
    {
        $this->stageCategories->removeElement($stageCategory);

        return $this;
    }
}
