<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="proposition")
 */
class Proposition
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Localisation", mappedBy="prositions")
     */
    private $localisations;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sondage", inversedBy="reponses")
     * @ORM\JoinColumn(name="sondage_id", referencedColumnName="id")
     */
    private $sondage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $couleur;

    public function __construct() {
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
     * Set label
     *
     * @param string $label
     *
     * @return Proposition
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
     * Set couleur
     *
     * @param string $couleur
     *
     * @return Proposition
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Add localisation
     *
     * @param \AppBundle\Entity\Localisation $localisation
     *
     * @return Proposition
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

    /**
     * Set sondage
     *
     * @param \AppBundle\Entity\Sondage $sondage
     *
     * @return Proposition
     */
    public function setSondage(\AppBundle\Entity\Sondage $sondage = null)
    {
        $this->sondage = $sondage;

        return $this;
    }

    /**
     * Get sondage
     *
     * @return \AppBundle\Entity\Sondage
     */
    public function getSondage()
    {
        return $this->sondage;
    }
}
