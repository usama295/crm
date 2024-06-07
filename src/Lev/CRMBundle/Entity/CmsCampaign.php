<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CmsCampaign
 *
 * @ORM\Table(name="cms_campaign", uniqueConstraints={@ORM\UniqueConstraint(name="campaignid", columns={"campaignid"})}, indexes={@ORM\Index(name="campaignid_2", columns={"campaignid", "startdate", "enddate"}), @ORM\Index(name="leadsourceid", columns={"leadsourceid"})})
 * @ORM\Entity
 */
class CmsCampaign
{
    /**
     * @var int
     *
     * @ORM\Column(name="campaignid", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $campaignid;

    /**
     * @var int
     *
     * @ORM\Column(name="leadsourceid", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $leadsourceid = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="campaignname", type="string", length=20, nullable=true)
     */
    private $campaignname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="startdate", type="date", nullable=true, options={"default"="0000-00-00"})
     */
    private $startdate = '0000-00-00';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="enddate", type="date", nullable=true, options={"default"="0000-00-00"})
     */
    private $enddate = '0000-00-00';

    /**
     * @var int
     *
     * @ORM\Column(name="scriptid", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $scriptid = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="sortorder", type="string", length=30, nullable=true)
     */
    private $sortorder;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $ts = 'CURRENT_TIMESTAMP';

    public function getCampaignid(): ?int
    {
        return $this->campaignid;
    }

    public function getLeadsourceid(): ?int
    {
        return $this->leadsourceid;
    }

    public function setLeadsourceid(int $leadsourceid): self
    {
        $this->leadsourceid = $leadsourceid;

        return $this;
    }

    public function getCampaignname(): ?string
    {
        return $this->campaignname;
    }

    public function setCampaignname(?string $campaignname): self
    {
        $this->campaignname = $campaignname;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStartdate(): ?\DateTimeInterface
    {
        return $this->startdate;
    }

    public function setStartdate(?\DateTimeInterface $startdate): self
    {
        $this->startdate = $startdate;

        return $this;
    }

    public function getEnddate(): ?\DateTimeInterface
    {
        return $this->enddate;
    }

    public function setEnddate(?\DateTimeInterface $enddate): self
    {
        $this->enddate = $enddate;

        return $this;
    }

    public function getScriptid(): ?int
    {
        return $this->scriptid;
    }

    public function setScriptid(int $scriptid): self
    {
        $this->scriptid = $scriptid;

        return $this;
    }

    public function getSortorder(): ?string
    {
        return $this->sortorder;
    }

    public function setSortorder(?string $sortorder): self
    {
        $this->sortorder = $sortorder;

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
