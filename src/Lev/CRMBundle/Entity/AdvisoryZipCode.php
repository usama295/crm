<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * AdvisoryZipCode Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="advisory_zip_code")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class AdvisoryZipCode
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
     * @ORM\Column(name="zip_code", type="string",length=9)
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=9)
     */
    protected $zipCode;

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
     * Set zipCode
     *
     * @param string $zipCode
     * @return AdvisoryZipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return string
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }


    public function toArray()
    {
        return array(
            'id' => $this->getId(),
            'zipCode' => $this->getZipCode()
        );
    }
}
