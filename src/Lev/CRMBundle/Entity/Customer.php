<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Customer Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="customer")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Customer
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Office is required")
     **/
    protected $office;

    /**
     * @ORM\Column(name="gid", type="string", length=12, nullable=true)
     * @Assert\Length(max=12, maxMessage="GID length must be 12 chars max.")
     */
    protected $gid;

    /**
     * @ORM\Column(name="primary_first_name", type="string",length=20)
     * @Assert\NotBlank(message="Primary first name is required")
     * @Assert\Length(max=20, maxMessage="Primary first name length must be 20 chars max.")
     */
    protected $primaryFirstName;

    /**
     * @ORM\Column(name="primary_last_name", type="string",length=40)
     * @Assert\NotBlank(message="Primary last name is required")
     * @Assert\Length(max=40, maxMessage="Primary last name length must be 20 chars max.")
     */
    protected $primaryLastName;

    /**
     * @ORM\Column(name="secondary_first_name", type="string",length=20, nullable=true)
     * @Assert\Length(max=20, maxMessage="Secondary first name length must be 20 chars max.")
     */
    protected $secondaryFirstName;

    /**
     * @ORM\Column(name="secondary_last_name", type="string",length=40, nullable=true)
     * @Assert\Length(max=40, maxMessage="Secondary last name length must be 40 chars max.")
     */
    protected $secondaryLastName;

    /**
     * @ORM\Column(name="secondary_relationship", type="string",length=20, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "spouse", "fiance", "significant-other", "parent", "sibling", "other"},
     *   message = "Invalid secondary relationship."
     * )
     * @Assert\Length(max=20, maxMessage="Secondary relashionship length must be 20 chars max.")
     */
    protected $secondaryRelationship;

    /**
     * @ORM\Column(name="home_year_built", type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $homeYearBuilt;

    /**
     * @ORM\Column(name="home_year_purchased", type="integer", nullable=true)
     * @Assert\Type(type="integer", message="The value {{ value }} is not a valid {{ type }}.")
     */
    protected $homeYearPurchased;

    /**
     * @ORM\Column(name="structure_type", type="string",length=20, nullable=true)
     * @Assert\Choice(
     *   choices = {"",
     *     "brick", "stone", "stucco", "frame", "condominium", "mobile-home",
     *     "modular", "duplex", "ranch", "split-level", "two-story", "town-house"
     *   },
     *   message = "Invalid structure type."
     * )
     * @Assert\Length(max=20, maxMessage="Structure type length must be 20 chars max.")
     */
    protected $structureType;

    /**
     * @ORM\Column(name="household_income", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "<20k", "20k-29k", "30k-69k", "70k-99k", "100k-149k", "150k-199k", "200k+"},
     *   message = "Invalid household income."
     * )
     * @Assert\Length(max=10, maxMessage="House income length must be 10 chars max.")
     */
    protected $householdIncome;

    /**
     * @ORM\Column(name="home_value", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "<50k", "50k-99k", "100k-199k", "200k-299k", "300k-399k", "400k-499k", "500k+"},
     *   message = "Invalid home value."
     * )
     * @Assert\Length(max=10, maxMessage="Home value length must be 10 chars max.")
     */
    protected $homeValue;

    /**
     * @ORM\Column(name="address_street", type="string",length=40, nullable=true)
     * @Assert\Length(max=40, maxMessage="Address street length must be 40 chars max.")
     */
    protected $addressStreet;

    /**
     * @ORM\Column(name="address_city", type="string",length=40, nullable=true)
     * @Assert\Length(max=40, maxMessage="Address city length must be 40 chars max.")
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
     * @ORM\Column(name="phone1number", type="string",length=20, nullable=true)
     * @Assert\Length(max=20, maxMessage="Phone number 1 length must be 20 chars max.")
     */
    protected $phone1Number;

    /**
     * @ORM\Column(name="phone1type", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "home", "mobile", "work"},
     *   message = "Invalid phone 1 type."
     * )
     * @Assert\Length(max=10, maxMessage="Phone type 1 length must be 10 chars max.")
     */
    protected $phone1Type;

    /**
     * @ORM\Column(name="phone2number", type="string",length=20, nullable=true)
     * @Assert\Length(max=20, maxMessage="Phone number 2 length must be 20 chars max.")
     */
    protected $phone2Number;

    /**
     * @ORM\Column(name="phone2type", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "home", "mobile", "work"},
     *   message = "Invalid phone 2 type."
     * )
     * @Assert\Length(max=10, maxMessage="Phone type 2 length must be 10 chars max.")
     */
    protected $phone2Type;

    /**
     * @ORM\Column(name="phone3number", type="string",length=20, nullable=true)
     * @Assert\Length(max=20, maxMessage="Phone number 3 length must be 20 chars max.")
     */
    protected $phone3Number;

    /**
     * @ORM\Column(name="phone3type", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "home", "mobile", "work"},
     *   message = "Invalid phone 3 type."
     * )
     * @Assert\Length(max=10, maxMessage="Phone type 3 length must be 10 chars max.")
     */
    protected $phone3Type;

    /**
     * @ORM\Column(name="primary_phone", type="string",length=1, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "1", "2", "3"},
     *   message = "Invalid DNC Lifetime Waiver Phone(primary phone)."
     * )
     * @Assert\Length(max=1)
     */
    protected $primaryPhone;

    /**
     * @ORM\Column(name="best_time_call", type="string",length=10, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "morning", "afternoon", "evening", "weekdays", "weekends"},
     *   message = "Invalid best time to call."
     * )
     * @Assert\Length(max=10, maxMessage="Best time to call length must be 10 chars max."))
     */
    protected $bestTimeCall;

    /**
     * @ORM\Column(name="email", type="string",length=100, nullable=true)
     * @Assert\Length(max=100, maxMessage="Email length must be 10 chars max."))
     * @Assert\Email
     */
    protected $email;

    /**
     * @ORM\Column(name="tcpa", type="boolean", nullable=false, options={"default" = 0})
     */
    protected $tcpa = 0;

    /**
     * @ORM\Column(name="wrong_number", type="boolean", nullable=false, options={"default" = 0})
     */
    protected $wrongNumber = 0;

    /**
     * @ORM\Column(name="restriction_comments", type="text", nullable=true)
     */
    protected $restrictionComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\History", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $histories;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Attachment", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $attachments;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Appointment", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $appointments;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Sale", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $sales;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Project", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\CallRecord", mappedBy="customer", cascade={"persist", "remove"})
     */
    protected $callRecords;

    /**
     * @ORM\Column(name="salesforce_id", type="string", length=20, nullable=true)
     */
    protected $salesforceId;

    /** @ORM\Column(name="is_legacy", type="boolean", nullable=false, options={"default" = 0})
     */
    protected $is_legacy = 0;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 1})
     */
    protected $isdeleted = 0;



    

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->histories   = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->appointments = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->callRecords = new ArrayCollection();
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
     * Set gid
     *
     * @param string $gid
     * @return Customer
     */
    public function setGid($gid)
    {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid
     *
     * @return string
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * Set primaryFirstName
     *
     * @param string $primaryFirstName
     * @return Customer
     */
    public function setPrimaryFirstName($primaryFirstName)
    {
        $this->primaryFirstName = $primaryFirstName;

        return $this;
    }

    /**
     * Get primaryFirstName
     *
     * @return string
     */
    public function getPrimaryFirstName()
    {
        return $this->primaryFirstName;
    }

    /**
     * Set primaryLastName
     *
     * @param string $primaryLastName
     * @return Customer
     */
    public function setPrimaryLastName($primaryLastName)
    {
        $this->primaryLastName = $primaryLastName;

        return $this;
    }

    /**
     * Get primaryLastName
     *
     * @return string
     */
    public function getPrimaryLastName()
    {
        return $this->primaryLastName;
    }

    /**
     * Set secondaryFirstName
     *
     * @param string $secondaryFirstName
     * @return Customer
     */
    public function setSecondaryFirstName($secondaryFirstName)
    {
        $this->secondaryFirstName = $secondaryFirstName;

        return $this;
    }

    /**
     * Get secondaryFirstName
     *
     * @return string
     */
    public function getSecondaryFirstName()
    {
        return $this->secondaryFirstName;
    }

    /**
     * Set secondaryLastName
     *
     * @param string $secondaryLastName
     * @return Customer
     */
    public function setSecondaryLastName($secondaryLastName)
    {
        $this->secondaryLastName = $secondaryLastName;

        return $this;
    }

    /**
     * Get secondaryLastName
     *
     * @return string
     */
    public function getSecondaryLastName()
    {
        return $this->secondaryLastName;
    }

    /**
     * Set secondaryRelationship
     *
     * @param string $secondaryRelationship
     * @return Customer
     */
    public function setSecondaryRelationship($secondaryRelationship)
    {
        $this->secondaryRelationship = $secondaryRelationship;

        return $this;
    }

    /**
     * Get secondaryRelationship
     *
     * @return string
     */
    public function getSecondaryRelationship()
    {
        return $this->secondaryRelationship;
    }

    /**
     * Set homeYearBuilt
     *
     * @param integer $homeYearBuilt
     * @return Customer
     */
    public function setHomeYearBuilt($homeYearBuilt)
    {
        $this->homeYearBuilt = $homeYearBuilt;

        return $this;
    }

    /**
     * Get homeYearBuilt
     *
     * @return integer
     */
    public function getHomeYearBuilt()
    {
        return $this->homeYearBuilt;
    }

    /**
     * Set homeYearPurchased
     *
     * @param integer $homeYearPurchased
     * @return Customer
     */
    public function setHomeYearPurchased($homeYearPurchased)
    {
        $this->homeYearPurchased = $homeYearPurchased;

        return $this;
    }

    /**
     * Get homeYearPurchased
     *
     * @return integer
     */
    public function getHomeYearPurchased()
    {
        return $this->homeYearPurchased;
    }

    /**
     * Set structureType
     *
     * @param string $structureType
     * @return Customer
     */
    public function setStructureType($structureType)
    {
        $this->structureType = $structureType;

        return $this;
    }

    /**
     * Get structureType
     *
     * @return string
     */
    public function getStructureType()
    {
        return $this->structureType;
    }

    /**
     * Set householdIncome
     *
     * @param string $householdIncome
     * @return Customer
     */
    public function setHouseholdIncome($householdIncome)
    {
        $this->householdIncome = $householdIncome;

        return $this;
    }

    /**
     * Get householdIncome
     *
     * @return string
     */
    public function getHouseholdIncome()
    {
        return $this->householdIncome;
    }

    /**
     * Set homeValue
     *
     * @param string $homeValue
     * @return Customer
     */
    public function setHomeValue($homeValue)
    {
        $this->homeValue = $homeValue;

        return $this;
    }

    /**
     * Get homeValue
     *
     * @return string
     */
    public function getHomeValue()
    {
        return $this->homeValue;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * Set phone1Number
     *
     * @param string $phone1Number
     * @return Customer
     */
    public function setPhone1Number($phone1Number)
    {
        $this->phone1Number = $phone1Number;

        return $this;
    }

    /**
     * Get phone1Number
     *
     * @return string
     */
    public function getPhone1Number()
    {
        return $this->phone1Number;
    }

    /**
     * Set phone1Type
     *
     * @param string $phone1Type
     * @return Customer
     */
    public function setPhone1Type($phone1Type)
    {
        $this->phone1Type = $phone1Type;

        return $this;
    }

    /**
     * Get phone1Type
     *
     * @return string
     */
    public function getPhone1Type()
    {
        return $this->phone1Type;
    }

    /**
     * Set phone2Number
     *
     * @param string $phone2Number
     * @return Customer
     */
    public function setPhone2Number($phone2Number)
    {
        $this->phone2Number = $phone2Number;

        return $this;
    }

    /**
     * Get phone2Number
     *
     * @return string
     */
    public function getPhone2Number()
    {
        return $this->phone2Number;
    }

    /**
     * Set phone2Type
     *
     * @param string $phone2Type
     * @return Customer
     */
    public function setPhone2Type($phone2Type)
    {
        $this->phone2Type = $phone2Type;

        return $this;
    }

    /**
     * Get phone2Type
     *
     * @return string
     */
    public function getPhone2Type()
    {
        return $this->phone2Type;
    }

    /**
     * Set phone3Number
     *
     * @param string $phone3Number
     * @return Customer
     */
    public function setPhone3Number($phone3Number)
    {
        $this->phone3Number = $phone3Number;

        return $this;
    }

    /**
     * Get phone3Number
     *
     * @return string
     */
    public function getPhone3Number()
    {
        return $this->phone3Number;
    }

    /**
     * Set phone3Type
     *
     * @param string $phone3Type
     * @return Customer
     */
    public function setPhone3Type($phone3Type)
    {
        $this->phone3Type = $phone3Type;

        return $this;
    }

    /**
     * Get phone3Type
     *
     * @return string
     */
    public function getPhone3Type()
    {
        return $this->phone3Type;
    }

    /**
     * Set primaryPhone
     *
     * @param string $primaryPhone
     * @return Customer
     */
    public function setPrimaryPhone($primaryPhone)
    {
        $this->primaryPhone = $primaryPhone;

        return $this;
    }

    /**
     * Get primaryPhone
     *
     * @return string
     */
    public function getPrimaryPhone()
    {
        return $this->primaryPhone;
    }

    /**
     * Set bestTimeCall
     *
     * @param string $bestTimeCall
     * @return Customer
     */
    public function setBestTimeCall($bestTimeCall)
    {
        $this->bestTimeCall = $bestTimeCall;

        return $this;
    }

    /**
     * Get bestTimeCall
     *
     * @return string
     */
    public function getBestTimeCall()
    {
        return $this->bestTimeCall;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Customer
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set tcpa
     *
     * @param boolean $tcpa
     * @return Customer
     */
    public function setTcpa($tcpa)
    {
        $this->tcpa = $tcpa;

        return $this;
    }

    /**
     * Get tcpa
     *
     * @return boolean
     */
    public function getTcpa()
    {
        return $this->tcpa;
    }

    /**
     * Set wrongNumber
     *
     * @param boolean $wrongNumber
     * @return Customer
     */
    public function setWrongNumber($wrongNumber)
    {
        $this->wrongNumber = $wrongNumber;

        return $this;
    }

    /**
     * Get wrongNumber
     *
     * @return boolean
     */
    public function getWrongNumber()
    {
        return $this->wrongNumber;
    }

    /**
     * Set restrictionComments
     *
     * @param string $restrictionComments
     * @return Customer
     */
    public function setRestrictionComments($restrictionComments)
    {
        $this->restrictionComments = $restrictionComments;

        return $this;
    }

    /**
     * Get restrictionComments
     *
     * @return string
     */
    public function getRestrictionComments()
    {
        return $this->restrictionComments;
    }

    /**
     * Set office
     *
     * @param \App\Lev\CRMBundle\Entity\Office $office
     * @return Customer
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
     * Set addressZip
     *
     * @param string $addressZip
     *
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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

    /**
     * Get primary fullName
     *
     * @return string
     */
    public function getPrimaryFullName()
    {
        return trim($this->getPrimaryFirstName() . ' ' . $this->getPrimaryLastName());
    }

    /**
     * Get secondary fullName
     *
     * @return string
     */
    public function getSecondaryFullName()
    {
        return trim($this->getSecondaryFirstName() . ' ' . $this->getSecondaryLastName());
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

    /**
     * @return array
     */
    public function toArray()
    {
        $attachments = array();
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = $attachment->toArrayShort();
        }

        return array(
            'id'                    => $this->getId(),
            'gid'                   => $this->getGid(),
            'primaryFirstName'      => $this->getPrimaryFirstName(),
            'primaryLastName'       => $this->getPrimaryLastName(),
            'secondaryFirstName'    => $this->getSecondaryFirstName(),
            'secondaryLastName'     => $this->getSecondaryLastName(),
            'secondaryRelationship' => $this->getSecondaryRelationship(),
            'homeYearBuilt'         => $this->getHomeYearBuilt(),
            'homeYearPurchased'     => $this->getHomeYearPurchased(),
            'structureType'         => $this->getStructureType(),
            'householdIncome'       => $this->getHouseholdIncome(),
            'homeValue'             => $this->getHomeValue(),
            'addressStreet'         => $this->getAddressStreet(),
            'addressCity'           => $this->getAddressCity(),
            'addressState'          => $this->getAddressState(),
            'addressZip'            => $this->getAddressZip(),
            'addressLat'            => $this->getAddressLat(),
            'addressLng'            => $this->getAddressLng(),
            'phone1Number'          => $this->getPhone1Number(),
            'phone1Type'            => $this->getPhone1Type(),
            'phone2Number'          => $this->getPhone2Number(),
            'phone2Type'            => $this->getPhone2Type(),
            'phone3Number'          => $this->getPhone3Number(),
            'phone3Type'            => $this->getPhone3Type(),
            'primaryPhone'          => $this->getPrimaryPhone(),
            'bestTimeCall'          => $this->getBestTimeCall(),
            'email'                 => $this->getEmail(),
            'tcpa'                  => $this->getTcpa(),
            'wrongNumber'           => $this->getWrongNumber(),
            'restrictionComments'   => $this->getRestrictionComments(),
            'office'                => $this->getOffice()->toArray(),
            'attachments'           => $attachments,
            'primaryFullName'       => $this->getPrimaryFullName(),
            'secondaryFullName'     => $this->getSecondaryFullName(),
            'createdAt'             => $this->getCreatedAt(),
            'createdBy'             => $this->getCreatedBy() ? $this->getCreatedBy()->toArrayShort() : null,
            'isdeleted'             => $this->getDeleted(),
        );
    }

    public function toArrayShort()
    {
        return array(
            'id'               => $this->getId(),
            'primaryFirstName' => $this->getPrimaryFirstName(),
            'primaryLastName'  => $this->getPrimaryLastName(),
            'primaryFullName'  => $this->getPrimaryFullName(),
            'email'            => $this->getEmail(),
        );
    }

    public function toArrayShortToCall()
    {
      return array(
          'id'                    => $this->getId(),
          'gid'                   => $this->getGid(),
          'primaryFirstName'      => $this->getPrimaryFirstName(),
          'primaryLastName'       => $this->getPrimaryLastName(),
          'secondaryFirstName'    => $this->getSecondaryFirstName(),
          'secondaryLastName'     => $this->getSecondaryLastName(),
          'secondaryRelationship' => $this->getSecondaryRelationship(),
          'homeYearBuilt'         => $this->getHomeYearBuilt(),
          'homeYearPurchased'     => $this->getHomeYearPurchased(),
          'structureType'         => $this->getStructureType(),
          'householdIncome'       => $this->getHouseholdIncome(),
          'homeValue'             => $this->getHomeValue(),
          'addressStreet'         => $this->getAddressStreet(),
          'addressCity'           => $this->getAddressCity(),
          'addressState'          => $this->getAddressState(),
          'addressZip'            => $this->getAddressZip(),
          'addressLat'            => $this->getAddressLat(),
          'addressLng'            => $this->getAddressLng(),
          'phone1Number'          => $this->getPhone1Number(),
          'phone1Type'            => $this->getPhone1Type(),
          'phone2Number'          => $this->getPhone2Number(),
          'phone2Type'            => $this->getPhone2Type(),
          'phone3Number'          => $this->getPhone3Number(),
          'phone3Type'            => $this->getPhone3Type(),
          'primaryPhone'          => $this->getPrimaryPhone(),
          'bestTimeCall'          => $this->getBestTimeCall(),
          'email'                 => $this->getEmail(),
          'tcpa'                  => $this->getTcpa(),
          'wrongNumber'           => $this->getWrongNumber(),
          'restrictionComments'   => $this->getRestrictionComments(),
          'primaryFullName'       => $this->getPrimaryFullName(),
          'secondaryFullName'     => $this->getSecondaryFullName(),
      );
    }

    /**
     * @return array
     */
    public function toArrayCallcenter()
    {
        $histories = array();
        // foreach($this->getHistories() as $history) {
        //     $histories[] = $history->toArray();
        // }

        return array(
            'id'                    => $this->getId(),
            'gid'                   => $this->getGid(),
            'primaryFirstName'      => $this->getPrimaryFirstName(),
            'primaryLastName'       => $this->getPrimaryLastName(),
            'secondaryFirstName'    => $this->getSecondaryFirstName(),
            'secondaryLastName'     => $this->getSecondaryLastName(),
            'secondaryRelationship' => $this->getSecondaryRelationship(),
            'homeYearBuilt'         => $this->getHomeYearBuilt(),
            'homeYearPurchased'     => $this->getHomeYearPurchased(),
            'structureType'         => $this->getStructureType(),
            'householdIncome'       => $this->getHouseholdIncome(),
            'homeValue'             => $this->getHomeValue(),
            'addressStreet'         => $this->getAddressStreet(),
            'addressCity'           => $this->getAddressCity(),
            'addressState'          => $this->getAddressState(),
            'addressZip'            => $this->getAddressZip(),
            'addressLat'            => $this->getAddressLat(),
            'addressLng'            => $this->getAddressLng(),
            'phone1Number'          => $this->getPhone1Number(),
            'phone1Type'            => $this->getPhone1Type(),
            'phone2Number'          => $this->getPhone2Number(),
            'phone2Type'            => $this->getPhone2Type(),
            'phone3Number'          => $this->getPhone3Number(),
            'phone3Type'            => $this->getPhone3Type(),
            'primaryPhone'          => $this->getPrimaryPhone(),
            'bestTimeCall'          => $this->getBestTimeCall(),
            'email'                 => $this->getEmail(),
            'tcpa'                  => $this->getTcpa(),
            'wrongNumber'           => $this->getWrongNumber(),
            'restrictionComments'   => $this->getRestrictionComments(),
            'primaryFullName'       => $this->getPrimaryFullName(),
            'secondaryFullName'     => $this->getSecondaryFullName(),
            'histories'             => $histories,
        );
    }

    /**
     * Add attachment
     *
     * @param \App\Lev\CRMBundle\Entity\Attachment $attachment
     *
     * @return Customer
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
     * @return Customer
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
    public function updatedDefault()
    {
        if (null === $this->getTcpa()) {
            $this->setTcpa(0);
        }
        if (null === $this->getWrongNumber()) {
            $this->setWrongNumber(0);
        }

        return $this;
    }



    /**
     * Add appointment
     *
     * @param \App\Lev\CRMBundle\Entity\Appointment $appointment
     *
     * @return Customer
     */
    public function addAppointment(\App\Lev\CRMBundle\Entity\Appointment $appointment)
    {
        $this->appointments[] = $appointment;

        return $this;
    }

    /**
     * Remove appointment
     *
     * @param \App\Lev\CRMBundle\Entity\Appointment $appointment
     */
    public function removeAppointment(\App\Lev\CRMBundle\Entity\Appointment $appointment)
    {
        $this->appointments->removeElement($appointment);
    }

    /**
     * Get appointments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAppointments()
    {
        return $this->appointments;
    }

    /**
     * Add sale
     *
     * @param \App\Lev\CRMBundle\Entity\Sale $sale
     *
     * @return Customer
     */
    public function addSale(\App\Lev\CRMBundle\Entity\Sale $sale)
    {
        $this->sales[] = $sale;

        return $this;
    }

    /**
     * Remove sale
     *
     * @param \App\Lev\CRMBundle\Entity\Sale $sale
     */
    public function removeSale(\App\Lev\CRMBundle\Entity\Sale $sale)
    {
        $this->sales->removeElement($sale);
    }

    /**
     * Get sales
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Add project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     *
     * @return Customer
     */
    public function addProject(\App\Lev\CRMBundle\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \App\Lev\CRMBundle\Entity\Project $project
     */
    public function removeProject(\App\Lev\CRMBundle\Entity\Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add callRecord
     *
     * @param \App\Lev\CRMBundle\Entity\CallRecord $callRecord
     *
     * @return Customer
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
