<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sondage", mappedBy="product")
     */
    private $sondages;

    public function __construct()
    {
        parent::__construct();
        $this->sondages = new ArrayCollection();
    }

    /**
     * Add sondage
     *
     * @param \AppBundle\Entity\Sondage $sondage
     *
     * @return User
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
}
