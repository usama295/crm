<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sale Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="attachment")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Attachment
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
     * @Assert\NotBlank(message="Required")
     **/
    protected $office;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff", inversedBy="attachments")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     **/
    protected $staff;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Customer", inversedBy="attachments")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true)
     **/
    protected $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Appointment", inversedBy="attachments", cascade="persist")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id", nullable=true)
     **/
    protected $appointment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Sale", inversedBy="attachments")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id", nullable=true)
     **/
    protected $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Project", inversedBy="attachments")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true)
     **/
    protected $project;

    /**
     * @ORM\Column(name="size", type="integer")
     * @Assert\Type("integer")
     */
    protected $size;

    /**
     * @ORM\Column(name="filename", type="string",length=255)
     * @Assert\Length(max=255)
     */
    protected $filename;

    /**
     * @ORM\Column(name="localpath", type="string",length=1024)
     * @Assert\Length(max=1024)
     */
    protected $localpath;

    /**
     * @ORM\Column(name="hash", type="string",length=64)
     * @Assert\Length(max=64)
     */
    protected $hash;

    /**
     * @ORM\Column(name="mimetype", type="string",length=40)
     * @Assert\Length(max=40)
     */
    protected $mimetype;


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
     * Set filename
     *
     * @param string $filename
     *
     * @return Attachment
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set localpath
     *
     * @param string $localpath
     *
     * @return Attachment
     */
    public function setLocalpath($localpath)
    {
        $this->localpath = $localpath;

        return $this;
    }

    /**
     * Get localpath
     *
     * @return string
     */
    public function getLocalpath()
    {
        return $this->localpath;
    }

    /**
     * Set hash
     *
     * @param string $hash
     *
     * @return Attachment
     */
    public function setHash($hash)
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * Get hash
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set mimetype
     *
     * @param string $mimetype
     *
     * @return Attachment
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;

        return $this;
    }

    /**
     * Get mimetype
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * Set office
     *
     * @param App\Lev\CRMBundle\Entity\Office $office
     *
     * @return Attachment
     */
    public function setOffice(\App\Lev\CRMBundle\Entity\Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return App\Lev\CRMBundle\Entity\Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set customer
     *
     * @param App\Lev\CRMBundle\Entity\Customer $customer
     *
     * @return Attachment
     */
    public function setCustomer(\App\Lev\CRMBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return App\Lev\CRMBundle\Entity\Customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set appointment
     *
     * @param App\Lev\CRMBundle\Entity\Appointment $appointment
     *
     * @return Attachment
     */
    public function setAppointment(\App\Lev\CRMBundle\Entity\Appointment $appointment = null)
    {
        $this->appointment = $appointment;

        return $this;
    }

    /**
     * Get appointment
     *
     * @return App\Lev\CRMBundle\Entity\Appointment
     */
    public function getAppointment()
    {
        return $this->appointment;
    }

    /**
     * Set sale
     *
     * @param App\Lev\CRMBundle\Entity\Sale $sale
     *
     * @return Attachment
     */
    public function setSale(App\Lev\CRMBundle\Entity\Sale $sale = null)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return App\Lev\CRMBundle\Entity\Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set project
     *
     * @param App\Lev\CRMBundle\Entity\Project $project
     *
     * @return Attachment
     */
    public function setProject(App\Lev\CRMBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return App\Lev\CRMBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set size
     *
     * @param integer $size
     *
     * @return Attachment
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getWebPath()
    {
        return '/files/' . $this->getHash() . '/' . $this->getFilename();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'          => $this->getId(),
            'office'      => $this->getOffice()->toArray(),
            'staff'       => $this->getStaff() ? $this->getStaff()->toArrayShort() : null,
            'customer'    => $this->getCustomer() ? $this->getCustomer()->toArrayShort() : null,
            'appointment' => $this->getAppointment() ? $this->getAppointment()->toArrayShort() : null,
            'sale'        => $this->getSale() ? $this->getSale()->toArrayShort() : null,
            'project'     => $this->getProject() ? $this->getProject()->toArrayShort() : null,
            'filename'    => $this->getFilename(),
            'size'        => $this->getSize(),
            'hash'        => $this->getHash(),
            'mimetype'    => $this->getMimetype(),
            'webpath'     => $this->getWebPath(),
            'createdAt'   => $this->getCreatedAt(),
            'createdBy'   => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
        );
    }

    /**
     * @return array
     */
    public function toArrayShort()
    {
        return array(
            'id'          => $this->getId(),
            'filename'    => $this->getFilename(),
            'size'        => $this->getSize(),
            'hash'        => $this->getHash(),
            'mimetype'    => $this->getMimetype(),
            'webpath'     => $this->getWebPath(),
            'createdAt'   => $this->getCreatedAt(),
        );
    }


    /**
     * Set staff
     *
     * @param App\Lev\CRMBundle\Entity\Staff $staff
     *
     * @return Attachment
     */
    public function setStaff(\App\Lev\CRMBundle\Entity\Staff $staff = null)
    {
        $this->staff = $staff;

        return $this;
    }

    /**
     * Get staff
     *
     * @return App\Lev\CRMBundle\Entity\Staff
     */
    public function getStaff()
    {
        return $this->staff;
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatePersistRecord()
    {
        if ($this->getProject()) {
            $this->setSale($this->getProject()->getSale());
        }

        if ($this->getSale()) {
            $this->setAppointment($this->getSale()->getAppointment());
        }

        if ($this->getAppointment()) {
            $this->setCustomer($this->getAppointment()->getCustomer());
        }

        if ($this->getCustomer()) {
            $this->setOffice($this->getCustomer()->getOffice());
        }
    }
}
