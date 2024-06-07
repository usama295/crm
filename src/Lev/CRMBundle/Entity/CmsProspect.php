<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CmsProspect
 *
 * @ORM\Table(name="cms_prospect", uniqueConstraints={@ORM\UniqueConstraint(name="prospectid", columns={"prospectid"})}, indexes={@ORM\Index(name="city", columns={"city"}), @ORM\Index(name="dnc", columns={"dnc"}), @ORM\Index(name="homephone", columns={"homephone"}), @ORM\Index(name="prospectid_2", columns={"prospectid"}), @ORM\Index(name="ssn1", columns={"ssn1"}), @ORM\Index(name="workphone", columns={"workphone"}), @ORM\Index(name="workphone2", columns={"workphone2"}), @ORM\Index(name="zip", columns={"zip"})})
 * @ORM\Entity
 */
class CmsProspect
{
    /**
     * @var int
     *
     * @ORM\Column(name="prospectid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $prospectid;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="salutation", type="boolean", nullable=true)
     */
    private $salutation = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname1", type="string", length=20, nullable=true)
     */
    private $firstname1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="middleinitial1", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $middleinitial1;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lastname1", type="string", length=20, nullable=true)
     */
    private $lastname1;

    /**
     * @var string
     *
     * @ORM\Column(name="ssn1", type="string", length=9, nullable=false)
     */
    private $ssn1 = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="firstname2", type="string", length=20, nullable=true)
     */
    private $firstname2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="middleinitial2", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $middleinitial2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lastname2", type="string", length=20, nullable=true)
     */
    private $lastname2;

    /**
     * @var string
     *
     * @ORM\Column(name="ssn2", type="string", length=9, nullable=false)
     */
    private $ssn2 = '';

    /**
     * @var string|null
     *
     * @ORM\Column(name="address", type="string", length=30, nullable=true)
     */
    private $address;

    /**
     * @var string|null
     *
     * @ORM\Column(name="address2", type="string", length=30, nullable=true)
     */
    private $address2;

    /**
     * @var string|null
     *
     * @ORM\Column(name="city", type="string", length=30, nullable=true)
     */
    private $city;

    /**
     * @var string|null
     *
     * @ORM\Column(name="state", type="string", length=2, nullable=true, options={"fixed"=true})
     */
    private $state;

    /**
     * @var string|null
     *
     * @ORM\Column(name="zip", type="string", length=9, nullable=true)
     */
    private $zip;

    /**
     * @var string|null
     *
     * @ORM\Column(name="emailaddress", type="string", length=50, nullable=true)
     */
    private $emailaddress;

    /**
     * @var string|null
     *
     * @ORM\Column(name="homephone", type="string", length=10, nullable=true)
     */
    private $homephone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="workphone", type="string", length=10, nullable=true)
     */
    private $workphone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="workphone2", type="string", length=10, nullable=true)
     */
    private $workphone2;

    /**
     * @var bool
     *
     * @ORM\Column(name="age", type="boolean", nullable=false)
     */
    private $age = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="race", type="boolean", nullable=false)
     */
    private $race = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="gender", type="boolean", nullable=false)
     */
    private $gender = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="primaryresidence", type="boolean", nullable=false)
     */
    private $primaryresidence = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="homeage", type="float", precision=10, scale=0, nullable=false)
     */
    private $homeage = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="yearsowned", type="float", precision=10, scale=0, nullable=false)
     */
    private $yearsowned = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="nitimes", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $nitimes = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="nicalldate", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $nicalldate = '0000-00-00';

    /**
     * @var bool
     *
     * @ORM\Column(name="dnc", type="boolean", nullable=false)
     */
    private $dnc = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $ts = 'CURRENT_TIMESTAMP';

    public function getProspectid(): ?int
    {
        return $this->prospectid;
    }

    public function getSalutation(): ?bool
    {
        return $this->salutation;
    }

    public function setSalutation(?bool $salutation): self
    {
        $this->salutation = $salutation;

        return $this;
    }

    public function getFirstname1(): ?string
    {
        return $this->firstname1;
    }

    public function setFirstname1(?string $firstname1): self
    {
        $this->firstname1 = $firstname1;

        return $this;
    }

    public function getMiddleinitial1(): ?string
    {
        return $this->middleinitial1;
    }

    public function setMiddleinitial1(?string $middleinitial1): self
    {
        $this->middleinitial1 = $middleinitial1;

        return $this;
    }

    public function getLastname1(): ?string
    {
        return $this->lastname1;
    }

    public function setLastname1(?string $lastname1): self
    {
        $this->lastname1 = $lastname1;

        return $this;
    }

    public function getSsn1(): ?string
    {
        return $this->ssn1;
    }

    public function setSsn1(string $ssn1): self
    {
        $this->ssn1 = $ssn1;

        return $this;
    }

    public function getFirstname2(): ?string
    {
        return $this->firstname2;
    }

    public function setFirstname2(?string $firstname2): self
    {
        $this->firstname2 = $firstname2;

        return $this;
    }

    public function getMiddleinitial2(): ?string
    {
        return $this->middleinitial2;
    }

    public function setMiddleinitial2(?string $middleinitial2): self
    {
        $this->middleinitial2 = $middleinitial2;

        return $this;
    }

    public function getLastname2(): ?string
    {
        return $this->lastname2;
    }

    public function setLastname2(?string $lastname2): self
    {
        $this->lastname2 = $lastname2;

        return $this;
    }

    public function getSsn2(): ?string
    {
        return $this->ssn2;
    }

    public function setSsn2(string $ssn2): self
    {
        $this->ssn2 = $ssn2;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): self
    {
        $this->zip = $zip;

        return $this;
    }

    public function getEmailaddress(): ?string
    {
        return $this->emailaddress;
    }

    public function setEmailaddress(?string $emailaddress): self
    {
        $this->emailaddress = $emailaddress;

        return $this;
    }

    public function getHomephone(): ?string
    {
        return $this->homephone;
    }

    public function setHomephone(?string $homephone): self
    {
        $this->homephone = $homephone;

        return $this;
    }

    public function getWorkphone(): ?string
    {
        return $this->workphone;
    }

    public function setWorkphone(?string $workphone): self
    {
        $this->workphone = $workphone;

        return $this;
    }

    public function getWorkphone2(): ?string
    {
        return $this->workphone2;
    }

    public function setWorkphone2(?string $workphone2): self
    {
        $this->workphone2 = $workphone2;

        return $this;
    }

    public function getAge(): ?bool
    {
        return $this->age;
    }

    public function setAge(bool $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRace(): ?bool
    {
        return $this->race;
    }

    public function setRace(bool $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getGender(): ?bool
    {
        return $this->gender;
    }

    public function setGender(bool $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPrimaryresidence(): ?bool
    {
        return $this->primaryresidence;
    }

    public function setPrimaryresidence(bool $primaryresidence): self
    {
        $this->primaryresidence = $primaryresidence;

        return $this;
    }

    public function getHomeage(): ?float
    {
        return $this->homeage;
    }

    public function setHomeage(float $homeage): self
    {
        $this->homeage = $homeage;

        return $this;
    }

    public function getYearsowned(): ?float
    {
        return $this->yearsowned;
    }

    public function setYearsowned(float $yearsowned): self
    {
        $this->yearsowned = $yearsowned;

        return $this;
    }

    public function getNitimes(): ?int
    {
        return $this->nitimes;
    }

    public function setNitimes(int $nitimes): self
    {
        $this->nitimes = $nitimes;

        return $this;
    }

    public function getNicalldate(): ?\DateTimeInterface
    {
        return $this->nicalldate;
    }

    public function setNicalldate(\DateTimeInterface $nicalldate): self
    {
        $this->nicalldate = $nicalldate;

        return $this;
    }

    public function getDnc(): ?bool
    {
        return $this->dnc;
    }

    public function setDnc(bool $dnc): self
    {
        $this->dnc = $dnc;

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
