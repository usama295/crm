<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="project_estimate")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ProjectEstimate
{
    use Traits\BlameableCreateEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Project", inversedBy="estimates")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $project;

    /**
     * @ORM\Column(name="product", type="string", length=100, nullable=true)
     * @Assert\Length(max=100)
     */
    protected $product;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Institution", inversedBy="estimates")
     * @ORM\JoinColumn(name="institution_id", referencedColumnName="id", nullable=true)
     */
    protected $institution;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Contractor", inversedBy="estimates")
     * @ORM\JoinColumn(name="contractor_id", referencedColumnName="id", nullable=true)
     */
    protected $contractor;

    /**
     * @ORM\Column(name="cost", type="decimal", precision=11, scale=2, nullable=true)
     */
    protected $cost;

    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    protected $note;

    /**
     * @ORM\Column(name="type", type="string", length=15, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "material", "financial", "misc", "contractor"},
     *   message = "Invalid type."
     * )
     * @Assert\Length(max=15)
     */
    protected $type;

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'          => $this->getId(),
            'project'     => $this->getProject()->getId(),
            'product'     => $this->getProduct(),
            'type'        => $this->getType(),
            'contractor'  => $this->getContractor() ? $this->getContractor()->toArray() : null,
            'institution' => $this->getInstitution() ? $this->getInstitution()->toArray() : null,
            'cost'        => $this->getCost(),
            'note'        => $this->getNote(),
            'createdAt'   => $this->getCreatedAt(),
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
     * Set product
     *
     * @param string $product
     *
     * @return ProjectEstimate
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return ProjectEstimate
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return ProjectEstimate
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     *
     * @return ProjectEstimate
     */
    public function setProject(\App\Lev\CRMBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \App\Lev\CRMBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set institution
     *
     * @param \App\Lev\CRMBundle\Entity\Institution $institution
     *
     * @return ProjectEstimate
     */
    public function setInstitution(\App\Lev\CRMBundle\Entity\Institution $institution = null)
    {
        $this->institution = $institution;

        return $this;
    }

    /**
     * Get institution
     *
     * @return \App\Lev\CRMBundle\Entity\Institution
     */
    public function getInstitution()
    {
        return $this->institution;
    }

    /**
     * Set contractor
     *
     * @param \App\Lev\CRMBundle\Entity\Contractor $contractor
     *
     * @return ProjectEstimate
     */
    public function setContractor(\App\Lev\CRMBundle\Entity\Contractor $contractor = null)
    {
        $this->contractor = $contractor;

        return $this;
    }

    /**
     * Get contractor
     *
     * @return \App\Lev\CRMBundle\Entity\Contractor
     */
    public function getContractor()
    {
        return $this->contractor;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return ProjectEstimate
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
