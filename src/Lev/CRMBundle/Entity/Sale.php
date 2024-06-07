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
 * @ORM\Table(name="sale")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Sale
{
    use Traits\BlameableCreateEntity;

    public static $discountMultiplier = array(
        'daily-price'    => 1,
        'year-price'     => 1.2,
        'marketing-home' => 0.92,
        'flex-install'   => 0.84,
        'family-friends' => 0.80,
    );

    public static $financingMultiplier = 1.025;

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
     * @ORM\OneToOne(targetEntity="App\Lev\CRMBundle\Entity\Appointment", inversedBy="sale")
     * @ORM\JoinColumn(name="appointment_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Appointment is required")
     **/
    protected $appointment;

    /**
     * @ORM\OneToOne(targetEntity="App\Lev\CRMBundle\Entity\Project", mappedBy="sale", cascade={"persist", "remove"})
     **/
    protected $project;

    /**
     * @ORM\Column(name="sales_tax", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $salesTax;

    /**
     * @ORM\Column(name="amount_due", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $amountDue;

    /**
     * @ORM\Column(name="amount_owned", type="string", length=200, nullable=true)
     */
    protected $amountOwned;

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
     * @ORM\Column(name="discount", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $discount;

    /**
     * @ORM\Column(name="discount_percentage", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $discountPercentage;

    /**
     * @ORM\Column(name="job_ceiling", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $jobCeiling;

    /**
     * @ORM\Column(name="sold_price", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $soldPrice;

    /**
     * @ORM\Column(name="job_ceiling_override", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $jobCeilingOverride;

    /**
     * @ORM\Column(name="sold_price_override", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $soldPriceOverride;

    /**
     * @ORM\Column(name="notes", type="text", nullable=true)
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}")
     */
    protected $notes;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\SaleProduct", mappedBy="sale", cascade={"persist", "remove"})
     */
    protected $products;

    /**
     * @ORM\Column(name="status", type="string", length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "approved", "on-hold", "completed", "declined", "canceled"},
     *   message = "Invalid status {{ value }}."
     * )
     * @Assert\Length(max=10, maxMessage="Status length must be 10 chars max.")
     */
    protected $status;

    /**
     * @ORM\Column(name="sold_on_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid Sold on Date")
     */
    protected $soldOnDate;

    /**
     * @ORM\Column(name="net_on_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid Net on Date")
     */
    protected $netOnDate;

    /**
     * @ORM\Column(name="paid_date", type="date", nullable=true)
     * @Assert\Date(message="Invalid Paid Date")
     */
    protected $paidDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\History", mappedBy="sale", cascade={"persist", "remove"})
     */
    protected $histories;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Attachment", mappedBy="sale", cascade={"persist", "remove"})
     */
    protected $attachments;

    /**
     * @ORM\Column(name="salesforce_id", type="string", length=20, nullable=true)
     */
    protected $salesforceId;

    /**
     * @ORM\Column(name="down_payment", type="float", nullable=true)
     */
    protected $downPayment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->products    = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set salesTax
     *
     * @param string $salesTax
     *
     * @return Sale
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
     * Set amountDue
     *
     * @param string $amountDue
     *
     * @return Sale
     */
    public function setAmountDue($amountDue)
    {
        $this->amountDue = $amountDue;

        return $this;
    }

    /**
     * Get amountDue
     *
     * @return string
     */
    public function getAmountDue()
    {
        return $this->amountDue;
    }

    /**
     * Set amountOwned
     *
     * @param string $amountOwned
     *
     * @return Sale
     */
    public function setAmountOwned($amountOwned)
    {
        $this->amountOwned = $amountOwned;

        return $this;
    }

    /**
     * Get amountOwned
     *
     * @return string
     */
    public function getAmountOwned()
    {
        return $this->amountOwned;
    }

    /**
     * Set paymentType
     *
     * @param string $paymentType
     *
     * @return Sale
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
     * Set discount
     *
     * @param string $discount
     *
     * @return Sale
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set jobCeiling
     *
     * @param string $jobCeiling
     *
     * @return Sale
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
     * Set notes
     *
     * @param string $notes
     *
     * @return Sale
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
     * Set status
     *
     * @param string $status
     *
     * @return Sale
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
     * Set soldOnDate
     *
     * @param \DateTime $soldOnDate
     *
     * @return Sale
     */
    public function setSoldOnDate($soldOnDate)
    {
        $this->soldOnDate = $soldOnDate;

        return $this;
    }

    /**
     * Get soldOnDate
     *
     * @return \DateTime
     */
    public function getSoldOnDate()
    {
        return $this->soldOnDate;
    }

    /**
     * Set netOnDate
     *
     * @param \DateTime $netOnDate
     *
     * @return Sale
     */
    public function setNetOnDate($netOnDate)
    {
        $this->netOnDate = $netOnDate;

        return $this;
    }

    /**
     * Get netOnDate
     *
     * @return \DateTime
     */
    public function getNetOnDate()
    {
        return $this->netOnDate;
    }

    /**
     * Set paidDate
     *
     * @param \DateTime $paidDate
     *
     * @return Sale
     */
    public function setPaidDate($paidDate)
    {
        $this->paidDate = $paidDate;

        return $this;
    }

    /**
     * Get paidDate
     *
     * @return \DateTime
     */
    public function getPaidDate()
    {
        return $this->paidDate;
    }

    /**
     * Set office
     *
     * @param \App\Lev\CRMBundle\Entity\Office $office
     *
     * @return Sale
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
     * Set customer
     *
     * @param \App\Lev\CRMBundle\Entity\Customer $customer
     *
     * @return Sale
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
     * Set appointment
     *
     * @param \App\Lev\CRMBundle\Entity\Appointment $appointment
     *
     * @return Sale
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
     * Add product
     *
     * @param \App\Lev\CRMBundle\Entity\SaleProduct $product
     *
     * @return Sale
     */
    public function addProduct(\App\Lev\CRMBundle\Entity\SaleProduct $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \App\Lev\CRMBundle\Entity\SaleProduct $product
     */
    public function removeProduct(\App\Lev\CRMBundle\Entity\SaleProduct $product)
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

    public function toArray()
    {
        $products = array();
        foreach ($this->getProducts() as $itemProduct) {
            $products[] = $itemProduct->toArray();
        }

        $histories = array();
        foreach($this->getHistories() as $history) {
            $histories[] = $history->toArray();
        }

        $attachments = array();
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = $attachment->toArrayShort();
        }

        return array(
            'id'                            => $this->getId(),
            'appointment'                   => $this->getAppointment()->toArrayShortToSale(),
            'soldPrice'                     => $this->getSoldPrice(),
            'soldPercentage'                => $this->getSoldPercentage(),
            'salesTax'                      => $this->getSalesTax(),
            'amountDue'                     => $this->getAmountDue(),
            'amountOwned'                   => $this->getAmountOwned(),
            'paymentType'                   => $this->getPaymentType(),
            'discount'                      => $this->getDiscount(),
            'discountPercentage'            => $this->getDiscountPercentage(),
            'jobCeiling'                    => $this->getJobCeiling(),
            'soldPrice'                     => $this->getSoldPrice(),
            'jobCeilingOverride'            => $this->getJobCeilingOverride(),
            'soldPriceOverride'             => $this->getSoldPriceOverride(),
            'notes'                         => $this->getNotes(),
            'status'                        => $this->getStatus(),
            'soldOnDate'                    => $this->getSoldOnDate(),
            'netOnDate'                     => $this->getNetOnDate(),
            'paidDate'                      => $this->getPaidDate(),
            'createdAt'                     => $this->getCreatedAt(),
            'createdBy'                     => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'customer'                      => $this->getCustomer() ? $this->getCustomer()->toArrayShort() : null,
            'office'                        => $this->getOffice()->toArray(),
            'histories'                     => $histories,
            'attachments'                   => $attachments,
            'products'                      => $products,
            'projectId'                     => $this->getProject() ? $this->getProject()->getId() : null,
            'appointmentId'                 => $this->getAppointment()->getId(),
            'financing'                     => $this->isFinancing(),
            'discountMethod'                => $this->getDiscountMethod(),
            'downPayment'                   => $this->getDownPayment(),
        );
    }

    public function toArrayShort()
    {
        return array(
            'id'                            => $this->getId(),
            'soldPrice'                     => $this->getSoldPrice(),
            'soldPercentage'                => $this->getSoldPercentage(),
            'salesTax'                      => $this->getSalesTax(),
            'amountDue'                     => $this->getAmountDue(),
            'amountOwned'                   => $this->getAmountOwned(),
            'paymentType'                   => $this->getPaymentType(),
            'discount'                      => $this->getDiscount(),
            'discountPercentage'            => $this->getDiscountPercentage(),
            'jobCeiling'                    => $this->getJobCeiling(),
            'soldPrice'                     => $this->getSoldPrice(),
            'jobCeilingOverride'            => $this->getJobCeilingOverride(),
            'soldPriceOverride'             => $this->getSoldPriceOverride(),
            'status'                        => $this->getStatus(),
            'netOnDate'                     => $this->getNetOnDate(),
            'soldOnDate'                    => $this->getSoldOnDate(),
            'paidDate'                      => $this->getPaidDate(),
            'createdAt'                     => $this->getCreatedAt(),
            'createdBy'                     => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'appointmentId'                 => $this->getAppointment()->getId(),
            'projectId'                     => $this->getProject() ? $this->getProject()->getId() : null,
            'financing'                     => $this->isFinancing(),
            'discountMethod'                => $this->getDiscountMethod(),
            'downPayment'                   => $this->getDownPayment(),
        );
    }

    public function toArrayShortProject()
    {
        return array(
            'id'                            => $this->getId(),
            'soldPrice'                     => $this->getSoldPrice(),
            'soldPercentage'                => $this->getSoldPercentage(),
            'salesTax'                      => $this->getSalesTax(),
            'amountDue'                     => $this->getAmountDue(),
            'amountOwned'                   => $this->getAmountOwned(),
            'paymentType'                   => $this->getPaymentType(),
            'discount'                      => $this->getDiscount(),
            'discountPercentage'            => $this->getDiscountPercentage(),
            'jobCeiling'                    => $this->getJobCeiling(),
            'soldPrice'                     => $this->getSoldPrice(),
            'jobCeilingOverride'            => $this->getJobCeilingOverride(),
            'soldPriceOverride'             => $this->getSoldPriceOverride(),
            'status'                        => $this->getStatus(),
            'soldOnDate'                    => $this->getSoldOnDate(),
            'netOnDate'                     => $this->getNetOnDate(),
            'paidDate'                      => $this->getPaidDate(),
            'createdAt'                     => $this->getCreatedAt(),
            'createdBy'                     => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'financing'                     => $this->isFinancing(),
            'discountMethod'                => $this->getDiscountMethod(),
            'appointmentId'                 => $this->getAppointment()->getId(),
            'downPayment'                   => $this->getDownPayment(),
        );
    }

    /**
     * Add history
     *
     * @param \App\Lev\CRMBundle\Entity\History $history
     *
     * @return Sale
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
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatePersistRecord()
    {

        if ($this->getDiscountMethod() !== 'other') {
            $this->setDiscountPercentage(null);
        }

        $this->setCustomer($this->getAppointment()->getCustomer());
        $this->setOffice($this->getCustomer()->getOffice());

        // Job Ceiling - check override
        if (null !== $this->getJobCeilingOverride() && $this->getJobCeilingOverride() > 0) {

            $this->setJobCeiling($this->getJobCeilingOverride());

        } else {

          $jobCeiling = 0;
          /** @var $saleProduct \App\Lev\CRMBundle\Entity\SaleProduct */
          foreach ($this->getProducts() as $saleProduct)
          {
              $jobCeiling += $saleProduct->getCost();
          }
          $this->setJobCeiling($jobCeiling);

        }

        // Sold Price - check override
        if (null !== $this->getSoldPriceOverride() && $this->getSoldPriceOverride() > 0) {

            $this->setSoldPrice($this->getSoldPriceOverride());

        } else {

          $jobCeiling = $this->getJobCeiling();
          // Discount
          if ($this->getDiscountMethod() === 'other' && null !== $this->getDiscountPercentage()) {
              $discountMultiplier = 1 - floatval($this->getDiscountPercentage()) / 100;
          } else {
            $discountMultiplier = array_key_exists($this->getDiscountMethod(), self::$discountMultiplier)
                                ? self::$discountMultiplier[$this->getDiscountMethod()]
                                : 1;
          }
          // Saves discount with sign => (+) for add (year-price)
          //                          or (-) for sub (other discountMethods)
          $discount = -($jobCeiling - ($jobCeiling * $discountMultiplier));
          $this->setDiscount($discount);

          // WHY is SUM and not SUB: discount already has the sign (+ or -)
          $soldPrice = $jobCeiling + $discount;
          if ($this->isFinancing()) {
              $soldPrice = $soldPrice * self::$financingMultiplier;
          }
          $this->setSoldPrice($soldPrice);

        }

        if (null === $this->getStatus()) {
            $this->setStatus('on-hold');
        }

    }

    public function getCostMap()
    {
        // The total ceiling cost for all products.
        $ceilingCost = 0;

        /** @var $saleProduct \App\Lev\CRMBundle\Entity\SaleProduct */
        foreach ($this->getProducts() as $itemProduct)
        {
            $ceilingCost += $itemProduct->getCost();
        }

        $financingPct = $this->isFinancing() ? 1.025 : 1;

        // The discount percentage based on the selected value.
        if ($this->getDiscountMethod() === 'other' && null !== $this->getDiscountPercentage()) {
            $discountMultiplier = 1 - $this->getDiscountPercentage() / 100;
        } else {
          $discountMultiplier = array_key_exists($this->getDiscountMethod(), self::$discountMultiplier)
                              ? self::$discountMultiplier[$this->getDiscountMethod()]
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

    /**
     * Sold Price:  products total - discount - sales tax.
     * @return float
     */
//    public function getSoldPrice()
//    {
//        return $this->getSalesTax() > 0
//            ? $this->getJobCeiling() - $this->getDiscount() - $this->getJobCeiling() * $this->getSalesTax()/100
//            : $this->getJobCeiling() - $this->getDiscount();
//    }

    /**
     * Percentage sold based on the ceiling price.
     * @return float
     */
    public function getSoldPercentage()
    {
        return $this->getJobCeiling() > 0 ? round($this->getSoldPrice() / $this->getJobCeiling() * 100, 2) : 0;
    }

    /**
     * 75% of of the Job Ceiling.
     * @return float
     */
    protected function getJobFloor()
    {
        return round($this->getJobCeiling() * 0.75, 2);
    }

    /**
     * Set project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     *
     * @return Sale
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
     * Add attachment
     *
     * @param \App\Lev\CRMBundle\Entity\Attachment $attachment
     *
     * @return Sale
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
     * @return Sale
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
     * Set discountMethod
     *
     * @param string $discountMethod
     *
     * @return Sale
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
     * Set downPayment
     *
     * @param float $downPayment
     *
     * @return Sale
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
     * Set discountPercentage
     *
     * @param string $discountPercentage
     *
     * @return Sale
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
     * Set soldPrice
     *
     * @param string $soldPrice
     *
     * @return Sale
     */
    public function setSoldPrice($soldPrice)
    {
        $this->soldPrice = $soldPrice;

        return $this;
    }

    /**
     * Get soldPrice
     *
     * @return string
     */
    public function getSoldPrice()
    {
        return $this->soldPrice;
    }

    /**
     * Set jobCeilingOverride
     *
     * @param string $jobCeilingOverride
     *
     * @return Sale
     */
    public function setJobCeilingOverride($jobCeilingOverride)
    {
        $this->jobCeilingOverride = $jobCeilingOverride;

        return $this;
    }

    /**
     * Get jobCeilingOverride
     *
     * @return string
     */
    public function getJobCeilingOverride()
    {
        return $this->jobCeilingOverride;
    }

    /**
     * Set soldPriceOverride
     *
     * @param string $soldPriceOverride
     *
     * @return Sale
     */
    public function setSoldPriceOverride($soldPriceOverride)
    {
        $this->soldPriceOverride = $soldPriceOverride;

        return $this;
    }

    /**
     * Get soldPriceOverride
     *
     * @return string
     */
    public function getSoldPriceOverride()
    {
        return $this->soldPriceOverride;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Sale
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
     * @return Sale
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
     * @return Sale
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
}
