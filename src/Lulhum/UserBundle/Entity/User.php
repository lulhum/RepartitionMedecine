<?php
// src/Lulhum/UserBundle/Entity/User.php

namespace Lulhum\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="Lulhum\UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{

    // /!\ Must also change the values list in the annotation field of the promotion attribute. Not very clean but no easy workaround...
    const PROMOTIONS = array('PACES' => 'PACES', 'L2' => 'L2', 'rL2' => 'rattrapages de L2', 'L3' => 'L3', 'rL3' => 'rattrapages de L3', 'DFASM1' => 'DFASM 1', 'rDFASM1' => 'rattrapages de DFASM 1', 'DFASM2' => 'DFASM 2', 'rDCEM3' => 'rattrapages de DCEM 3', 'DCEM4' => 'DCEM 4');
    
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="firstname", type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(name="lastname", type="string")
     */
    private $lastname;

    /**
     * @ORM\Column(name="student_id", type="string", length=10, unique=true)
     */
    private $studentId;

    /**
     * @ORM\Column(name="phone", type="string", length=10)
     */
    private $phone;

    /**
     * @ORM\Column(name="promotion", type="string", nullable=true, columnDefinition="enum('PACES', 'L2', 'rL2', 'L3', 'rL3', 'DFASM1', 'rDFASM1', 'DFASM2', 'rDCEM3', 'DCEM4')")
     */
    private $promotion;

    /**
     * @ORM\Column(name="present", type="string", columnDefinition="enum('Oui', 'Non', 'Variable')")
     */
    private $present;

    /**
     * @ORM\Column(name="internet_access", type="string", columnDefinition="enum('Oui', 'Non', 'Variable')")
     */
    private $internetAccess;

    /**
     * @ORM\ManyToOne(targetEntity="Lulhum\UserBundle\Entity\User")
     */
    private $proxy;

    /**
     * @ORM\Column(name="repartition_group", type="string", nullable=true, columnDefinition="enum('A', 'B')")
     */
    private $repartitionGroup;

    /**
     * @ORM\Column(name="repartition_group_requested_at", type="datetime", nullable=true)
     */
    private $repartitionGroupRequestedAt;

    /**
     * @ORM\Column(name="repartition_group_force", type="boolean")
     */
    private $repartitionGroupForce = False;

    public function setRepartitionGroup($repartitionGroup)
    {
        $this->repartitionGroup = $repartitionGroup;
        $this->repartitionGroupRequestedAt = new \DateTime();
        
        return $this;
    }

    public function getRepartitionGroup()
    {
        return $this->repartitionGroup;
    }

    public function getRepartitionGroupRequestedAt()
    {
        return $this->repartitionGroupRequestedAt;
    }

    private function setRepartitionGroupRequestedAt($repartitionGroupRequestedAt) {
        $this->repartitionGroupRequestedAt = null;

        return $this;
    }

    public function setRepartitionGroupForce($repartitionGroupForce)
    {
        $this->repartitionGroupForce = $repartitionGroupForce;

        return $this;
    }

    public function getRepartitionGroupForce()
    {
        return $this->repartitionGroupForce;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getFullname()
    {
        return $this->lastname.' '.$this->firstname;
    }

    public function setStudentId($studentId)
    {
        $this->studentId = $studentId;

        return $this;
    }

    public function getStudentId()
    {
        return $this->studentId;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;

        return $this;
    }

    public function getPromotion()
    {
        return $this->promotion;
    }

    public function getTextPromotion()
    {
        if(array_key_exists($this->getPromotion(), self::PROMOTIONS)) {
            
            return self::PROMOTIONS[$this->getPromotion()];
        }
        elseif(is_null($this->getPromotion())) {

            return 'Non défini';
        }
        else {

            return $this->getPromotion();
        }
    }

    public function setPresent($present)
    {
        $this->present = $present;

        return $this;
    }

    public function getPresent()
    {
        return $this->present;
    }

    public function setInternetAccess($internetAccess)
    {
        $this->internetAccess = $internetAccess;

        return $this;
    }

    public function getInternetAccess()
    {
        return $this->internetAccess;
    }

    public function setProxy(User $proxy = null)
    {
        $this->proxy = $proxy;

        return $this;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    public function getPromotionChoicesValues()
    {
        return self::PROMOTIONS;
    }

    public function __toString()
    {
        return $this->getFullname();
    }

    public function resetRepartitionGroup() {
        $this->setRepartitionGroup(null);
        $this->setRepartitionGroupForce(null);
        $this->setRepartitionGroupRequestedAt(null);
    }

    public function switchRepartitionGroup() {        
        if($this->repartitionGroup == 'A') {
            $this->setRepartitionGroup('B');
        }
        else {
            $this->setRepartitionGroup('A');
        }
    }

}