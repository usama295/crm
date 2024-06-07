<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * StaffMember Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="staff")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Staff extends BaseUser
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="first_name", type="string",length=20)
     * @Assert\NotBlank(message="Required", groups={"profile", "admin"})
     * @Assert\Length(max=20, groups={"profile", "admin"})
     */
    protected $firstName;

    /**
     * @ORM\Column(name="last_name", type="string",length=40, nullable=true)
     * @Assert\Length(max=40, groups={"profile", "admin"})
     */
    protected $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Office")
     * @ORM\JoinColumn(name="office_id", referencedColumnName="id", nullable=true)
     **/
    protected $office;

    /**
     * @ORM\ManyToMany(targetEntity="App\Lev\CRMBundle\Entity\StaffRole")
     * @ORM\JoinTable(name="staff_role_members",
     *   joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *   inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $staffroles;

    /**
     * @ORM\Column(name="position_title", type="string",length=20, nullable=true)
     * @Assert\Length(max=20, groups={"profile", "admin"})
     */
    protected $positionTitle;

    /**
     * @ORM\Column(name="employment_type", type="string",length=20, nullable=true)
     * @Assert\Choice(
     *   choices = {"", "none", "full-time", "part-time", "contractor", "temporary"},
     *   message = "The value must be one of none, full-time, part-time, contractor, temporary"
     * )
     * @Assert\Length(max=20, groups={"profile", "admin"})
     */
    protected $employmentType;

    /**
     * @ORM\Column(name="employment_date_start", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $employmentDateStart;

    /**
     * @ORM\Column(name="employment_date_end", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $employmentDateEnd;

    /**
     * @ORM\Column(name="sales_capab_comp", type="array", nullable=true)
     * @Assert\Choice(
     *   choices  = {"doors", "gutters", "roofing", "siding", "trim", "windows"},
     *   message  = "Invalid sales capabilities component: {{ value }} (expected: doors, gutters, roofing, siding, trim, windows).",
     *   multiple = true
     * )
     */
    protected $salesCapabComp;

    /**
     * @ORM\Column(name="project_capab_comp", type="array", nullable=true)
     * @Assert\Choice(
     *   choices  = {"doors", "gutters", "roofing", "siding", "trim", "windows"},
     *   message  = "Invalid project capabilities component: {{ value }} (expected: doors, gutters, roofing, siding, trim, windows).",
     *   multiple = true
     * )
     */
    protected $projectCapabComp;

    /**
     * @ORM\Column(name="marketing_capab_comp", type="boolean", nullable=true)
     */
    protected $marketingCapabComp;

    /**
     * @ORM\Column(name="certified_renovator", type="boolean", nullable=true)
     */
    protected $certifiedRenovator;

    /**
     * @ORM\Column(name="certification_id", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $certificationId;

    /**
     * @ORM\Column(name="lswp_job_training", type="boolean", nullable=true)
     */
    protected $lswpJobTraining;

    /**
     * @ORM\Column(name="certification_expiration", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $certificationExpiration;

    /**
     * @ORM\Column(name="address_street", type="string",length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    protected $addressStreet;

    /**
     * @ORM\Column(name="address_city", type="string",length=30, nullable=true)
     * @Assert\Length(max=30)
     */
    protected $addressCity;

    /**
     * @ORM\Column(name="address_state", type="string",length=2, nullable=true)
     * @Assert\Length(max=2)
     */
    protected $addressState;

    /**
     * @ORM\Column(name="address_zip", type="string",length=9, nullable=true)
     * @Assert\Length(max=9)
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
     * @ORM\Column(name="phone_home", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $phoneHome;

    /**
     * @ORM\Column(name="phone_mobile", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $phoneMobile;

    /**
     * @ORM\Column(name="phone_work", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $phoneWork;

    /**
     * @ORM\Column(name="phone_twilio", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $phoneTwilio;

    /**
     * @ORM\Column(name="emergency_contact_name", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $emergencyContactName;

    /**
     * @ORM\Column(name="emergency_contact_phone", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $emergencyContactPhone;

    /**
     * @ORM\Column(name="emergency_contact_relation", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $emergencyContactRelation;

    /**
     * @ORM\Column(name="driver_licence_number", type="string",length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $driverLicenceNumber;

    /**
     * @ORM\Column(name="driver_licence_state", type="string",length=2, nullable=true)
     * @Assert\Length(max=2)
     */
    protected $driverLicenceState;

    /**
     * @ORM\Column(name="driver_licence_expiration", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $driverLicenceExpiration;

    /**
     * @ORM\Column(name="auto_liab_ins_provider", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $autoLiabInsProvider;

    /**
     * @ORM\Column(name="auto_liab_ins_coverage", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $autoLiabInsCoverage;

    /**
     * @ORM\Column(name="auto_liab_ins_expiration", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $autoLiabInsExpiration;

    /**
     * @ORM\Column(name="workers_comp_ins_provider", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $workersCompInsProvider;

    /**
     * @ORM\Column(name="workers_comp_ins_coverage", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $workersCompInsCoverage;

    /**
     * @ORM\Column(name="workers_comp_ins_expiration", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $workersCompInsExpiration;

    /**
     * @ORM\Column(name="liab_ins_provider", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $liabInsProvider;

    /**
     * @ORM\Column(name="liab_ins_coverage", type="string",length=40, nullable=true)
     * @Assert\Length(max=40)
     */
    protected $liabInsCoverage;

    /**
     * @ORM\Column(name="liab_ins_expiration", type="date", nullable=true)
     * @Assert\Date(message="Invalid date")
     */
    protected $liabInsExpiration;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Attachment", mappedBy="staff", cascade={"persist", "remove"})
     */
    protected $attachments;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\Appointment", mappedBy="lockedBy", cascade={"persist", "remove"})
     */
    protected $callsAsLocker;

    /**
     * @ORM\Column(name="salesforce_id", type="string", length=20, nullable=true)
     */
    protected $salesforceId;

    /**
     * @ORM\Column(name="salesforce_user_id", type="string", length=20, nullable=true)
     */
    protected $salesforceUserId;


    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity(array(
            'fields'  => 'email',
            'message' => 'This email already exists.',
        )));

        $metadata->addPropertyConstraint('email', new Assert\Email());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $staffroles = array();
        foreach($this->getStaffroles() as $staffrole) {
            $staffroles[] = $staffrole->getId();
        }

        $attachments = array();
        foreach($this->getAttachments() as $attachment) {
            $attachments[] = $attachment->toArrayShort();
        }

        return array(
            'id'                       => $this->getId(),
            'username'                 => $this->getUsername(),
            'email'                    => $this->getEmail(),
            'enabled'                  => $this->isEnabled(),
            'superAdmin'               => $this->isSuperAdmin(),
            'roles'                    => (array) $this->getRoles(),
            'staffroles'               => $staffroles,
            'firstName'                => $this->getFirstName(),
            'lastName'                 => $this->getLastName(),
            'fullName'                 => $this->getFullName(),
            'positionTitle'            => $this->getPositionTitle(),
            'employmentType'           => $this->getEmploymentType(),
            'employmentDateStart'      => $this->getEmploymentDateStart(),
            'employmentDateEnd'        => $this->getEmploymentDateEnd(),
            'salesCapabComp'           => $this->getSalesCapabComp(),
            'projectCapabComp'         => $this->getProjectCapabComp(),
            'marketingCapabComp'       => $this->getMarketingCapabComp(),
            'office'                   => $this->getOffice() ? $this->getOffice()->toArray() : array(),
            'certifiedRenovator'       => $this->getCertifiedRenovator(),
            'lswpJobTraining'          => $this->getLswpJobTraining(),
            'certificationId'          => $this->getCertificationId(),
            'certificationExpiration'  => $this->getCertificationExpiration(),
            'addressStreet'            => $this->getAddressStreet(),
            'addressCity'              => $this->getAddressCity(),
            'addressState'             => $this->getAddressState(),
            'addressZip'               => $this->getAddressZip(),
            'addressLat'               => $this->getAddressLat(),
            'addressLng'               => $this->getAddressLng(),
            'phoneHome'                => $this->getPhoneHome(),
            'phoneMobile'              => $this->getPhoneMobile(),
            'phoneWork'                => $this->getPhoneWork(),
            'phoneTwilio'              => $this->getPhoneTwilio(),
            'emergencyContactName'     => $this->getEmergencyContactName(),
            'emergencyContactPhone'    => $this->getEmergencyContactPhone(),
            'emergencyContactRelation' => $this->getEmergencyContactRelation(),
            'driverLicenceNumber'      => $this->getDriverLicenceNumber(),
            'driverLicenceState'       => $this->getDriverLicenceState(),
            'driverLicenceExpiration'  => $this->getDriverLicenceExpiration(),
            'autoLiabInsProvider'      => $this->getAutoLiabInsProvider(),
            'autoLiabInsCoverage'      => $this->getAutoLiabInsCoverage(),
            'autoLiabInsExpiration'    => $this->getAutoLiabInsExpiration(),
            'workersCompInsProvider'   => $this->getWorkersCompInsProvider(),
            'workersCompInsCoverage'   => $this->getWorkersCompInsCoverage(),
            'workersCompInsExpiration' => $this->getWorkersCompInsExpiration(),
            'liabInsProvider'          => $this->getLiabInsProvider(),
            'liabInsCoverage'          => $this->getLiabInsCoverage(),
            'liabInsExpiration'        => $this->getLiabInsExpiration(),
            'attachments'              => $attachments,
        );
    }

    /**
     * @return array
     */
    public function toArrayShort()
    {
        return array(
            'id'                       => $this->getId(),
            'username'                 => $this->getUsername(),
            'email'                    => $this->getEmail(),
            'firstName'                => $this->getFirstName(),
            'lastName'                 => $this->getLastName(),
            'fullName'                 => $this->getFullName(),
            'phoneTwilio'              => $this->getPhoneTwilio(),
        );
    }

    /**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        $roles = $this->roles;

        foreach ($this->getStaffroles() as $group) {
            $roles = array_merge($roles, $group->getRoles());
            if ($group->isSuperadmin()) {
                $roles[] = 'ROLE_ADMIN';
             }
        }

        // we need to make sure to have at least one role
        $roles[] = static::ROLE_DEFAULT;
        array_unique($roles);

        return array_unique($roles);
    }

    /**
     * Check if user has role
     * @param  string  $role
     * @return boolean
     */
    public function hasRole($role) {
        return in_array($role, $this->getRoles());
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->staffroles  = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attachments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->callsAsLocker = new ArrayCollection();
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
     * Add staffroles
     *
     * @param App\Lev\CRMBundle\Entity\StaffRole $staffroles
     * @return Staff
     */
    public function addStaffrole()
    {   
         
        $staffroles = new \App\Lev\CRMBundle\Entity\StaffRole;
       
        $this->staffroles[] = $staffroles;

        return $this;
    }

    /**
     * Remove staffroles
     *
     * @param App\Lev\CRMBundle\Entity\StaffRole $staffroles
     */
    public function removeStaffrole()
    {
        $staffroles = App\Lev\CRMBundle\Entity\StaffRole;
        $this->staffroles->removeElement($staffroles);
    }

    /**
     * Get staffroles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStaffroles()
    {
        return $this->staffroles;
    }

    /**
     * Get staffroles
     *
     * @return $this
     */
    public function setStaffroles(\Doctrine\Common\Collections\ArrayCollection $staffroles)
    {
        $toRemove = array();
        foreach($this->getStaffroles() as $staffrole) {
            $toRemove[$staffrole->getId()] = $staffrole;
        }
        foreach ($staffroles as $staffrole) {
            if (array_key_exists($staffrole->getId(), $toRemove)) {
                unset($toRemove[$staffrole->getId()]);
            } else {
                $this->addStaffrole($staffrole);
            }
        }
        foreach ($toRemove as $staffrole) {
            $this->removeStaffrole($staffrole);
        }

        return $this;
    }


    /**
     * Check if user has a staffrole
     * @param  string/integer  $staffrole Staffrole id or shortname
     * @return boolean            [description]
     */
    public function hasStaffrole($staffrole)
    {
        foreach ($this->getStaffroles() as $sr) {
          if ($sr->getId() === $staffrole || $sr->getShortname() === $staffrole) {
            return true;
          }
          if ($sr->getShortname() === 'ADMIN') {
            return true;
          }
        }

        return false;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return Staff
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Staff
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return trim($this->getFirstName() . ' ' . $this->getLastName());
    }

    /**
     * Set positionTitle
     *
     * @param string $positionTitle
     * @return Staff
     */
    public function setPositionTitle($positionTitle)
    {
        $this->positionTitle = $positionTitle;

        return $this;
    }

    /**
     * Get positionTitle
     *
     * @return string
     */
    public function getPositionTitle()
    {
        return $this->positionTitle;
    }

    /**
     * Set employmentType
     *
     * @param string $employmentType
     * @return Staff
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;

        return $this;
    }

    /**
     * Get employmentType
     *
     * @return string
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * Set employmentDateStart
     *
     * @param \DateTime $employmentDateStart
     * @return Staff
     */
    public function setEmploymentDateStart($employmentDateStart)
    {
        $this->employmentDateStart = $employmentDateStart;

        return $this;
    }

    /**
     * Get employmentDateStart
     *
     * @return \DateTime
     */
    public function getEmploymentDateStart()
    {
        return $this->employmentDateStart;
    }

    /**
     * Set employmentDateEnd
     *
     * @param \DateTime $employmentDateEnd
     * @return Staff
     */
    public function setEmploymentDateEnd($employmentDateEnd)
    {
        $this->employmentDateEnd = $employmentDateEnd;

        return $this;
    }

    /**
     * Get employmentDateEnd
     *
     * @return \DateTime
     */
    public function getEmploymentDateEnd()
    {
        return $this->employmentDateEnd;
    }

    /**
     * Set salesCapabComp
     *
     * @param array $salesCapabComp
     * @return Staff
     */
    public function setSalesCapabComp($salesCapabComp)
    {
        $this->salesCapabComp = $salesCapabComp;

        return $this;
    }

    /**
     * Get salesCapabComp
     *
     * @return array
     */
    public function getSalesCapabComp()
    {
        return $this->salesCapabComp;
    }

    /**
     * Set projectCapabComp
     *
     * @param array $projectCapabComp
     * @return Staff
     */
    public function setProjectCapabComp($projectCapabComp)
    {
        $this->projectCapabComp = $projectCapabComp;

        return $this;
    }

    /**
     * Get projectCapabComp
     *
     * @return array
     */
    public function getProjectCapabComp()
    {
        return $this->projectCapabComp;
    }

    /**
     * Set office
     *
     * @param App\Lev\CRMBundle\Entity\Office $office
     * @return Staff
     */
    public function setOffice()
    {
        $office = new \App\Lev\CRMBundle\Entity\Office;
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
     * Set certifiedRenovator
     *
     * @param boolean $certifiedRenovator
     * @return Staff
     */
    public function setCertifiedRenovator($certifiedRenovator)
    {
        $this->certifiedRenovator = $certifiedRenovator;

        return $this;
    }

    /**
     * Get certifiedRenovator
     *
     * @return boolean
     */
    public function getCertifiedRenovator()
    {
        return $this->certifiedRenovator;
    }

    /**
     * Set lswpJobTraining
     *
     * @param boolean $lswpJobTraining
     * @return Staff
     */
    public function setLswpJobTraining($lswpJobTraining)
    {
        $this->lswpJobTraining = $lswpJobTraining;

        return $this;
    }

    /**
     * Get lswpJobTraining
     *
     * @return boolean
     */
    public function getLswpJobTraining()
    {
        return $this->lswpJobTraining;
    }

    /**
     * Set certificationId
     *
     * @param string $certificationId
     * @return Staff
     */
    public function setCertificationId($certificationId)
    {
        $this->certificationId = $certificationId;

        return $this;
    }

    /**
     * Get certificationId
     *
     * @return string
     */
    public function getCertificationId()
    {
        return $this->certificationId;
    }

    /**
     * Set certificationExpiration
     *
     * @param \DateTime $certificationExpiration
     * @return Staff
     */
    public function setCertificationExpiration($certificationExpiration)
    {
        $this->certificationExpiration = $certificationExpiration;

        return $this;
    }

    /**
     * Get certificationExpiration
     *
     * @return \DateTime
     */
    public function getCertificationExpiration()
    {
        return $this->certificationExpiration;
    }

    /**
     * Set addressStreet
     *
     * @param string $addressStreet
     * @return Staff
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
     * @return Staff
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
     * @return Staff
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
     * Set phoneHome
     *
     * @param string $phoneHome
     * @return Staff
     */
    public function setPhoneHome($phoneHome)
    {
        $this->phoneHome = $phoneHome;

        return $this;
    }

    /**
     * Get phoneHome
     *
     * @return string
     */
    public function getPhoneHome()
    {
        return $this->phoneHome;
    }

    /**
     * Set phoneMobile
     *
     * @param string $phoneMobile
     * @return Staff
     */
    public function setPhoneMobile($phoneMobile)
    {
        $this->phoneMobile = $phoneMobile;

        return $this;
    }

    /**
     * Get phoneMobile
     *
     * @return string
     */
    public function getPhoneMobile()
    {
        return $this->phoneMobile;
    }

    /**
     * Set phoneWork
     *
     * @param string $phoneWork
     * @return Staff
     */
    public function setPhoneWork($phoneWork)
    {
        $this->phoneWork = $phoneWork;

        return $this;
    }

    /**
     * Get phoneWork
     *
     * @return string
     */
    public function getPhoneWork()
    {
        return $this->phoneWork;
    }

    /**
     * Set emergencyContactName
     *
     * @param string $emergencyContactName
     * @return Staff
     */
    public function setEmergencyContactName($emergencyContactName)
    {
        $this->emergencyContactName = $emergencyContactName;

        return $this;
    }

    /**
     * Get emergencyContactName
     *
     * @return string
     */
    public function getEmergencyContactName()
    {
        return $this->emergencyContactName;
    }

    /**
     * Set emergencyContactPhone
     *
     * @param string $emergencyContactPhone
     * @return Staff
     */
    public function setEmergencyContactPhone($emergencyContactPhone)
    {
        $this->emergencyContactPhone = $emergencyContactPhone;

        return $this;
    }

    /**
     * Get emergencyContactPhone
     *
     * @return string
     */
    public function getEmergencyContactPhone()
    {
        return $this->emergencyContactPhone;
    }

    /**
     * Set emergencyContactRelation
     *
     * @param string $emergencyContactRelation
     * @return Staff
     */
    public function setEmergencyContactRelation($emergencyContactRelation)
    {
        $this->emergencyContactRelation = $emergencyContactRelation;

        return $this;
    }

    /**
     * Get emergencyContactRelation
     *
     * @return string
     */
    public function getEmergencyContactRelation()
    {
        return $this->emergencyContactRelation;
    }

    /**
     * Set driverLicenceNumber
     *
     * @param string $driverLicenceNumber
     * @return Staff
     */
    public function setDriverLicenceNumber($driverLicenceNumber)
    {
        $this->driverLicenceNumber = $driverLicenceNumber;

        return $this;
    }

    /**
     * Get driverLicenceNumber
     *
     * @return string
     */
    public function getDriverLicenceNumber()
    {
        return $this->driverLicenceNumber;
    }

    /**
     * Set driverLicenceState
     *
     * @param string $driverLicenceState
     * @return Staff
     */
    public function setDriverLicenceState($driverLicenceState)
    {
        $this->driverLicenceState = $driverLicenceState;

        return $this;
    }

    /**
     * Get driverLicenceState
     *
     * @return string
     */
    public function getDriverLicenceState()
    {
        return $this->driverLicenceState;
    }

    /**
     * Set driverLicenceExpiration
     *
     * @param \DateTime $driverLicenceExpiration
     * @return Staff
     */
    public function setDriverLicenceExpiration($driverLicenceExpiration)
    {
        $this->driverLicenceExpiration = $driverLicenceExpiration;

        return $this;
    }

    /**
     * Get driverLicenceExpiration
     *
     * @return \DateTime
     */
    public function getDriverLicenceExpiration()
    {
        return $this->driverLicenceExpiration;
    }

    /**
     * Set autoLiabInsProvider
     *
     * @param string $autoLiabInsProvider
     * @return Staff
     */
    public function setAutoLiabInsProvider($autoLiabInsProvider)
    {
        $this->autoLiabInsProvider = $autoLiabInsProvider;

        return $this;
    }

    /**
     * Get autoLiabInsProvider
     *
     * @return string
     */
    public function getAutoLiabInsProvider()
    {
        return $this->autoLiabInsProvider;
    }

    /**
     * Set autoLiabInsCoverage
     *
     * @param string $autoLiabInsCoverage
     * @return Staff
     */
    public function setAutoLiabInsCoverage($autoLiabInsCoverage)
    {
        $this->autoLiabInsCoverage = $autoLiabInsCoverage;

        return $this;
    }

    /**
     * Get autoLiabInsCoverage
     *
     * @return string
     */
    public function getAutoLiabInsCoverage()
    {
        return $this->autoLiabInsCoverage;
    }

    /**
     * Set autoLiabInsExpiration
     *
     * @param \DateTime $autoLiabInsExpiration
     * @return Staff
     */
    public function setAutoLiabInsExpiration($autoLiabInsExpiration)
    {
        $this->autoLiabInsExpiration = $autoLiabInsExpiration;

        return $this;
    }

    /**
     * Get autoLiabInsExpiration
     *
     * @return \DateTime
     */
    public function getAutoLiabInsExpiration()
    {
        return $this->autoLiabInsExpiration;
    }

    /**
     * Set workersCompInsProvider
     *
     * @param string $workersCompInsProvider
     * @return Staff
     */
    public function setWorkersCompInsProvider($workersCompInsProvider)
    {
        $this->workersCompInsProvider = $workersCompInsProvider;

        return $this;
    }

    /**
     * Get workersCompInsProvider
     *
     * @return string
     */
    public function getWorkersCompInsProvider()
    {
        return $this->workersCompInsProvider;
    }

    /**
     * Set workersCompInsCoverage
     *
     * @param string $workersCompInsCoverage
     * @return Staff
     */
    public function setWorkersCompInsCoverage($workersCompInsCoverage)
    {
        $this->workersCompInsCoverage = $workersCompInsCoverage;

        return $this;
    }

    /**
     * Get workersCompInsCoverage
     *
     * @return string
     */
    public function getWorkersCompInsCoverage()
    {
        return $this->workersCompInsCoverage;
    }

    /**
     * Set workersCompInsExpiration
     *
     * @param \DateTime $workersCompInsExpiration
     * @return Staff
     */
    public function setWorkersCompInsExpiration($workersCompInsExpiration)
    {
        $this->workersCompInsExpiration = $workersCompInsExpiration;

        return $this;
    }

    /**
     * Get workersCompInsExpiration
     *
     * @return \DateTime
     */
    public function getWorkersCompInsExpiration()
    {
        return $this->workersCompInsExpiration;
    }

    /**
     * Set liabInsProvider
     *
     * @param string $liabInsProvider
     * @return Staff
     */
    public function setLiabInsProvider($liabInsProvider)
    {
        $this->liabInsProvider = $liabInsProvider;

        return $this;
    }

    /**
     * Get liabInsProvider
     *
     * @return string
     */
    public function getLiabInsProvider()
    {
        return $this->liabInsProvider;
    }

    /**
     * Set liabInsCoverage
     *
     * @param string $liabInsCoverage
     * @return Staff
     */
    public function setLiabInsCoverage($liabInsCoverage)
    {
        $this->liabInsCoverage = $liabInsCoverage;

        return $this;
    }

    /**
     * Get liabInsCoverage
     *
     * @return string
     */
    public function getLiabInsCoverage()
    {
        return $this->liabInsCoverage;
    }

    /**
     * Set liabInsExpiration
     *
     * @param \DateTime $liabInsExpiration
     * @return Staff
     */
    public function setLiabInsExpiration($liabInsExpiration)
    {
        $this->liabInsExpiration = $liabInsExpiration;

        return $this;
    }

    /**
     * Get liabInsExpiration
     *
     * @return \DateTime
     */
    public function getLiabInsExpiration()
    {
        return $this->liabInsExpiration;
    }

    /**
     * Check if is group superadmin
     *
     * @return bool
     */
    public function isGroupAdmin()
    {
        $admin = false;
        foreach($this->getStaffroles() as $group) {
            if ($group->isSuperadmin()) {
                $admin = true;
            }
        }
        return $admin;
    }

    /**
     * Set marketingCapabComp
     *
     * @param boolean $marketingCapabComp
     *
     * @return Staff
     */
    public function setMarketingCapabComp($marketingCapabComp)
    {
        $this->marketingCapabComp = $marketingCapabComp;

        return $this;
    }

    /**
     * Get marketingCapabComp
     *
     * @return boolean
     */
    public function getMarketingCapabComp()
    {
        return $this->marketingCapabComp;
    }

    /**
     * Set addressZip
     *
     * @param string $addressZip
     *
     * @return Staff
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
     * @return Staff
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
     * @return Staff
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
     * Add attachment
     *
     * @param App\Lev\CRMBundle\Entity\Attachment $attachment
     *
     * @return Staff
     */
    public function addAttachment()
    {
        $attachment = App\Lev\CRMBundle\Entity\Attachment;
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param App\Lev\CRMBundle\Entity\Attachment $attachment
     */
    public function removeAttachment()
    {
        $attachment = App\Lev\CRMBundle\Entity\Attachment;
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
     * @return Staff
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
     * Set salesforceUserId
     *
     * @param string $salesforceUserId
     *
     * @return Staff
     */
    public function setSalesforceUserId($salesforceUserId)
    {
        $this->salesforceUserId = $salesforceUserId;

        return $this;
    }

    /**
     * Get salesforceUserId
     *
     * @return string
     */
    public function getSalesforceUserId()
    {
        return $this->salesforceUserId;
    }

    /**
     * Add callsAsLocker
     *
     * @param App\Lev\CRMBundle\Entity\Call $callsAsLocker
     *
     * @return Staff
     */
    public function addCallsAsLocker()
    {
        $callsAsLocker = App\Lev\CRMBundle\Entity\Call;
        $this->callsAsLocker[] = $callsAsLocker;

        return $this;
    }

    /**
     * Remove callsAsLocker
     *
     * @param App\Lev\CRMBundle\Entity\Call $callsAsLocker
     */
    public function removeCallsAsLocker()
    {
        $callsAsLocker = App\Lev\CRMBundle\Entity\Call;
        $this->callsAsLocker->removeElement($callsAsLocker);
    }

    /**
     * Get callsAsLocker
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCallsAsLocker()
    {
        return $this->callsAsLocker;
    }

    /**
     * Add callsAsOwner
     *
     * @param App\Lev\CRMBundle\Entity\Call $callsAsOwner
     *
     * @return Staff
     */
    public function addCallsAsOwner()
    {
        $callsAsOwner = App\Lev\CRMBundle\Entity\Call;
        $this->callsAsOwner[] = $callsAsOwner;

        return $this;
    }

    /**
     * Remove callsAsOwner
     *
     * @param App\Lev\CRMBundle\Entity\Call $callsAsOwner
     */
    public function removeCallsAsOwner()
    {
        $callsAsOwner = App\Lev\CRMBundle\Entity\Call;
        $this->callsAsOwner->removeElement($callsAsOwner);
    }

    /**
     * Get callsAsOwner
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCallsAsOwner()
    {
        return $this->callsAsOwner;
    }

    /**
     * Set phoneTwilio
     *
     * @param string $phoneTwilio
     *
     * @return Staff
     */
    public function setPhoneTwilio($phoneTwilio)
    {
        $this->phoneTwilio = $phoneTwilio;

        return $this;
    }

    /**
     * Get phoneTwilio
     *
     * @return string
     */
    public function getPhoneTwilio()
    {
        return $this->phoneTwilio;
    }
}
