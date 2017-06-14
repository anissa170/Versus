<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sondage")
 */
class Sondage
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Proposition", mappedBy="sondage")
     */
    private $reponses;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Carte", inversedBy="sondages")
     * @ORM\JoinColumn(name="carte_id", referencedColumnName="id")
     */
    private $carte;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publier;

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
     * Set titre
     *
     * @param string $titre
     *
     * @return Sondage
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Sondage
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set publier
     *
     * @param boolean $publier
     *
     * @return Sondage
     */
    public function setPublier($publier)
    {
        $this->publier = $publier;

        return $this;
    }

    /**
     * Get publier
     *
     * @return boolean
     */
    public function getPublier()
    {
        return $this->publier;
    }

    /**
     * Add reponse
     *
     * @param \AppBundle\Entity\Proposition $reponse
     *
     * @return Sondage
     */
    public function addReponse(\AppBundle\Entity\Proposition $reponse)
    {
        $this->reponses[] = $reponse;

        return $this;
    }

    /**
     * Remove reponse
     *
     * @param \AppBundle\Entity\Proposition $reponse
     */
    public function removeReponse(\AppBundle\Entity\Proposition $reponse)
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

    /**
     * Set carte
     *
     * @param \AppBundle\Entity\Carte $carte
     *
     * @return Sondage
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
}
