<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sale Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="project")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Project
{
    use Traits\BlameableCreateEntity;

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Office is required")
     **/
    protected $office;

    /**
     * @ORM\OneToOne(targetEntity="App\Lev\CRMBundle\Entity\Sale", inversedBy="project")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Sale is required")
     **/
    protected $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Custome is required")
     **/
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="job_manager_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Job Manager is required")
     **/
    protected $jobManager;

    /**
     * @ORM\Column(name="job_category", type="array", length=256, nullable=true)
     * @Assert\Choice(
     *   choices = {"doors", "gutters", "windows", "roofing", "siding", "trim"},
     *   multiple = true,
     *   message = "Invalid job category."
     * )
     */
    protected $jobCategory;

    /**
     * @ORM\Column(name="comments", type="text", nullable=true)
     */
    protected $comments;

    /**
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "none", "on-hold", "completed", "canceled"},
     *   message = "Invalid status."
     * )
     * @Assert\Length(max=10, maxMessage="Status length must be 10 chars max.")
     */
    protected $status;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\ProjectEstimate", mappedBy="project", cascade={"persist", "remove"})
     */
   protected $estimates;

    /**
     * @ORM\Column(name="enabled_activities", type="array", length=256, nullable=true)
     * @Assert\Choice(
     *   choices = {"order", "measure", "service", "install"},
     *   multiple = true,
     *   message = "Invalid activity.."
     * )
     */
    protected $enabledActivities;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\ProjectActivity", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $activities;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\History", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $histories;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Attachment", mappedBy="project", cascade={"persist", "remove"})
     */
    protected $attachments;

    /**
     * @ORM\Column(name="salesforce_id", type="string", length=20, nullable=true)
     */
    protected $salesforceId;

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
     * @ORM\Column(name="install_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $installDate;

    /**
     * @ORM\Column(name="complete_at", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $completedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->estimates   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activities  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->histories   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \DateTime();
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
     * Set comments
     *
     * @param string $comments
     *
     * @return Project
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
     * Set status
     *
     * @param string $status
     *
     * @return Project
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set office
     *
     * @param \App\Lev\CRMBundle\Entity\Office $office
     *
     * @return Project
     */
    public function setOffice(\App\Lev\CRMBundle\Entity\Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return \App\Lev\CRMBundle\Entity\Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set sale
     *
     * @param \App\Lev\CRMBundle\Entity\Sale $sale
     *
     * @return Project
     */
    public function setSale(\App\Lev\CRMBundle\Entity\Sale $sale = null)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return \App\Lev\CRMBundle\Entity\Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set customer
     *
     * @param \App\Lev\CRMBundle\Entity\Customer $customer
     *
     * @return Project
     */
    public function setCustomer(\App\Lev\CRMBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \App\Lev\CRMBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set jobManager
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $jobManager
     *
     * @return Project
     */
    public function setJobManager(\App\Lev\CRMBundle\Entity\Staff $jobManager = null)
    {
        $this->jobManager = $jobManager;

        return $this;
    }

    /**
     * Get jobManager
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getJobManager()
    {
        return $this->jobManager;
    }

    /**
     * Set jobCategory
     *
     * @param array $jobCategory
     *
     * @return Project
     */
    public function setJobCategory($jobCategory)
    {
        $this->jobCategory = $jobCategory;

        return $this;
    }

    /**
     * Get jobCategory
     *
     * @return array
     */
    public function getJobCategory()
    {
        return $this->jobCategory;
    }

    /**
     * Add activity
     *
     * @param \App\Lev\CRMBundle\Entity\ProjectActivity $activity
     *
     * @return Project
     */
    public function addActivity(\App\Lev\CRMBundle\Entity\ProjectActivity $activity)
    {
        $this->activities[] = $activity;

        return $this;
    }

    /**
     * Remove activity
     *
     * @param \App\Lev\CRMBundle\Entity\ProjectActivity $activity
     */
    public function removeActivity(\App\Lev\CRMBundle\Entity\ProjectActivity $activity)
    {
        $this->activities->removeElement($activity);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * Add history
     *
     * @param \App\Lev\CRMBundle\Entity\History $history
     *
     * @return Project
     */
    public function addHistory(\App\Lev\CRMBundle\Entity\History $history)
    {
        $this->histories[] = $history;

        return $this;
    }

    /**
     * Remove history
     *
     * @param \App\Lev\CRMBundle\Entity\History $history
     */
    public function removeHistory(\App\Lev\CRMBundle\Entity\History $history)
    {
        $this->histories->removeElement($history);
    }

    /**
     * Get histories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getHistories()
    {
        return $this->histories;
    }

    public function toArrayWithHistory()
    {
        $data = $this->toArray();
        $histories = array();
        foreach($this->getHistories() as $history) {
            $histories[] = $history->toArray();
        }

        $data['histories'] = $histories;

        return $data;
    }

    public function toArray()
    {
        $estimates = array();
        foreach ($this->getEstimates() as $estimate) {
            $estimates[] = $estimate->toArray();
        }

        $activities = array();
        foreach ($this->getActivities() as $activity) {
            $activities[] = $activity->toArray();
        }

        $attachments = array();
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = $attachment->toArrayShort();
        }

        return array(
            'id' => $this->getId(),
            'jobCategory'     => $this->getJobCategory(),
            'jobManager'      => $this->getJobManager()? $this->getJobManager()->toArrayShort() : null,
            'comments'        => $this->getComments(),
            'status'          => $this->getStatus(),
            'sale'            => $this->getSale()->toArrayShortProject(),
            'saleId'          => $this->getSale()->getId(),
            'office'          => $this->getOffice()->toArray(),
            'customer'        => $this->getCustomer() ? $this->getCustomer()->toArrayShort() : null,
            'projectEstimate' => $estimates,
            'activities'      => $activities,
            'attachments'     => $attachments,
            'startDate'       => $this->getStartDate(),
            'endDate'         => $this->getEndDate(),
            'installDate'     => $this->getInstallDate(),
            'completedAt'     => $this->getCompletedAt(),
            'createdAt'       => $this->getCreatedAt(),
            'createdBy'       => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'enabledActivities' => $this->getEnabledActivities(),
        );
    }


    /**
     * Add estimate
     *
     * @param \App\Lev\CRMBundle\Entity\ProjectEstimate $estimate
     *
     * @return Project
     */
    public function addEstimate(\App\Lev\CRMBundle\Entity\ProjectEstimate $estimate)
    {
        $this->estimates[] = $estimate;

        return $this;
    }

    /**
     * Remove estimate
     *
     * @param \App\Lev\CRMBundle\Entity\ProjectEstimate $estimate
     */
    public function removeEstimate(\App\Lev\CRMBundle\Entity\ProjectEstimate $estimate)
    {
        $this->estimates->removeElement($estimate);
    }

    /**
     * Get estimates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstimates()
    {
        return $this->estimates;
    }

    /**
     * Add attachment
     *
     * @param \App\Lev\CRMBundle\Entity\Attachment $attachment
     *
     * @return Project
     */
    public function addAttachment(\App\Lev\CRMBundle\Entity\Attachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param \App\Lev\CRMBundle\Entity\Attachment $attachment
     */
    public function removeAttachment(\App\Lev\CRMBundle\Entity\Attachment $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Set salesforceId
     *
     * @param string $salesforceId
     *
     * @return Project
     */
    public function setSalesforceId($salesforceId)
    {
        $this->salesforceId = $salesforceId;

        return $this;
    }

    /**
     * Get salesforceId
     *
     * @return string
     */
    public function getSalesforceId()
    {
        return $this->salesforceId;
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatePersistRecord()
    {
        $this->setCustomer($this->getSale()->getCustomer());
        $this->setOffice($this->getCustomer()->getOffice());

        $startDate   = null;
        $endDate     = null;
        $installDate = null;
        $complete    = true;
        $completedAt = null;
        /**
         * @var \App\Lev\CRMBundle\Entity\ProjectActivity $activity
         */
        foreach ($this->getActivities() as $activity) {

            // StartDate
            if (null === $startDate) {
                $startDate = $activity->getStartDate();
            };
            if (
              $startDate instanceof \DateTime
              && $activity->getStartDate() instanceof \DateTime
              && $activity->getStartDate() < $startDate
            ) {
                $startDate = $activity->getStartDate();
            }

            // EndDate
            if (null === $endDate) {
                $endDate = $activity->getEndDate();
            }
            if ($endDate instanceof \DateTime
              && $activity->getEndDate() instanceof \DateTime
              && $activity->getEndDate() > $endDate
            ) {
                $endDate = $activity->getEndDate();
            }

            // InstallDate
            if (
              $activity->getStartDate() instanceof \DateTime
              && $activity->getName() === 'Install'
            ) {
                $installDate = $activity->getStartDate();
            }

            // CompletedAt
            if (null === $activity->getCompletedAt()) {
                $complete = false;
            }
            $completedAt = ($completedAt instanceof \DateTime
                           && $activity->getCompletedAt() instanceof \DateTime
                           && $activity->getCompletedAt() > $completedAt)
                ? $activity->getCompletedAt()
                : ($activity->getCompletedAt() instanceof \DateTime)
                    ? $activity->getCompletedAt()
                    : $completedAt;
        }
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        $this->setInstallDate($installDate);
        $this->setCompletedAt($complete ? $completedAt : null);
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Project
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
     * @return Project
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
     * Set installDate
     *
     * @param \DateTime $installDate
     *
     * @return Project
     */
    public function setInstallDate($installDate)
    {
        $this->installDate = $installDate;

        return $this;
    }

    /**
     * Get installDate
     *
     * @return \DateTime
     */
    public function getInstallDate()
    {
        return $this->installDate;
    }


    /**
     * Set completedAt
     *
     * @param \DateTime $completedAt
     *
     * @return Project
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
     * Set enabledActivities
     *
     * @param array $enabledActivities
     *
     * @return Project
     */
    public function setEnabledActivities($enabledActivities)
    {
        $this->enabledActivities = $enabledActivities;

        return $this;
    }

    /**
     * Get enabledActivities
     *
     * @return array
     */
    public function getEnabledActivities()
    {
        return $this->enabledActivities;
    }
}
