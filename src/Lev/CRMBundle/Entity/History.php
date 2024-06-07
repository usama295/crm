<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="history")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class History
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Customer", inversedBy="histories")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     */
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Appointment", inversedBy="histories")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", nullable=true)
     */
    protected $appointment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Sale", inversedBy="histories")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id", nullable=true)
     */
    protected $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Project", inversedBy="histories")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     */
    protected $project;

    /**
     * @ORM\Column(name="subject", type="string", length=20)
     */
    protected $subject;

    /**
     * @ORM\Column(name="message_code", type="integer")
     */
    protected $messageCode;

    /**
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    protected $message;

    public function toArray()
    {
        return array(
            'id'            => $this->getId(),
            'customerId'    => $this->getCustomer() ? $this->getCustomer()->getId() : null,
            'appointmentId' => $this->getAppointment() ? $this->getAppointment()->getId() : null,
            'saleId'        => $this->getSale() ? $this->getSale()->getId() : null,
            'projectId'     => $this->getProject() ? $this->getProject()->getId() : null,
            'subject'       => $this->getSubject(),
            'messageCode'   => $this->getMessageCode(),
            'message'       => $this->getMessage(),
            'createdAt'     => $this->getCreatedAt(),
            'createdBy'     => $this->getCreatedBy() ? array(
                'id'        => $this->getCreatedBy()->getId(),
                'username'  => $this->getCreatedBy()->getUsername(),
                'firstName' => $this->getCreatedBy()->getFirstName(),
                'lastName'  => $this->getCreatedBy()->getLastName(),
            ) : null,
        );
    }

    public function toArrayShort()
    {
        return array(
            'id'            => $this->getId(),
            'subject'       => $this->getSubject(),
            'messageCode'   => $this->getMessageCode(),
            'message'       => $this->getMessage(),
            'createdAt'     => $this->getCreatedAt(),
            'createdBy'     => $this->getCreatedBy() ? array(
                'id'        => $this->getCreatedBy()->getId(),
                'username'  => $this->getCreatedBy()->getUsername(),
                'firstName' => $this->getCreatedBy()->getFirstName(),
                'lastName'  => $this->getCreatedBy()->getLastName(),
                'fullName'  => $this->getCreatedBy()->getFullName(),
            ) : null,
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
     * Set appointment
     *
     * @param \App\Lev\CRMBundle\Entity\Appointment $appointment
     *
     * @return History
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
     * Set sale
     *
     * @param \App\Lev\CRMBundle\Entity\Sale $sale
     *
     * @return History
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
     * Set project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     *
     * @return History
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
     * Set customer
     *
     * @param \App\Lev\CRMBundle\Entity\Customer $customer
     *
     * @return History
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
     * Set subject
     *
     * @param string $subject
     *
     * @return History
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set messageCode
     *
     * @param integer $messageCode
     *
     * @return History
     */
    public function setMessageCode($messageCode)
    {
        $this->messageCode = $messageCode;

        return $this;
    }

    /**
     * Get messageCode
     *
     * @return integer
     */
    public function getMessageCode()
    {
        return $this->messageCode;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return History
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

}
