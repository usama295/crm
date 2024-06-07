<?php
namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Leads
 *
 * @ORM\Table(name="leads", uniqueConstraints={@ORM\UniqueConstraint(name="id_UNIQUE", columns={"id"})})
 * @ORM\Entity
 */
class Leads
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="voided", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $voided;

    /**
     * @var int|null
     *
     * @ORM\Column(name="officeId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $officeid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="clientId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $clientid;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateEntered", type="datetime", nullable=true)
     */
    private $dateentered;

    /**
     * @var int|null
     *
     * @ORM\Column(name="productId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $productid;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateLead", type="datetime", nullable=true)
     */
    private $datelead;

    /**
     * @var string|null
     *
     * @ORM\Column(name="genNum", type="string", length=3, nullable=true, options={"fixed"=true})
     */
    private $gennum;

    /**
     * @var string|null
     *
     * @ORM\Column(name="generator", type="string", length=50, nullable=true)
     */
    private $generator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="slmNum", type="string", length=3, nullable=true, options={"fixed"=true})
     */
    private $slmnum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="salesRepId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $salesrepid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="result", type="string", length=50, nullable=true)
     */
    private $result;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reason", type="string", length=500, nullable=true)
     */
    private $reason;

    /**
     * @var float|null
     *
     * @ORM\Column(name="contract", type="float", precision=10, scale=0, nullable=true)
     */
    private $contract;

    /**
     * @var string|null
     *
     * @ORM\Column(name="status", type="string", length=5, nullable=true)
     */
    private $status;

    /**
     * @var int|null
     *
     * @ORM\Column(name="jobNumber", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $jobnumber;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reset", type="string", length=5, nullable=true)
     */
    private $reset;

    /**
     * @var int|null
     *
     * @ORM\Column(name="resetter", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $resetter;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ccNumber", type="string", length=5, nullable=true)
     */
    private $ccnumber;

    /**
     * @var int|null
     *
     * @ORM\Column(name="crewChief", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $crewchief;

    /**
     * @var int|null
     *
     * @ORM\Column(name="leadSourceId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $leadsourceid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numberOfWindows", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $numberofwindows;

    /**
     * @var int|null
     *
     * @ORM\Column(name="percentSiding", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $percentsiding;

    /**
     * @var int|null
     *
     * @ORM\Column(name="officeLeadId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $officeleadid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="numberOfDoors", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $numberofdoors;

    /**
     * @var int|null
     *
     * @ORM\Column(name="percentRoof", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $percentroof;

    /**
     * @var string|null
     *
     * @ORM\Column(name="division", type="string", length=50, nullable=true)
     */
    private $division;

    /**
     * @var string|null
     *
     * @ORM\Column(name="conStatus", type="string", length=5, nullable=true)
     */
    private $constatus;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateCon", type="datetime", nullable=true)
     */
    private $datecon;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateOrigination", type="datetime", nullable=true)
     */
    private $dateorigination;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateResetter", type="datetime", nullable=true)
     */
    private $dateresetter;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rehasher", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $rehasher;

    /**
     * @var int|null
     *
     * @ORM\Column(name="confirmer", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $confirmer;

    /**
     * @var string|null
     *
     * @ORM\Column(name="gennet", type="string", length=50, nullable=true)
     */
    private $gennet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="spokeWith", type="string", length=50, nullable=true)
     */
    private $spokewith;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dateEdited", type="datetime", nullable=true, options={"comment"="ts field from Tracker tbleads"})
     */
    private $dateedited;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="isCoded", type="boolean", nullable=true)
     */
    private $iscoded;

    /**
     * @var string|null
     *
     * @ORM\Column(name="confirmationCode", type="string", length=50, nullable=true)
     */
    private $confirmationcode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="dispatchConfirmer", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $dispatchconfirmer;

    /**
     * @var int|null
     *
     * @ORM\Column(name="createdBy", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $createdby;

    /**
     * @var int|null
     *
     * @ORM\Column(name="leadSourceTypeId", type="integer", nullable=true, options={"unsigned"=true})
     */
    private $leadsourcetypeid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoided(): ?bool
    {
        return $this->voided;
    }

    public function getOfficeid(): ?int
    {
        return $this->officeid;
    }

    public function setOfficeid(?int $officeid): self
    {
        $this->officeid = $officeid;

        return $this;
    }

    public function getClientid(): ?int
    {
        return $this->clientid;
    }

    public function setClientid(?int $clientid): self
    {
        $this->clientid = $clientid;

        return $this;
    }

    public function getDateentered(): ?\DateTimeInterface
    {
        return $this->dateentered;
    }

    public function setDateentered(?\DateTimeInterface $dateentered): self
    {
        $this->dateentered = $dateentered;

        return $this;
    }

    public function getProductid(): ?int
    {
        return $this->productid;
    }

    public function setProductid(?int $productid): self
    {
        $this->productid = $productid;

        return $this;
    }

    public function getDatelead(): ?\DateTimeInterface
    {
        return $this->datelead;
    }

    public function setDatelead(?\DateTimeInterface $datelead): self
    {
        $this->datelead = $datelead;

        return $this;
    }

    public function getGennum(): ?string
    {
        return $this->gennum;
    }

    public function setGennum(?string $gennum): self
    {
        $this->gennum = $gennum;

        return $this;
    }

    public function getGenerator(): ?string
    {
        return $this->generator;
    }

    public function setGenerator(?string $generator): self
    {
        $this->generator = $generator;

        return $this;
    }

    public function getSlmnum(): ?string
    {
        return $this->slmnum;
    }

    public function setSlmnum(?string $slmnum): self
    {
        $this->slmnum = $slmnum;

        return $this;
    }

    public function getSalesrepid(): ?int
    {
        return $this->salesrepid;
    }

    public function setSalesrepid(?int $salesrepid): self
    {
        $this->salesrepid = $salesrepid;

        return $this;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(?string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getContract(): ?float
    {
        return $this->contract;
    }

    public function setContract(?float $contract): self
    {
        $this->contract = $contract;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getJobnumber(): ?int
    {
        return $this->jobnumber;
    }

    public function setJobnumber(?int $jobnumber): self
    {
        $this->jobnumber = $jobnumber;

        return $this;
    }

    public function getReset(): ?string
    {
        return $this->reset;
    }

    public function setReset(?string $reset): self
    {
        $this->reset = $reset;

        return $this;
    }

    public function getResetter(): ?int
    {
        return $this->resetter;
    }

    public function setResetter(?int $resetter): self
    {
        $this->resetter = $resetter;

        return $this;
    }

    public function getCcnumber(): ?string
    {
        return $this->ccnumber;
    }

    public function setCcnumber(?string $ccnumber): self
    {
        $this->ccnumber = $ccnumber;

        return $this;
    }

    public function getCrewchief(): ?int
    {
        return $this->crewchief;
    }

    public function setCrewchief(?int $crewchief): self
    {
        $this->crewchief = $crewchief;

        return $this;
    }

    public function getLeadsourceid(): ?int
    {
        return $this->leadsourceid;
    }

    public function setLeadsourceid(?int $leadsourceid): self
    {
        $this->leadsourceid = $leadsourceid;

        return $this;
    }

    public function getNumberofwindows(): ?int
    {
        return $this->numberofwindows;
    }

    public function setNumberofwindows(?int $numberofwindows): self
    {
        $this->numberofwindows = $numberofwindows;

        return $this;
    }

    public function getPercentsiding(): ?int
    {
        return $this->percentsiding;
    }

    public function setPercentsiding(?int $percentsiding): self
    {
        $this->percentsiding = $percentsiding;

        return $this;
    }

    public function getOfficeleadid(): ?int
    {
        return $this->officeleadid;
    }

    public function setOfficeleadid(?int $officeleadid): self
    {
        $this->officeleadid = $officeleadid;

        return $this;
    }

    public function getNumberofdoors(): ?int
    {
        return $this->numberofdoors;
    }

    public function setNumberofdoors(?int $numberofdoors): self
    {
        $this->numberofdoors = $numberofdoors;

        return $this;
    }

    public function getPercentroof(): ?int
    {
        return $this->percentroof;
    }

    public function setPercentroof(?int $percentroof): self
    {
        $this->percentroof = $percentroof;

        return $this;
    }

    public function getDivision(): ?string
    {
        return $this->division;
    }

    public function setDivision(?string $division): self
    {
        $this->division = $division;

        return $this;
    }

    public function getConstatus(): ?string
    {
        return $this->constatus;
    }

    public function setConstatus(?string $constatus): self
    {
        $this->constatus = $constatus;

        return $this;
    }

    public function getDatecon(): ?\DateTimeInterface
    {
        return $this->datecon;
    }

    public function setDatecon(?\DateTimeInterface $datecon): self
    {
        $this->datecon = $datecon;

        return $this;
    }

    public function getDateorigination(): ?\DateTimeInterface
    {
        return $this->dateorigination;
    }

    public function setDateorigination(?\DateTimeInterface $dateorigination): self
    {
        $this->dateorigination = $dateorigination;

        return $this;
    }

    public function getDateresetter(): ?\DateTimeInterface
    {
        return $this->dateresetter;
    }

    public function setDateresetter(?\DateTimeInterface $dateresetter): self
    {
        $this->dateresetter = $dateresetter;

        return $this;
    }

    public function getRehasher(): ?int
    {
        return $this->rehasher;
    }

    public function setRehasher(?int $rehasher): self
    {
        $this->rehasher = $rehasher;

        return $this;
    }

    public function getConfirmer(): ?int
    {
        return $this->confirmer;
    }

    public function setConfirmer(?int $confirmer): self
    {
        $this->confirmer = $confirmer;

        return $this;
    }

    public function getGennet(): ?string
    {
        return $this->gennet;
    }

    public function setGennet(?string $gennet): self
    {
        $this->gennet = $gennet;

        return $this;
    }

    public function getSpokewith(): ?string
    {
        return $this->spokewith;
    }

    public function setSpokewith(?string $spokewith): self
    {
        $this->spokewith = $spokewith;

        return $this;
    }

    public function getDateedited(): ?\DateTimeInterface
    {
        return $this->dateedited;
    }

    public function setDateedited(?\DateTimeInterface $dateedited): self
    {
        $this->dateedited = $dateedited;

        return $this;
    }

    public function getIscoded(): ?bool
    {
        return $this->iscoded;
    }

    public function setIscoded(?bool $iscoded): self
    {
        $this->iscoded = $iscoded;

        return $this;
    }

    public function getConfirmationcode(): ?string
    {
        return $this->confirmationcode;
    }

    public function setConfirmationcode(?string $confirmationcode): self
    {
        $this->confirmationcode = $confirmationcode;

        return $this;
    }

    public function getDispatchconfirmer(): ?int
    {
        return $this->dispatchconfirmer;
    }

    public function setDispatchconfirmer(?int $dispatchconfirmer): self
    {
        $this->dispatchconfirmer = $dispatchconfirmer;

        return $this;
    }

    public function getCreatedby(): ?int
    {
        return $this->createdby;
    }

    public function setCreatedby(?int $createdby): self
    {
        $this->createdby = $createdby;

        return $this;
    }

    public function getLeadsourcetypeid(): ?int
    {
        return $this->leadsourcetypeid;
    }

    public function setLeadsourcetypeid(?int $leadsourcetypeid): self
    {
        $this->leadsourcetypeid = $leadsourcetypeid;

        return $this;
    }


}
