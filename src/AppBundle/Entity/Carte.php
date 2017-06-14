<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="carte")
 */
class Carte
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sondage", mappedBy="carte")
     */
    private $sondages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Localisation", mappedBy="carte")
     */
    private $localisations;

    public function __construct() {
        $this->sondages = new ArrayCollection();
        $this->localisations = new ArrayCollection();
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
     * Set image
     *
     * @param string $image
     *
     * @return Carte
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
     * Add sondage
     *
     * @param \AppBundle\Entity\Sondage $sondage
     *
     * @return Carte
     */
    public function addSondage(\AppBundle\Entity\Sondage $sondage)
    {
        $this->sondages[] = $sondage;

        return $this;
    }

    /**
     * Remove sondage
     *
     * @param \AppBundle\Entity\Sondage $sondage
     */
    public function removeSondage(\AppBundle\Entity\Sondage $sondage)
    {
        $this->sondages->removeElement($sondage);
    }

    /**
     * Get sondages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSondages()
    {
        return $this->sondages;
    }

    /**
     * Add localisation
     *
     * @param \AppBundle\Entity\Localisation $localisation
     *
     * @return Carte
     */
    public function addLocalisation(\AppBundle\Entity\Localisation $localisation)
    {
        $this->localisations[] = $localisation;

        return $this;
    }

    /**
     * Remove localisation
     *
     * @param \AppBundle\Entity\Localisation $localisation
     */
    public function removeLocalisation(\AppBundle\Entity\Localisation $localisation)
    {
        $this->localisations->removeElement($localisation);
    }

    /**
     * Get localisations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocalisations()
    {
        return $this->localisations;
    }
}
