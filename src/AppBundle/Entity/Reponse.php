<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="reponse")
 */
class Reponse
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Proposition", inversedBy="reponses")
     * @ORM\JoinColumn(name="proposition_id", referencedColumnName="id")
     */
    private $proposition;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Localisation", inversedBy="reponses")
     * @ORM\JoinColumn(name="localisation_id", referencedColumnName="id")
     */
    private $localisation;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

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
     * Set datetime
     *
     * @param \DateTime $datetime
     *
     * @return Reponse
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set proposition
     *
     * @param \AppBundle\Entity\Proposition $proposition
     *
     * @return Reponse
     */
    public function setProposition(\AppBundle\Entity\Proposition $proposition = null)
    {
        $this->proposition = $proposition;

        return $this;
    }

    /**
     * Get proposition
     *
     * @return \AppBundle\Entity\Proposition
     */
    public function getProposition()
    {
        return $this->proposition;
    }

    /**
     * Set localisation
     *
     * @param \AppBundle\Entity\Localisation $localisation
     *
     * @return Reponse
     */
    public function setLocalisation(\AppBundle\Entity\Localisation $localisation = null)
    {
        $this->localisation = $localisation;

        return $this;
    }

    /**
     * Get localisation
     *
     * @return \AppBundle\Entity\Localisation
     */
    public function getLocalisation()
    {
        return $this->localisation;
    }
}
