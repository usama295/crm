<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProjectLaborCost
 *
 * @ORM\Table(name="project_labor_cost", indexes={@ORM\Index(name="IDX_511446A710405986", columns={"institution_id"}), @ORM\Index(name="IDX_511446A7166D1F9C", columns={"project_id"}), @ORM\Index(name="IDX_511446A7B0265DC7", columns={"contractor_id"}), @ORM\Index(name="IDX_511446A7DE12AB56", columns={"created_by"})})
 * @ORM\Entity
 */
class ProjectLaborCost
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int|null
     *
     * @ORM\Column(name="project_id", type="integer", nullable=true)
     */
    private $projectId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=20, nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cost", type="decimal", precision=11, scale=2, nullable=true)
     */
    private $cost;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="contractor_id", type="integer", nullable=true)
     */
    private $contractorId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="note", type="text", length=0, nullable=true)
     */
    private $note;

    /**
     * @var \Institution
     *
     * @ORM\ManyToOne(targetEntity="Institution")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="institution_id", referencedColumnName="id")
     * })
     */
    private $institution;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Contractor", inversedBy="projectLaborCost")
     * @ORM\JoinTable(name="project_labor_cost_contractor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="project_labor_cost_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="contractor_id", referencedColumnName="id")
     *   }
     * )
     */
    private $contractor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->contractor = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(?int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?int $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getContractorId(): ?int
    {
        return $this->contractorId;
    }

    public function setContractorId(?int $contractorId): self
    {
        $this->contractorId = $contractorId;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getInstitution(): ?Institution
    {
        return $this->institution;
    }

    public function setInstitution(?Institution $institution): self
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * @return Collection|Contractor[]
     */
    public function getContractor(): Collection
    {
        return $this->contractor;
    }

    public function addContractor(Contractor $contractor): self
    {
        if (!$this->contractor->contains($contractor)) {
            $this->contractor[] = $contractor;
        }

        return $this;
    }

    public function removeContractor(Contractor $contractor): self
    {
        if ($this->contractor->contains($contractor)) {
            $this->contractor->removeElement($contractor);
        }

        return $this;
    }

}
