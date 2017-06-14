<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="localisation")
 */
class Localisation
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carte", inversedBy="localisations")
     * @ORM\JoinColumn(name="carte_id", referencedColumnName="id")
     */
    private $carte;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Proposition", inversedBy="localisations")
     * @ORM\JoinTable(name="propositions_localisations")
     */
    private $propositions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="float", length=10)
     */
    private $posX;

    /**
     * @ORM\Column(type="float", length=10)
     */
    private $posY;

    public function __construct() {
        $this->propositions = new ArrayCollection();
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
     * Set label
     *
     * @param string $label
     *
     * @return Localisation
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set posX
     *
     * @param float $posX
     *
     * @return Localisation
     */
    public function setPosX($posX)
    {
        $this->posX = $posX;

        return $this;
    }

    /**
     * Get posX
     *
     * @return float
     */
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     * Set posY
     *
     * @param float $posY
     *
     * @return Localisation
     */
    public function setPosY($posY)
    {
        $this->posY = $posY;

        return $this;
    }

    /**
     * Get posY
     *
     * @return float
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     * Set carte
     *
     * @param \AppBundle\Entity\Carte $carte
     *
     * @return Localisation
     */
    public function setCarte(\AppBundle\Entity\Carte $carte = null)
    {
        $this->carte = $carte;

        return $this;
    }

    /**
     * Get carte
     *
     * @return \AppBundle\Entity\Carte
     */
    public function getCarte()
    {
        return $this->carte;
    }

    /**
     * Add prosition
     *
     * @param \AppBundle\Entity\Proposition $prosition
     *
     * @return Localisation
     */
    public function addProsition(\AppBundle\Entity\Proposition $prosition)
    {
        $this->propositions[] = $prosition;

        return $this;
    }

    /**
     * Remove prosition
     *
     * @param \AppBundle\Entity\Proposition $prosition
     */
    public function removeProsition(\AppBundle\Entity\Proposition $prosition)
    {
        $this->propositions->removeElement($prosition);
    }

    /**
     * Get prositions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPrositions()
    {
        return $this->propositions;
    }
}
