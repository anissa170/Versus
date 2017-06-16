<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="point")
 */
class Point
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Localisation", inversedBy="points")
     * @ORM\JoinColumn(name="localisation_id", referencedColumnName="id")
     */
    private $localisation;

    /**
     * @ORM\Column(type="float", length=10)
     */
    private $posX;

    /**
     * @ORM\Column(type="float", length=10)
     */
    private $posY;

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
     * Set posX
     *
     * @param float $posX
     *
     * @return Point
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
     * @return Point
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
     * Set localisation
     *
     * @param \AppBundle\Entity\Localisation $localisation
     *
     * @return Point
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
