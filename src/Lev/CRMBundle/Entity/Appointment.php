<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Blameable\Traits\BlameableEntity;
/**
 * Appointment Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="appointment")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Appointment
{
    use Traits\BlameableCreateEntity;

    static public $paymentTypeLabels = array(
      "cash"        => "Cash",
      "credit-card" => "Credit Card",
      "financing"   => "Financing",
      "other"       => "Other",
    );



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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Customer is required")
     **/
    protected $customer;

    /**
     * @ORM\Column(name="address_street", type="string",length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="Address Street length must be 100 chars max.")
     */
    protected $addressStreet;

    /**
     * @ORM\Column(name="address_city", type="string",length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="Address city length must be 100 chars max.")
     */
    protected $addressCity;

    /**
     * @ORM\Column(name="address_state", type="string",length=2, nullable=true)
     * @Assert\Length(max=2, maxMessage="Address state length must be 2 chars max.")
     */
    protected $addressState;

    /**
     * @ORM\Column(name="address_zip", type="string",length=9, nullable=true)
     * @Assert\Length(max=9, maxMessage="Address ZIP length must be 9 chars max.")
     */
    protected $addressZip;


    /**
     * @ORM\Column(name="address_lat", type="decimal", precision=10, scale=6, nullable=true)
     */
    protected $addressLat;

    /**
     * @ORM\Column(name="address_lng", type="decimal", precision=10, scale=6, nullable=true)
     */
    protected $addressLng;

    /**
     * @ORM\Column(name="product_interest", type="array", nullable=true)
     * @Assert\Choice(
     *   choices = {"", "windows", "roofing", "siding", "gutters", "trim", "doors"},
     *   message = "Invalid product interest {{ value }}.",
     *   multiple = true
     * )
     */
    protected $productInterest;

    /**
     * @ORM\Column(name="roof_age", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Roof Age length must be 50 chars max.")
     */
    protected $roofAge;

    /**
     * @ORM\Column(name="windows_qty", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Windows Qty length must be 50 chars max.")
     */
    protected $windowsQty;

    /**
     * @ORM\Column(name="windows_last_replaced", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Windows last replaced length must be 50 chars max.")
     */
    protected $windowsLastReplaced;

    /**
     * @ORM\Column(name="sides_qty", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Sides Qty length must be 50 chars max.")
     */
    protected $sidesQty;

    /**
     * @ORM\Column(name="credit_quality_value", type="integer", nullable=true)
     */
    protected $creditQualityValue;

    /**
     * @ORM\Column(name="siding_age", type="string", length=50, nullable=true)
     * @Assert\Length(max=50, maxMessage="Siding Age length must be 50 chars max.")
     */
    protected $sidingAge;

    /**
     * @ORM\Column(name="type", type="string", length=30, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "rehash", "reset", "new", "vivint", "follow-ups", "cancel-save"},
     *   message = "Invalid type {{ value }}."
     * )
     * @Assert\Length(max=30, maxMessage="Type length must be 30 chars max.")
     */
    protected $type;

    /**
     * @ORM\Column(name="cancel_reason", type="string", length=100, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "not-interested", "missing-party", "not-confirmed", "bad-timing", "renter-non-owner", "cant-do-job", "missing-party", "wrong-number", "tcpa", "out-of-area", "mobile-home", "bad-or-old-data", "credit", "other"},
     *   message = "Invalid cancel reason {{ value }}."
     * )
     * @Assert\Length(max=100, maxMessage="Cancel Reason length must be 100 chars max.")
     */
    protected $cancelReason;

    /**
     * @ORM\Column(name="pitch_miss_reason", type="string", length=30, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "other-estimates", "not-interested", "cant-afford", "third-party-influence", "no-decision-in-one-day", "home-for-sale", "cant-do-job"},
     *   message = "Invalid pitch-miss reason {{ value }}."
     * )
     * @Assert\Length(max=30, maxMessage="Pitch-miss length must be 30 chars max.")
     */
    protected $pitchMissReason;

    /**
     * @ORM\Column(name="no_pitch_reason", type="string", length=15, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "one-leg", "no-show", "job-too-small", "cant-do-job", "renters"},
     *   message = "Invalid no-pitch reason {{ value }}."
     * )
     * @Assert\Length(max=15, maxMessage="No-pitch length must be 15 chars max.")
     */
    protected $noPitchReason;

    /**
     * @ORM\Column(name="notes", type="text", nullable=true)
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}")
     */
    protected $notes;

    /**
     * @ORM\Column(name="datetime", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $datetime;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="sales_rep_id", referencedColumnName="id")
     **/
    protected $salesRep;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="marketing_rep_id", referencedColumnName="id")
     **/
    protected $marketingRep;

    /**
     * @ORM\Column(name="marketer_source", type="string", length=25, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "website", "call-in", "referral", "vivant-solar", "maryland-marketing", "solar-city"},
     *   message = "Invalid marketer source {{ value }}."
     * )
     * @Assert\Length(max=25, maxMessage="Marketer Source length must be 15 chars max.")
     */
    protected $marketerSource;

    /**
     * @ORM\Column(name="status", type="string", length=15, nullable=false, options={"default" = "pending"})
     * @Assert\Choice(
     *   choices = {"pending", "scheduled", "no-pitch", "confirmed", "sold", "pitch-miss", "canceled", "archived"},
     *   message = "Invalid status {{ value }}."
     * )
     * @Assert\Length(max=15, maxMessage="Status length must be 15 chars max.")
     */
    protected $status;

    /**
     * @ORM\OneToOne(targetEntity="App\Lev\CRMBundle\Entity\Sale", mappedBy="appointment", cascade={"persist", "remove"})
     **/
    protected $sale;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\AppointmentProduct", mappedBy="appointment", cascade={"persist", "remove"})
     */
    protected $products;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\History", mappedBy="appointment", cascade={"persist", "remove"})
     */
    protected $histories;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Attachment", mappedBy="appointment", cascade={"persist", "remove"})
     */
    protected $attachments;

    /**
     * @ORM\Column(name="salesforce_id", type="string", length=20, nullable=true)
     */
    protected $salesforceId;

    /**
     * @ORM\Column(name="callback", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $callback;

    /**
     * @ORM\Column(name="due_by", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $dueBy;

    /**
     * @ORM\Column(name="done_at", type="datetime", nullable=true)
     * @Assert\Date()
     */
    protected $doneAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="locked_by", referencedColumnName="id", nullable=true)
     **/
    protected $lockedBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Call", mappedBy="appointment", cascade={"persist", "remove"})
     */
    protected $calls;

    /**
     * @ORM\Column(name="confirmed_sales_rep", type="boolean", nullable=false, options={"default" = 0})
     */
    protected $confirmedSalesRep;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\CallRecord", mappedBy="appointment", cascade={"persist", "remove"})
     */
    protected $callRecords;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="reset_by", referencedColumnName="id", nullable=true)
     **/
    protected $resetBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Staff")
     * @ORM\JoinColumn(name="callcenter_rep", referencedColumnName="id", nullable=true)
     **/
    protected $callcenterRep;

    /**
     * @ORM\Column(name="payment_type", type="string", length=15, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "cash", "credit-card", "financing", "other"},
     *   message = "Invalid payment type {{ value }}."
     * )
     * @Assert\Length(max=15, maxMessage="Payment type length must be 15 chars max.")
     */
    protected $paymentType;

    /**
     * @ORM\Column(name="discount_method", type="string", length=20, options={"default" = "daily-price"}, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "daily-price", "year-price", "marketing-home", "flex-install", "family-friends", "other"},
     *   message = "Invalid discount method {{ value }}."
     * )
     * @Assert\Length(max=15, maxMessage="Discount method length must be 15 chars max.")
     */
    protected $discountMethod;

    /**
     * @ORM\Column(name="discount_percentage", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $discountPercentage;

    /**
     * @ORM\Column(name="down_payment", type="float", nullable=true)
     */
    protected $downPayment;

    /**
     * @ORM\Column(name="sales_tax", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $salesTax;

    /**
     * @ORM\Column(name="contract_sign_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid Contract Signature Date")
     */
    protected $contractSignDate;

    /**
     * @ORM\Column(name="contract_cancellation_due_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid Cancellation Due Date")
     */
    protected $contractCancellationDueDate;

    /**
     * @ORM\Column(name="contract_delivery_address", type="string",length=1000, nullable=true)
     * @Assert\Length(max=1000, maxMessage="Contract Delivery Address length must be 1000 chars max.")
     */
    protected $contractDeliveryAddress;

    /**
     * @ORM\Column(name="contract_customer_signature1", type="text", nullable=true)
     */
    protected $contractCustomerSignature1;

    /**
     * @ORM\Column(name="contract_customer_signature2", type="text", nullable=true)
     */
    protected $contractCustomerSignature2;

    /**
     * @ORM\Column(name="contract_recipient_email_address", type="string",length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="Contract Recipiente Email Address length must be 100 chars max.")
     */
    protected $contractRecipientEmailAddress;

    /**
     * @ORM\Column(name="job_ceiling", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $jobCeiling;

    /** @ORM\Column(name="is_legacy", type="boolean", nullable=false, options={"default" = 0})
    */
    protected $is_legacy =0;

     /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 1})
     */
    protected $isdeleted = 0;




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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        $pi = array_map('ucfirst', $this->getProductInterest());

        $productInterest = ucfirst(implode(', ', $pi));
        $customerName    = $this->getCustomer()->getPrimaryFirstName()
            . ' ' . $this->getCustomer()->getPrimaryLastName();
        return "$productInterest appointment for $customerName";
    }

    /**
     * Set productInterest
     *
     * @param string $productInterest
     *
     * @return Appointment
     */
    public function setProductInterest($productInterest)
    {
        $this->productInterest = $productInterest;

        return $this;
    }

    /**
     * Get productInterest
     *
     * @return string
     */
    public function getProductInterest()
    {
        return $this->productInterest;
    }

    /**
     * Set roofAge
     *
     * @param integer $roofAge
     * @return Appointment
     */
    public function setRoofAge($roofAge)
    {
        $this->roofAge = $roofAge;

        return $this;
    }

    /**
     * Get roofAge
     *
     * @return integer
     */
    public function getRoofAge()
    {
        return $this->roofAge;
    }

    /**
     * Set windowsQty
     *
     * @param integer $windowsQty
     * @return Appointment
     */
    public function setWindowsQty($windowsQty)
    {
        $this->windowsQty = $windowsQty;

        return $this;
    }

    /**
     * Get windowsQty
     *
     * @return integer
     */
    public function getWindowsQty()
    {
        return $this->windowsQty;
    }

    /**
     * Set windowsLastReplaced
     *
     * @param string $windowsLastReplaced
     * @return Appointment
     */
    public function setWindowsLastReplaced($windowsLastReplaced)
    {
        $this->windowsLastReplaced = $windowsLastReplaced;

        return $this;
    }

    /**
     * Get windowsLastReplaced
     *
     * @return string
     */
    public function getWindowsLastReplaced()
    {
        return $this->windowsLastReplaced;
    }

    /**
     * Set sidesQty
     *
     * @param integer $sidesQty
     * @return Appointment
     */
    public function setSidesQty($sidesQty)
    {
        $this->sidesQty = $sidesQty;

        return $this;
    }

    /**
     * Get sidesQty
     *
     * @return integer
     */
    public function getSidesQty()
    {
        return $this->sidesQty;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Appointment
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
     * Get canceled
     *
     * @return boolean
     */
    public function isCanceled()
    {
        return $this->status = 'canceled';
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Appointment
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return Appointment
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Set office
     *
     * @param App\Lev\CRMBundle\Entity\Office $office
     * @return Appointment
     */
    public function setOffice(\App\Lev\CRMBundle\Entity\Office $office = null)
    {
        $this->office = $office;

        return $this;
    }

    /**
     * Get office
     *
     * @return \Lev\CRMBundle\Entity\Office
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * Set customer
     *
     * @param App\Lev\CRMBundle\Entity\Customer $customer
     * @return Appointment
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
     * Set cancelReason
     *
     * @param string $cancelReason
     * @return Appointment
     */
    public function setCancelReason($cancelReason)
    {
        $this->cancelReason = $cancelReason === '' ? null : $cancelReason;

        return $this;
    }

    /**
     * Get cancelReason
     *
     * @return string
     */
    public function getCancelReason()
    {
        return $this->cancelReason;
    }

    /**
     * Determining if appointment was demoed
     * If status is anything other than no-pitch AND past scheduled date/time.
     *
     * @return bool
     */
    public function isDemoed()
    {
        $now = new \DateTime();

        return $this->getStatus() === 'sold' || null !== $this->getPitchMissReason();
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Appointment
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
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatePersistRecord()
    {
        $this->setOffice($this->getCustomer()->getOffice());

        $status = $this->getStatus();

        if (null === $status) {
            $status = 'pending';
        }

        if ($status === 'pending' && null !== $this->getDatetime()) {
            $status = 'scheduled';
            $callback = $this->getDatetime();
            $callback->sub(new \DateInterval('PT3H'));
            $this->setCallback($callback);
        }

        // status = scheduled/confirmed` but `datetime === NULL or salesRep === NULL`, so set `status = pending
        if (null === $this->getCallback() && null === $this->getDatetime()) {
            $this->setCallback(new \Datetime());
        }

        if (null === $this->getCallback() && null !== $this->getDatetime()) {
            $this->setCallback($this->getDatetime());
        }

        // REHASHING
        $limit = new \Datetime();
        $limit->sub(new \DateInterval('PT1H'));
        if (
            $status === 'confirmed'
            && !$this->getCustomer()->getTcpa()
            && !$this->getCustomer()->getWrongNumber()
            && null !== $this->getDatetime()
            && $this->getDatetime() <= $limit
        ) {
          $status === 'pitch-miss';
        }

        //  _DONT_SHOW_QUERY AND appointment was canceled_
        if (
            !$this->getCustomer()->getTcpa()
            && !$this->getCustomer()->getWrongNumber()
            && $status === 'canceled'
        ) {
          $status === 'no-pitch';
        }

        // _DONT_SHOW_QUERY AND If a day has passed and no sales rep is set,
        //    the call should go to reset._
        //    (TODO if salesRep is mandatory when datetime is set, this rule is USELESS)
        $todayStart = new \Datetime();
        $todayStart->setTime(0, 0);
        if (
            !$this->getCustomer()->getTcpa()
            && !$this->getCustomer()->getWrongNumber()
            && $status === 'pending'
            && null === $this->getSalesRep()
            && null !== $this->getDatetime()
            && $this->getDatetime() < $todayStart
        ) {
          $status === 'no-pitch';
        }

        // _DONT_SHOW_QUERY AND No matter what, only TODAY's calls should be in
        // scheduling and confirming. Everything else will either be in rehash,
        // reset, or unresulted._
        if (
            in_array($status, array('pending', 'scheduled', 'confirmed'))
            && !$this->getCustomer()->getTcpa()
            && !$this->getCustomer()->getWrongNumber()
            && (
                (null === $this->getDatetime() && $this->getCallback() < $todayStart)
                || (null !== $this->getDatetime() && $this->getDatetime() < $todayStart)
            )
        ) {
          $status === 'no-pitch';
        }

        if ($status !== 'canceled' && null !== $this->getSale()) {
            $status = 'sold';
        }

        $this->setStatus($status);

        if (null === $this->getConfirmedSalesRep()) {
            $this->setConfirmedSalesRep(false);
        }

        // Job Ceiling
        $jobCeiling = 0;
        /** @var $saleProduct \App\Lev\CRMBundle\Entity\SaleProduct */
        foreach ($this->getProducts() as $saleProduct)
        {
            $jobCeiling += $saleProduct->getCost();
        }
        $this->setJobCeiling($jobCeiling);

        return $this;
    }

    /**
     * Set sale
     *
     * @param App\Lev\CRMBundle\Entity\Sale $sale
     * @return Appointment
     */
    public function setSale(\App\Lev\CRMBundle\Entity\Sale $sale = null)
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
     * Constructor
     */
    public function __construct()
    {
        $this->products    = new \Doctrine\Common\Collections\ArrayCollection();
        $this->histories   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->calls = new ArrayCollection();
        $this->callRecords = new ArrayCollection();
         $this->createdAt = new \DateTime();
    }

    /**
     * Add product
     *
     * @param \App\Lev\CRMBundle\Entity\AppointmentProduct $product
     *
     * @return Appointment
     */
    public function addProduct(\App\Lev\CRMBundle\Entity\AppointmentProduct $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \App\Lev\CRMBundle\Entity\AppointmentProduct $product
     */
    public function removeProduct(\App\Lev\CRMBundle\Entity\AppointmentProduct $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * Set salesRep
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $salesRep
     *
     * @return Appointment
     */
    public function setSalesRep(\App\Lev\CRMBundle\Entity\Staff $salesRep = null)
    {
        $this->salesRep = $salesRep;

        return $this;
    }

    /**
     * Get salesRep
     *
     * @return App\Lev\CRMBundle\Entity\Staff
     */
    public function getSalesRep()
    {
        return $this->salesRep;
    }


    /**
     * Add history
     *
     * @param \App\Lev\CRMBundle\Entity\History $history
     *
     * @return Appointment
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
     * Set addressStreet
     *
     * @param string $addressStreet
     *
     * @return Appointment
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;

        return $this;
    }

    /**
     * Get addressStreet
     *
     * @return string
     */
    public function getAddressStreet()
    {
        return $this->addressStreet;
    }

    /**
     * Set addressCity
     *
     * @param string $addressCity
     *
     * @return Appointment
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;

        return $this;
    }

    /**
     * Get addressCity
     *
     * @return string
     */
    public function getAddressCity()
    {
        return $this->addressCity;
    }

    /**
     * Set addressState
     *
     * @param string $addressState
     *
     * @return Appointment
     */
    public function setAddressState($addressState)
    {
        $this->addressState = $addressState;

        return $this;
    }

    /**
     * Get addressState
     *
     * @return string
     */
    public function getAddressState()
    {
        return $this->addressState;
    }

    /**
     * Set addressZip
     *
     * @param string $addressZip
     *
     * @return Appointment
     */
    public function setAddressZip($addressZip)
    {
        $this->addressZip = $addressZip;

        return $this;
    }

    /**
     * Get addressZip
     *
     * @return string
     */
    public function getAddressZip()
    {
        return $this->addressZip;
    }

    /**
     * Set addressLat
     *
     * @param string $addressLat
     *
     * @return Appointment
     */
    public function setAddressLat($addressLat)
    {
        $this->addressLat = $addressLat;

        return $this;
    }

    /**
     * Get addressLat
     *
     * @return string
     */
    public function getAddressLat()
    {
        return $this->addressLat;
    }

    /**
     * Set addressLng
     *
     * @param string $addressLng
     *
     * @return Appointment
     */
    public function setAddressLng($addressLng)
    {
        $this->addressLng = $addressLng;

        return $this;
    }

    /**
     * Get addressLng
     *
     * @return string
     */
    public function getAddressLng()
    {
        return $this->addressLng;
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

    public function getCostMap()
    {
        // The total ceiling cost for all products.
        $ceilingCost = 0;

        /** @var $saleProduct \App\Lev\CRMBundle\Entity\AppointmentProduct */
        foreach ($this->getProducts() as $itemProduct)
        {
            $ceilingCost += $itemProduct->getCost();
        }

        $financingPct = $this->isFinancing() ? 1.025 : 1;

        // The discount percentage based on the selected value.
        if ($this->getDiscountMethod() === 'other' && null !== $this->getDiscountPercentage()) {
            $discountMultiplier = 1 - $this->getDiscountPercentage() / 100;
        } else {
          $discountMultiplier = array_key_exists($this->getDiscountMethod(), Sale::$discountMultiplier)
                              ? Sale::$discountMultiplier[$this->getDiscountMethod()]
                              : 1;
        }

        // The cost for the total discount.
        $costWithDiscount = $ceilingCost * $discountMultiplier;

        // The extra cost for the sales tax.
        // (pct / 100) * amount
        $salesTaxCost = $costWithDiscount * ($this->getSalesTax() / 100);

        // Cost of the finance fee.
        $financeCost = $this->isFinancing() ? $costWithDiscount *($financingPct / 100) : 0;

        // Everything included.
        $finalTotal = ($costWithDiscount + $salesTaxCost) * $financingPct;

        return array(
          'financingCost' => $financeCost,
          'financingPct'  => $this->isFinancing() ? ($financingPct - 1) * 100 : 0,
          'downPayment'   => $this->getDownPayment(),
          'beforeTaxCost' => $costWithDiscount,
          'salesTaxPct'   => $this->getSalesTax(),
          'salesTaxCost'  => $salesTaxCost,
          'total'         => $finalTotal,
        );

    }

    public function toArray()
    {
        $products = array();
        foreach ($this->getProducts() as $itemProduct) {
            $products[] = $itemProduct->toArray();
        }

        $attachments = array();
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = $attachment->toArrayShort();
        }

        return array(
            'id'                  => $this->getId(),
            'name'                => $this->getName(),
            'productInterest'     => $this->getProductInterest(),
            'roofAge'             => $this->getRoofAge(),
            'windowsQty'          => $this->getWindowsQty(),
            'windowsLastReplaced' => $this->getWindowsLastReplaced(),
            'sidesQty'            => $this->getSidesQty(),
            'sidingAge'           => $this->getSidingAge(),
            'type'                => $this->getType(),
            'notes'               => $this->getNotes(),
            'datetime'            => $this->getDatetime(),
            'office'              => $this->getOffice()->toArray(),
            'customer'            => $this->getCustomer()->toArrayShortToCall(),
            'status'              => $this->getStatus(),
            'cancelReason'        => $this->getCancelReason(),
            'sale'                => $this->getSale() ? $this->getSale()->toArrayShort() : null,
            'saleID'              => $this->getSale() ? $this->getSale()->getId() : null,
            'salesRep'            => $this->getSalesRep() ? $this->getSalesRep()->toArrayShort() : null,
            'marketingRep'        => $this->getMarketingRep() ? $this->getMarketingRep()->toArrayShort() : null,
            'marketerSource'      => $this->getMarketerSource(),
            'addressStreet'       => $this->getAddressStreet(),
            'addressCity'         => $this->getAddressCity(),
            'addressState'        => $this->getAddressState(),
            'addressZip'          => $this->getAddressZip(),
            'addressLat'          => $this->getAddressLat(),
            'addressLng'          => $this->getAddressLng(),
            'products'            => $products,
            'attachments'         => $attachments,
            'demoed'              => $this->isDemoed(),
            'createdAt'           => $this->getCreatedAt(),
            'createdBy'           => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'resetBy'             => $this->getResetBy() ? $this->getResetBy()->toArrayShort() : null,
            'callcenterRep'       => $this->getResetBy() ? $this->getCallcenterRep()->toArrayShort() : null,
            'callback'            => $this->getCallback(),
            'confirmedSalesRep'   => $this->getConfirmedSalesRep(),
            'noPitchReason'       => $this->getNoPitchReason(),
            'pitchMissReason'     => $this->getPitchMissReason(),
            'salesTax'                      => $this->getSalesTax(),
            'paymentType'                   => $this->getPaymentType(),
            'discountPercentage'            => $this->getDiscountPercentage(),
            'financing'                     => $this->isFinancing(),
            'discountMethod'                => $this->getDiscountMethod(),
            'downPayment'                   => $this->getDownPayment(),
            'contractSignDate'              => $this->getContractSignDate(),
            'contractCancellationDueDate'   => $this->getContractCancellationDueDate(),
            'contractDeliveryAddress'       => $this->getContractDeliveryAddress(),
            'contractRecipientEmailAddress' => $this->getContractRecipientEmailAddress(),
            'contractCustomerSignature1'    => $this->getContractCustomerSignature1(),
            'contractCustomerSignature2'    => $this->getContractCustomerSignature2(),
            'lockedBy'                      => $this->getLockedBy() ? $this->getLockedBy()->toArrayShort() : null,
            'dueBy'                         => $this->getDueBy(),
            'creditQualityValue'            => $this->getCreditQualityValue(),
            'isdeleted'             => $this->getDeleted(),
        );
    }


    public function toArrayCallcenter()
    {
        $appointments = array(
            'id'                  => $this->getId(),
            'name'                => $this->getName(),
            'productInterest'     => $this->getProductInterest(),
            'roofAge'             => $this->getRoofAge(),
            'windowsQty'          => $this->getWindowsQty(),
            'windowsLastReplaced' => $this->getWindowsLastReplaced(),
            'sidesQty'            => $this->getSidesQty(),
            'sidingAge'           => $this->getSidingAge(),
            'type'                => $this->getType(),
            'notes'               => $this->getNotes(),
            'datetime'            => $this->getDatetime(),
            'office'              => $this->getOffice()->toArray(),
            'customer'            => $this->getCustomer()->toArrayShortToCall(),
            'status'              => $this->getStatus(),
            'cancelReason'        => $this->getCancelReason(),
            'salesRep'            => $this->getSalesRep() ? $this->getSalesRep()->toArrayShort() : null,
            'marketingRep'        => $this->getMarketingRep() ? $this->getMarketingRep()->toArrayShort() : null,
            'marketerSource'      => $this->getMarketerSource(),
            'addressStreet'       => $this->getAddressStreet(),
            'addressCity'         => $this->getAddressCity(),
            'addressState'        => $this->getAddressState(),
            'addressZip'          => $this->getAddressZip(),
            'addressLat'          => $this->getAddressLat(),
            'addressLng'          => $this->getAddressLng(),
            'demoed'              => $this->isDemoed(),
            'createdAt'           => $this->getCreatedAt(),
            'createdBy'           => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'resetBy'             => $this->getResetBy() ? $this->getResetBy()->toArrayShort() : null,
            'callcenterRep'       => $this->getCallcenterRep() ? $this->getCallcenterRep()->toArrayShort() : null,
            'callback'            => $this->getCallback(),
            'confirmedSalesRep'   => $this->getConfirmedSalesRep(),
            'noPitchReason'       => $this->getNoPitchReason(),
            'pitchMissReason'     => $this->getPitchMissReason(),
            'salesTax'                      => $this->getSalesTax(),
            'paymentType'                   => $this->getPaymentType(),
            'discountPercentage'            => $this->getDiscountPercentage(),
            'financing'                     => $this->isFinancing(),
            'discountMethod'                => $this->getDiscountMethod(),
            'downPayment'                   => $this->getDownPayment(),
            'contractSignDate'              => $this->getContractSignDate(),
            'contractCancellationDueDate'   => $this->getContractCancellationDueDate(),
            'contractDeliveryAddress'       => $this->getContractDeliveryAddress(),
            'contractRecipientEmailAddress' => $this->getContractRecipientEmailAddress(),
            'contractCustomerSignature1'    => $this->getContractCustomerSignature1(),
            'contractCustomerSignature2'    => $this->getContractCustomerSignature2(),
            'lockedBy'                      => $this->getLockedBy() ? $this->getLockedBy()->toArrayShort() : null,
            'dueBy'                         => $this->getDueBy(),
            'creditQualityValue'            => $this->getCreditQualityValue(),
        );

        return $appointments;
    }

    public function toArrayShort()
    {
        return array(
            'id'              => $this->getId(),
            'name'            => $this->getName(),
            'datetime'        => $this->getDatetime(),
            'productInterest' => $this->getProductInterest(),
            'addressCity'     => $this->getAddressCity(),
            'addressState'    => $this->getAddressState(),
            'status'          => $this->getStatus(),
            'salesRep'        => $this->getSalesRep() ?  $this->getSalesRep()->toArrayShort() : null,
            'callback'        => $this->getCallback(),
        );

    }

    public function toArrayShortToSale()
    {
        return array(
            'id'       => $this->getId(),
            'salesRep' => $this->getSalesRep() ?  $this->getSalesRep()->toArrayShort() : null,
            'name'     => $this->getName(),
            'status'   => $this->getStatus(),
        );

    }

    /**
     * Get canceled
     *
     * @return boolean
     */
    public function getCanceled()
    {
        return $this->canceled;
    }

    /**
     * Add attachment
     *
     * @param \App\Lev\CRMBundle\Entity\Attachment $attachment
     *
     * @return Appointment
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
     * Set marketingRep
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $marketingRep
     *
     * @return Appointment
     */
    public function setMarketingRep(\App\Lev\CRMBundle\Entity\Staff $marketingRep = null)
    {
        $this->marketingRep = $marketingRep;

        return $this;
    }

    /**
     * Get marketingRep
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getMarketingRep()
    {
        return $this->marketingRep;
    }

    /**
     * Set salesforceId
     *
     * @param string $salesforceId
     *
     * @return Appointment
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
     * Set callback
     *
     * @param \DateTime $callback
     *
     * @return Appointment
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
     * @return Appointment
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
     * @return Appointment
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
     * Set lockedBy
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $lockedBy
     *
     * @return Appointment
     */
    public function setLockedBy(\App\Lev\CRMBundle\Entity\Staff $lockedBy = null)
    {
        $this->lockedBy = $lockedBy;

        return $this;
    }

    /**
     * Get lockedBy
     *
     * @return App\Lev\CRMBundle\Entity\Staff
     */
    public function getLockedBy()
    {
        return $this->lockedBy;
    }

    /**
     * Set sidingAge
     *
     * @param string $sidingAge
     *
     * @return Appointment
     */
    public function setSidingAge($sidingAge)
    {
        $this->sidingAge = $sidingAge;

        return $this;
    }

    /**
     * Get sidingAge
     *
     * @return string
     */
    public function getSidingAge()
    {
        return $this->sidingAge;
    }

    /**
     * Get Google Maps Search
     * @return string
     */
    public function getGoogleMapsSearch()
    {
        $address = $this->getAddressStreet()
                 . ', ' . $this->getAddressCity()
                 . ', ' . $this->getAddressState()
                 . ', ' . $this->getAddressZip()
                 . ',+USA'
                 . ',' . $this->getAddressLat()
                 . ',' . $this->getAddressLng();
        return str_replace(' ', '+', $address);
    }

    /**
     * Get Calls Count By Outcome
     * @param  string $outcome
     * @return integer
     */
    public function getCallsCountByOutcome($outcome)
    {
        $total = 0;

        foreach ($this->getCalls() as $call) {
            if ($call->getOutcome() === $outcome) {
                $total++;
            }
        }

        return $total;
    }

    /**
     * Add call
     *
     * @param \App\Lev\CRMBundle\Entity\History $call
     *
     * @return Appointment
     */
    public function addCall(\App\Lev\CRMBundle\Entity\History $call)
    {
        $this->calls[] = $call;

        return $this;
    }

    /**
     * Remove call
     *
     * @param \App\Lev\CRMBundle\Entity\History $call
     */
    public function removeCall(\App\Lev\CRMBundle\Entity\History $call)
    {
        $this->calls->removeElement($call);
    }

    /**
     * Get calls
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalls()
    {
        return $this->calls;
    }

    /**
     * Set confirmedSalesRep
     *
     * @param boolean $confirmedSalesRep
     *
     * @return Appointment
     */
    public function setConfirmedSalesRep($confirmedSalesRep)
    {
        $this->confirmedSalesRep = $confirmedSalesRep;

        return $this;
    }

    /**
     * Get confirmedSalesRep
     *
     * @return boolean
     */
    public function getConfirmedSalesRep()
    {
        return $this->confirmedSalesRep;
    }

    /**
     * Get confirmedSalesRep
     *
     * @return boolean
     */
    public function isConfirmedSalesRep()
    {
        return (boolean) $this->confirmedSalesRep;
    }


    /**
     * Set marketerSource
     *
     * @param string $marketerSource
     *
     * @return Appointment
     */
    public function setMarketerSource($marketerSource)
    {
        $this->marketerSource = $marketerSource;

        return $this;
    }

    /**
     * Get marketerSource
     *
     * @return string
     */
    public function getMarketerSource()
    {
        return $this->marketerSource;
    }

    /**
     * Add callRecord
     *
     * @param \App\Lev\CRMBundle\Entity\CallRecord $callRecord
     *
     * @return Appointment
     */
    public function addCallRecord(\App\Lev\CRMBundle\Entity\CallRecord $callRecord)
    {
        $this->callRecords[] = $callRecord;

        return $this;
    }

    /**
     * Remove callRecord
     *
     * @param \App\Lev\CRMBundle\Entity\CallRecord $callRecord
     */
    public function removeCallRecord(\App\Lev\CRMBundle\Entity\CallRecord $callRecord)
    {
        $this->callRecords->removeElement($callRecord);
    }

    /**
     * Get callRecords
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCallRecords()
    {
        return $this->callRecords;
    }

    /**
     * Set resetBy
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $resetBy
     *
     * @return Appointment
     */
    public function setResetBy(\App\Lev\CRMBundle\Entity\Staff $resetBy = null)
    {
        $this->resetBy = $resetBy;

        return $this;
    }

    /**
     * Get resetBy
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getResetBy()
    {
        return $this->resetBy;
    }

    /**
     * Set callcenterRep
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $callcenterRep
     *
     * @return Appointment
     */
    public function setCallcenterRep(\App\Lev\CRMBundle\Entity\Staff $callcenterRep = null)
    {
        $this->callcenterRep = $callcenterRep;

        return $this;
    }

    /**
     * Get callcenterRep
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getCallcenterRep()
    {
        return $this->callcenterRep;
    }

    /**
     * Set pitchMissReason
     *
     * @param string $pitchMissReason
     *
     * @return Appointment
     */
    public function setPitchMissReason($pitchMissReason)
    {
        $this->pitchMissReason = $pitchMissReason === '' ? null : $pitchMissReason;

        return $this;
    }

    /**
     * Get pitchMissReason
     *
     * @return string
     */
    public function getPitchMissReason()
    {
        return $this->pitchMissReason;
    }

    /**
     * Set noPitchReason
     *
     * @param string $noPitchReason
     *
     * @return Appointment
     */
    public function setNoPitchReason($noPitchReason)
    {
        $this->noPitchReason = $noPitchReason === '' ? null : $noPitchReason;

        return $this;
    }

    /**
     * Get noPitchReason
     *
     * @return string
     */
    public function getNoPitchReason()
    {
        return $this->noPitchReason;
    }


    /**
     * Set paymentType
     *
     * @param string $paymentType
     *
     * @return Appointment
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set discountMethod
     *
     * @param string $discountMethod
     *
     * @return Appointment
     */
    public function setDiscountMethod($discountMethod)
    {
        $this->discountMethod = $discountMethod;

        return $this;
    }

    /**
     * Get discountMethod
     *
     * @return string
     */
    public function getDiscountMethod()
    {
        return $this->discountMethod;
    }

    /**
     * Is financing
     *
     * @return boolean
     */
    public function isFinancing()
    {
        return $this->getPaymentType() === 'financing';
    }

    /**
     * Set discountPercentage
     *
     * @param string $discountPercentage
     *
     * @return Appointment
     */
    public function setDiscountPercentage($discountPercentage)
    {
        $this->discountPercentage = $discountPercentage;

        return $this;
    }

    /**
     * Get discountPercentage
     *
     * @return string
     */
    public function getDiscountPercentage()
    {
        return $this->discountPercentage;
    }

    /**
     * Set downPayment
     *
     * @param float $downPayment
     *
     * @return Appointment
     */
    public function setDownPayment($downPayment)
    {
        $this->downPayment = $downPayment;

        return $this;
    }

    /**
     * Get downPayment
     *
     * @return float
     */
    public function getDownPayment()
    {
        return $this->downPayment;
    }

    /**
     * Set contractSignDate
     *
     * @param \DateTime $contractSignDate
     *
     * @return Appointment
     */
    public function setContractSignDate($contractSignDate)
    {
        $this->contractSignDate = $contractSignDate;

        return $this;
    }

    /**
     * Get contractSignDate
     *
     * @return \DateTime
     */
    public function getContractSignDate()
    {
        return $this->contractSignDate;
    }

    /**
     * Set contractCancellationDueDate
     *
     * @param \DateTime $contractCancellationDueDate
     *
     * @return Appointment
     */
    public function setContractCancellationDueDate($contractCancellationDueDate)
    {
        $this->contractCancellationDueDate = $contractCancellationDueDate;

        return $this;
    }

    /**
     * Get contractCancellationDueDate
     *
     * @return \DateTime
     */
    public function getContractCancellationDueDate()
    {
        return $this->contractCancellationDueDate;
    }

    /**
     * Set contractDeliveryAddress
     *
     * @param string $contractDeliveryAddress
     *
     * @return Appointment
     */
    public function setContractDeliveryAddress($contractDeliveryAddress)
    {
        $this->contractDeliveryAddress = $contractDeliveryAddress;

        return $this;
    }

    /**
     * Get contractDeliveryAddress
     *
     * @return string
     */
    public function getContractDeliveryAddress()
    {
        return $this->contractDeliveryAddress;
    }

    /**
     * Set contractCustomerSignature1
     *
     * @param string $contractCustomerSignature1
     *
     * @return Appointment
     */
    public function setContractCustomerSignature1($contractCustomerSignature1)
    {
        $this->contractCustomerSignature1 = $contractCustomerSignature1;

        return $this;
    }

    /**
     * Get contractCustomerSignature1
     *
     * @return string
     */
    public function getContractCustomerSignature1()
    {
        return $this->contractCustomerSignature1;
    }

    /**
     * Set contractCustomerSignature2
     *
     * @param string $contractCustomerSignature2
     *
     * @return Appointment
     */
    public function setContractCustomerSignature2($contractCustomerSignature2)
    {
        $this->contractCustomerSignature2 = $contractCustomerSignature2;

        return $this;
    }

    /**
     * Get contractCustomerSignature2
     *
     * @return string
     */
    public function getContractCustomerSignature2()
    {
        return $this->contractCustomerSignature2;
    }

    /**
     * Set contractRecipientEmailAddress
     *
     * @param string $contractRecipientEmailAddress
     *
     * @return Appointment
     */
    public function setContractRecipientEmailAddress($contractRecipientEmailAddress)
    {
        $this->contractRecipientEmailAddress = $contractRecipientEmailAddress;

        return $this;
    }

    /**
     * Get contractRecipientEmailAddress
     *
     * @return string
     */
    public function getContractRecipientEmailAddress()
    {
        return $this->contractRecipientEmailAddress;
    }

    /**
     * Set salesTax
     *
     * @param string $salesTax
     *
     * @return Appointment
     */
    public function setSalesTax($salesTax)
    {
        $this->salesTax = $salesTax;

        return $this;
    }

    /**
     * Get salesTax
     *
     * @return string
     */
    public function getSalesTax()
    {
        return $this->salesTax;
    }

    /**
     * Set creditQualityValue
     *
     * @param integer $creditQualityValue
     *
     * @return Appointment
     */
    public function setCreditQualityValue($creditQualityValue)
    {
        $this->creditQualityValue = $creditQualityValue;

        return $this;
    }

    /**
     * Get creditQualityValue
     *
     * @return integer
     */
    public function getCreditQualityValue()
    {
        return $this->creditQualityValue;
    }

    /**
     * Get Real Status
     * @return string
     */
    public function getRealStatus()
    {
        $status = $this->getStatus();
        $now = new \Datetime();
        if (
            $status === 'confirmed'
            && null !== $this->getDatetime()
            && $this->getDatetime() > $now
        ) {
            $status = 'in-process';
        }

        return $status;
    }

    /**
     * Get Payment Type Formated
     * @return string
     */
    public function getPaymentTypeFormated()
    {
        return array_key_exists($this->getPaymentType(), self::$paymentTypeLabels)
            ? self::$paymentTypeLabels[$this->getPaymentType()]
            : '';
    }

    /**
     * Set jobCeiling
     *
     * @param string $jobCeiling
     *
     * @return Appointment
     */
    public function setJobCeiling($jobCeiling)
    {
        $this->jobCeiling = $jobCeiling;

        return $this;
    }

    /**
     * Get jobCeiling
     *
     * @return string
     */
    public function getJobCeiling()
    {
        return $this->jobCeiling;
    }
    
    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Appointment
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
     * Add histories
     *
     * @param \App\Lev\CRMBundle\Entity\History $histories
     * @return Appointment
     */
    public function addHistorie(\App\Lev\CRMBundle\Entity\History $histories)
    {
        $this->histories[] = $histories;

        return $this;
    }

    /**
     * Remove histories
     *
     * @param \App\Lev\CRMBundle\Entity\History $histories
     */
    public function removeHistorie(\App\Lev\CRMBundle\Entity\History $histories)
    {
        $this->histories->removeElement($histories);
    }

    /**
     * Set createdBy
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $createdBy
     * @return Appointment
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

      public function getDeleted()
    {
        return $this->isdeleted;
    }
    public function setDeleted($isdeleted)
    {
        $this->isdeleted = $isdeleted;
    }
}
