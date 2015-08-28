<?php

namespace Lulhum\RepartitionMedecineBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Parameter
{

    const PARAMETERS = array(
        'groupRepartitionMode' => array(
            'description' => 'Mode de répartition des groupes',
            'default' => 'manual',
            'values' => array(
                'manual' => 'Répartition manuelle',
                'last' => 'Automatique (priorité aux premiers inscrits)',
                'random' => 'Automatique (aléatoire)'
            )
        ),
        'pagination' => array(
            'description' => 'Nombre de résultats par page',
            'default' => '20',
            'values' => array(
                '10' => '10',
                '20' => '20',
                '30' => '30',
                '40' => '40',
                '50' => '50',
                '60' => '60',
                '70' => '70',
                '80' => '80',
                '90' => '90',
                '100' => '100',
            )
        ),
        'allowUserRegistrations' => array(
            'description' => 'Autoriser les utilisateurs à s\'inscrire',
            'default' => 'true',
            'values' => array(
                'true' => 'Oui',
                'false' => 'Non',
            )
        ),
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
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;

    public function __construct($name, $value=null) {
        $this->setName($name);
        $this->setValue($value);
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
     * @return Parameter
     */
    private function setName($name)
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
     * Set value
     *
     * @param string $value
     * @return Parameter
     */
    public function setValue($value=null)
    {
        if(is_null($value)) {
            $value = self::PARAMETERS[$this->getName()]['default'];
        }
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
