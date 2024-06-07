<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\Group as BaseGroup;

/**
 * StaffRole Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="staff_role")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class StaffRole extends BaseGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="shortname", type="string", length=10, nullable=true)
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=10)
     */
    protected $shortname;

    /**
     * @ORM\Column(name="superadmin", type="boolean")
     */
    protected $superadmin;

    public function toArray()
    {
        return array(
            'id'         => $this->getId(),
            'name'       => $this->getName(),
            'shortname'  => $this->getShortname(),
            'roles'      => $this->getRoles(),
            'superadmin' => $this->isSuperadmin(),
        );
    }

    public function toArrayShort()
    {
        return array(
            'id'         => $this->getId(),
            'name'       => $this->getName(),
            'shortname'  => $this->getShortname(),
            'superadmin' => $this->isSuperadmin(),
        );
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
     * Set superadmin
     *
     * @param boolean $superadmin
     * @return StaffRole
     */
    public function setSuperadmin($superadmin)
    {
        $this->superadmin = $superadmin;

        return $this;
    }

    /**
     * Get superadmin
     *
     * @return boolean
     */
    public function getSuperadmin()
    {
        return $this->superadmin;
    }

    /**
     * Check if  superadmin
     *
     * @return boolean
     */
    public function isSuperadmin()
    {
        return (boolean) $this->superadmin;
    }


    /**
     * Set shortname
     *
     * @param string $shortname
     *
     * @return StaffRole
     */
    public function setShortname($shortname)
    {
        $this->shortname = $shortname;

        return $this;
    }

    /**
     * Get shortname
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->shortname;
    }

    public function __toString() {
        return $this->name;
    }
}
