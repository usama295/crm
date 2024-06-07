<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Dnclist
 *
 * @ORM\Table(name="dnclist", uniqueConstraints={@ORM\UniqueConstraint(name="phonenumber", columns={"phonenumber"})}, indexes={@ORM\Index(name="areacode", columns={"areacode"})})
 * @ORM\Entity
 */
class Dnclist
{
    /**
     * @var int
     *
     * @ORM\Column(name="dnclistid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dnclistid;

    /**
     * @var string
     *
     * @ORM\Column(name="areacode", type="string", length=3, nullable=false, options={"fixed"=true})
     */
    private $areacode = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="phonenumber", type="string", length=10, nullable=false)
     */
    private $phonenumber = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=30, nullable=false)
     */
    private $firstname = '';

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=30, nullable=false)
     */
    private $lastname = '';

    /**
     * @var bool
     *
     * @ORM\Column(name="imported", type="boolean", nullable=false)
     */
    private $imported = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateadded", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $dateadded = '0000-00-00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TS", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $ts = 'CURRENT_TIMESTAMP';

    public function getDnclistid(): ?int
    {
        return $this->dnclistid;
    }

    public function getAreacode(): ?string
    {
        return $this->areacode;
    }

    public function setAreacode(string $areacode): self
    {
        $this->areacode = $areacode;

        return $this;
    }

    public function getPhonenumber(): ?string
    {
        return $this->phonenumber;
    }

    public function setPhonenumber(string $phonenumber): self
    {
        $this->phonenumber = $phonenumber;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImported(): ?bool
    {
        return $this->imported;
    }

    public function setImported(bool $imported): self
    {
        $this->imported = $imported;

        return $this;
    }

    public function getDateadded(): ?\DateTimeInterface
    {
        return $this->dateadded;
    }

    public function setDateadded(\DateTimeInterface $dateadded): self
    {
        $this->dateadded = $dateadded;

        return $this;
    }

    public function getTs(): ?\DateTimeInterface
    {
        return $this->ts;
    }

    public function setTs(\DateTimeInterface $ts): self
    {
        $this->ts = $ts;

        return $this;
    }


}
