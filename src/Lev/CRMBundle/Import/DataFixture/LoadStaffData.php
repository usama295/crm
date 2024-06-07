<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Staff;
use Doctrine\Common\Collections\ArrayCollection;

class LoadStaffData extends AbstractDataFixture implements DataFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->loadFromArray();
        $this->loadFromCSV();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 4;
    }

    public function loadFromArray()
    {
        $this->setDefault('staff', 1);

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        try {
            $count = 0;
            $staffs = $this->getStaffs();
            $this->progressStart('Admin staff', count($staffs));
            foreach ($staffs as $s) {
                $count++;
                $staff = $userManager->createUser();
                $staff->setEnabled(true);
                foreach($s as $method => $value) {
                    if ($method === 'setStaffroles') {
                        $arrayCollection = new ArrayCollection();
                        foreach ($value as $id) {
                            $arrayCollection->add($this->getReference('staffrole', $id));
                        }
                        $staff->setStaffroles($arrayCollection);
                    } else if ($method === 'setOffice') {
                        $staff->setOffice($this->getReference('office', $value));
                    } else {
                        $staff->$method($value);
                    }
                }

                if (!array_key_exists('setStaffroles', $s)) {
                    $arrayCollection = new ArrayCollection();
                    $arrayCollection->add($this->getReference('staffrole', rand(2,4)));
                    $staff->setStaffroles($arrayCollection);
                }

                $staff->setOffice($this->getReference('office', 1));

                $staff->setUsername($s['setEmail']);
                $userManager->updatePassword($staff);
                $userManager->updateUser($staff);
                if ($count === 1) {
                    $this->addReference('staff', 'admin', $staff);
                } else {
                    $this->addReference('staff', $staff->getId(), $staff);
                }
                $this->progressAdvance();
            }
            $this->progressFinish();
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }

    }

    public function getStaffs()
    {

        return array(
            array(
                "setFirstName" => "Admin",
                "setLastName" => "Test",
                "setEmail" => "admin@test.com",
                "setAddressStreet" => "Ap #414-335 Lobortis Street",
                "setAddressCity" => "Caruaru",
                "setAddressState" => "RS",
                "setAddressZip" => "93328",
                "setAddressLat"=>"-37.95802",
                "setAddressLng"=>"167.03549",
                "setPhoneHome" => "(339) 285-3020",
                "setPhoneMobile" => "(347) 789-6757",
                "setEmergencyContactName" => "Lamar Z. Wilcox",
                "setEmergencyContactPhone" => "(863) 860-7110",
                "setStaffroles" => array(1),
                "setPlainPassword" => '654321',
            ),
            array(
                "setFirstName" => "SuperAdmin",
                "setLastName" => "CRM",
                "setEmail" => "dev@lev-interactive.com",
                "setAddressStreet" => "12614 Hillmeade Station Drive",
                "setAddressCity" => "Bowie",
                "setAddressState" => "ND",
                "setAddressZip" => "20720",
                "setAddressLat"=>"38.984676",
                "setAddressLng"=>"-76.785104",
                "setPhoneHome" => "",
                "setPhoneMobile" => "",
                "setEmergencyContactName" => "",
                "setEmergencyContactPhone" => "",
                "setStaffroles" => array(1),
                "setPlainPassword" => 'js028NSJQAZLP0Hn193NoO0sms2',
                "setRoles" => array('ROLE_SUPER_ADMIN'),
            ),
        );
    }

    public function loadFromCSV()
    {
        $manager = $this->getManager();
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->getContainer()->get('fos_user.user_manager');

        $positionTitles = array(
            'Sales'           => 4,
            'Sales man'       => 4,
            'Call Center RVP' => 3,
            'Call Center rep' => 3,
            'Call Center Rep' => 3,
            'Call Center'     => 3,
        );

        try {
            $staffs = $this->getCSV();
            $this->progressStart('staff', count($staffs));
            foreach ($staffs as $item) {

                if (empty($item[22])) {
                  $this->addReference('staff', $item[68], false);
                  continue;
                }

                $staff = $userManager->createUser();
                $name      = explode(' ', $item[61]);
                $lastName  = array_pop($name);
                $firstName = implode(' ', $name);
                $staff
                    ->setSalesforceId($item[0])
                    ->setSalesforceUserId($item[68])
                    ->setEnabled($item[1] === 'Active')
                    ->setUsername($item[22]) // User $item[68]
                    ->setEmail($item[22])

                    // TODO
                    ->setOffice($this->getReference('office', 1))

                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setPositionTitle($item[44])
                    // ->setEmploymentType(null)
                    ->setEmploymentDateStart(!empty($item[62]) ? new \DateTime($item[62]) : null)
                    ->setEmploymentDateEnd(!empty($item[28]) ? new \DateTime($item[28]) : null)
                    ->setSalesCapabComp(explode(';', trim(strtolower($item[55]))))
                    ->setProjectCapabComp(explode(';', trim(strtolower($item[50]))))
                    // ->setCertifiedRenovator(null)
                    // ->setLswpJobTraining(null)
                    // ->setCertificationId(null)
                    // ->setCertificationExpiration(null)
                    ->setAddressStreet($item[64])
                    ->setAddressCity(null)
                    ->setAddressState($item[63]) /// ??? Maryland, Massachussets
                    ->setAddressZip($item[73])
                    // ->setAddressLat(null)
                    // ->setAddressLng(null)
                    ->setPhoneHome($item[30])
                    ->setPhoneMobile($item[45])
                    // ->setPhoneWork(null)
                    ->setEmergencyContactName($item[24])
                    ->setEmergencyContactPhone($item[25])
                    ->setEmergencyContactRelation($item[26])
                    // ->setDriverLicenceNumber(null)
                    // ->setDriverLicenceState(null)
                    ->setDriverLicenceExpiration(!empty($item[19]) ? new \DateTime($item[19]) : null)
                    // ->setAutoLiabInsProvider(null)
                    // ->setAutoLiabInsCoverage(null)
                    // ->setAutoLiabInsExpiration(null)
                    //  ->setWorkersCompInsProvider(null)
                    //  ->setWorkersCompInsCoverage(null)
                    //  ->setWorkersCompInsExpiration(null)
                    //  ->setLiabInsProvider(null)
                    //  ->setLiabInsCoverage(null)
                    //  ->setLiabInsExpiration(null)
                    ->setMarketingCapabComp($item[44] === 'true' ? 1 : 0)
                    ->setPlainPassword('123456')
                    ;

                // Marketing User 44
                if($item[44] === 'true') {
                    $staffroleCollection = new ArrayCollection();
                    $staffroleCollection->add($this->getReference('staffrole', 2));
                    $staff->setStaffroles($staffroleCollection);
                }
                // Position Title 44
                if(!empty($item[44]) && in_array($item[44], $positionTitles)) {
                    $staffroleCollection = new ArrayCollection();
                    $id = $positionTitles[$item[44]];
                    $staffroleCollection->add($this->getReference('staffrole', $id));
                    $staff->setStaffroles($staffroleCollection);
                }

                $userManager->updatePassword($staff);
                $userManager->updateUser($staff);
                $manager->flush();

                $this->addReference('staff', $item[68], $staff);
                $this->addReference('staff', $item[0], $staff);
                $this->progressAdvance();
            }
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    public function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/staff.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }
}
