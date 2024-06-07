<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CmsLeadsource
 *
 * @ORM\Table(name="cms_leadsource")
 * @ORM\Entity
 */
class CmsLeadsource
{
    /**
     * @var int
     *
     * @ORM\Column(name="leadsourceid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $leadsourceid;

    /**
     * @var int
     *
     * @ORM\Column(name="divisionid", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $divisionid = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="leadsourcename", type="string", length=30, nullable=false)
     */
    private $leadsourcename = '';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchasedate", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $purchasedate = '0000-00-00';

    /**
     * @var float
     *
     * @ORM\Column(name="purchaseprice", type="float", precision=10, scale=0, nullable=false)
     */
    private $purchaseprice = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="wherepurchased", type="string", length=30, nullable=true)
     */
    private $wherepurchased;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $startdate = '0000-00-00';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="date", nullable=false, options={"default"="0000-00-00"})
     */
    private $enddate = '0000-00-00';

    /**
     * @var bool
     *
     * @ORM\Column(name="trackertype", type="boolean", nullable=false)
     */
    private $trackertype = '0';

    /**
     * @var int
     *
     * @ORM\Column(name="trackerleadsourceid", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $trackerleadsourceid = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $ts = 'CURRENT_TIMESTAMP';

    public function getLeadsourceid(): ?int
    {
        return $this->leadsourceid;
    }

    public function getDivisionid(): ?int
    {
        return $this->divisionid;
    }

    public function setDivisionid(int $divisionid): self
    {
        $this->divisionid = $divisionid;

        return $this;
    }

    public function getLeadsourcename(): ?string
    {
        return $this->leadsourcename;
    }

    public function setLeadsourcename(string $leadsourcename): self
    {
        $this->leadsourcename = $leadsourcename;

        return $this;
    }

    public function getPurchasedate(): ?\DateTimeInterface
    {
        return $this->purchasedate;
    }

    public function setPurchasedate(\DateTimeInterface $purchasedate): self
    {
        $this->purchasedate = $purchasedate;

        return $this;
    }

    public function getPurchaseprice(): ?float
    {
        return $this->purchaseprice;
    }

    public function setPurchaseprice(float $purchaseprice): self
    {
        $this->purchaseprice = $purchaseprice;

        return $this;
    }

    public function getWherepurchased(): ?string
    {
        return $this->wherepurchased;
    }

    public function setWherepurchased(?string $wherepurchased): self
    {
        $this->wherepurchased = $wherepurchased;

        return $this;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(\DateTimeInterface $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(\DateTimeInterface $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getTrackertype(): ?bool
    {
        return $this->trackertype;
    }

    public function setTrackertype(bool $trackertype): self
    {
        $this->trackertype = $trackertype;

        return $this;
    }

    public function getTrackerleadsourceid(): ?int
    {
        return $this->trackerleadsourceid;
    }

    public function setTrackerleadsourceid(int $trackerleadsourceid): self
    {
        $this->trackerleadsourceid = $trackerleadsourceid;

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
