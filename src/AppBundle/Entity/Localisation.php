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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Reponse", mappedBy="localisation")
     */
    private $reponses;

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
        $this->reponses = new ArrayCollection();
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
     * Add reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     *
     * @return Localisation
     */
    public function addReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \AppBundle\Entity\Reponse $reponse
     */
    public function removeReponse(\AppBundle\Entity\Reponse $reponse)
    {
        $this->reponses->removeElement($reponse);
    }

    /**
     * Get reponses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReponses()
    {
        return $this->reponses;
    }
}
