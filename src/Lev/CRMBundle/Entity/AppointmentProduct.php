<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="appointment_product")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class AppointmentProduct extends AbstractCalculatorProduct
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Appointment", inversedBy="products")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id")
     */
    protected $appointment;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Product", inversedBy="appointments")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

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
     * @return AppointmentProduct
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
     * Set product
     *
     * @param \App\Lev\CRMBundle\Entity\Product $product
     *
     * @return AppointmentProduct
     */
    public function setProduct(\App\Lev\CRMBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \App\Lev\CRMBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
