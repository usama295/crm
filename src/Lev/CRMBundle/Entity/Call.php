<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sale Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="customer_call")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Call
{
    use Traits\BlameableCreateEntity;

    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    /**
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $datetime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(message="Required")
     **/
    protected $office;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank(message="Required")
     **/
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Appointment", inversedBy="call")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", nullable=true)
     **/
    protected $appointment;

    /**
     * @ORM\Column(name="outcome", type="string", length=15, nullable=false)
     * @Assert\Choice(choices = {"scheduled", "confirmed", "not-qualified", "wrong-number", "tcpa", "no-answer", "left-message", "busy-signal", "schedule-later", "call-later"}, message = "Invalid status." )
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=15)
     */
    protected $outcome;

    /**
     * To Array
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'            => $this->getId(),
            'customer'      => $this->getCustomer() ? $this->getCustomer()->toArrayShort() : null,
            'customerID'    => $this->getCustomer() ? $this->getCustomer()->getId() : null,
            'appointment'   => $this->getAppointment() ? $this->getAppointment()->toArrayShort() : null,
            'appointmentID' => $this->getAppointment() ? $this->getAppointment()->getId() : null,
            'outcome'       => $this->getOutcome(),
            'createdAt'     => $this->getCreatedAt(),
            'createdBy'     => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
        );
    }


    /**
     * To Array
     * @return array
     */
    public function toArrayToAppointments()
    {
        return array(
            'id'            => $this->getId(),
            'outcome'       => $this->getOutcome(),
            'createdAt'     => $this->getCreatedAt(),
            'createdBy'     => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
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
     * Set outcome
     *
     * @param string $outcome
     *
     * @return Call
     */
    public function setOutcome($outcome)
    {
        $this->outcome = $outcome;

        return $this;
    }

    /**
     * Get outcome
     *
     * @return string
     */
    public function getOutcome()
    {
        return $this->outcome;
    }

    /**
     * Set callback
     *
     * @param \DateTime $callback
     *
     * @return Call
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get callback
     *
     * @return \DateTime
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set dueBy
     *
     * @param \DateTime $dueBy
     *
     * @return Call
     */
    public function setDueBy($dueBy)
    {
        $this->dueBy = $dueBy;

        return $this;
    }

    /**
     * Get dueBy
     *
     * @return \DateTime
     */
    public function getDueBy()
    {
        return $this->dueBy;
    }

    /**
     * Set doneAt
     *
     * @param \DateTime $doneAt
     *
     * @return Call
     */
    public function setDoneAt($doneAt)
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    /**
     * Get doneAt
     *
     * @return \DateTime
     */
    public function getDoneAt()
    {
        return $this->doneAt;
    }

    /**
     * Set office
     *
     * @param \App\Lev\CRMBundle\Entity\Office $office
     *
     * @return Call
     */
    public function setOffice(\App\Lev\CRMBundle\Entity\Office $office)
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
     * Set customer
     *
     * @param \App\Lev\CRMBundle\Entity\Customer $customer
     *
     * @return Call
     */
    public function setCustomer(\App\Lev\CRMBundle\Entity\Customer $customer)
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
     * Set appointment
     *
     * @param \App\Lev\CRMBundle\Entity\Appointment $appointment
     *
     * @return Call
     */
    public function setAppointment(\App\Lev\CRMBundle\Entity\Appointment $appointment = null)
    {
        $this->appointment = $appointment;

        return $this;
    }

    /**
     * Get appointment
     *
     * @return \App\Lev\CRMBundle\Entity\Appointment
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->histories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add history
     *
     * @param \App\Lev\CRMBundle\Entity\History $history
     *
     * @return Call
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

   

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Call
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $createdBy
     * @return Call
     */
    public function setCreatedBy(\App\Lev\CRMBundle\Entity\Staff $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(?\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }
}
