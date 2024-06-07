<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="project_calendar")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ProjectActivity
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Project", inversedBy="activities")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=50)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Required")
     **/
    protected $assignee;

    /**
     * @ORM\Column(name="type", type="string", length=256)
     * @Assert\Choice(
     *   choices = {"doors", "gutters", "windows", "roofing", "siding", "trim"},
     *   multiple = true,
     *   message = "Invalid type."
     * )
     */
    protected $type;

    /**
     * @ORM\Column(name="start_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $startDate;

    /**
     * @ORM\Column(name="end_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $endDate;

    /**
     * @ORM\Column(name="completed_at", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $completedAt;

    /**
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    protected $comments;

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
     * Set name
     *
     * @param string $name
     *
     * @return ProjectActivity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return ProjectActivity
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

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return ProjectActivity
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return ProjectActivity
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     *
     * @return ProjectActivity
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get completedAt
     *
     * @return \DateTime
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * Set comments
     *
     * @param string $comments
     *
     * @return ProjectActivity
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * Get comments
     *
     * @return string
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     *
     * @return ProjectActivity
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
     * Set assignee
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $assignee
     *
     * @return ProjectActivity
     */
    public function setAssignee(\App\Lev\CRMBundle\Entity\Staff $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'          => $this->getId(),
            'project'     => $this->getProject()->getId(),
            'name'        => $this->getName(),
            'assignee'    => $this->getAssignee() ? $this->getAssignee()->toArray() : null,
            'type'        => $this->getType(),
            'startDate'   => $this->getStartDate(),
            'endDate'     => $this->getEndDate(),
            'completedAt' => $this->getCompletedAt(),
            'comments'    => $this->getComments(),
        );
    }
}
