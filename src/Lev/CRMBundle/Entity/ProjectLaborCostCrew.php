<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ProjectLaborCostCrew
 *
 * @ORM\Table(name="project_labor_cost_crew", indexes={@ORM\Index(name="IDX_109B13775FE259F6", columns={"crew_id"}), @ORM\Index(name="IDX_109B137768FB27A", columns={"project_labor_cost_id"})})
 * @ORM\Entity
 */
class ProjectLaborCostCrew
{
    /**
     * @var int
     *
     * @ORM\Column(name="project_labor_cost_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $projectLaborCostId;

    /**
     * @var \Crew
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Crew")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="crew_id", referencedColumnName="id")
     * })
     */
    private $crew;

    public function getProjectLaborCostId(): ?int
    {
        return $this->projectLaborCostId;
    }

    public function getCrew(): ?Crew
    {
        return $this->crew;
    }

    public function setCrew(?Crew $crew): self
    {
        $this->crew = $crew;

        return $this;
    }


}
