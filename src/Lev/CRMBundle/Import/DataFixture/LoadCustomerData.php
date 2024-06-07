<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Customer;
use Doctrine\Common\Collections\ArrayCollection;

class LoadCustomerData extends AbstractDataFixture implements DataFixtureInterface
{

    protected $options = array(
      'phoneTypes' => array(
          'default'          => null,
          'Primary Work'     => 'work',
          'Home'             => 'home',
          'Primary Mobile'   => 'mobile',
          'Secondary Mobile' => 'mobile',
      ),
      'relationships' => array(
          'default'                 => null,
          'Spouse'                  => 'spouse',
          'Fiancé'                  => 'fiance',
          'Significant Other'       => 'significant-other',
          'Son or Daughter'         => 'parent',
          'Parent'                  => 'parent',
          'Sibling'                 => 'sibling',
          'Other Relative'          => 'other',
          'Girlfriend or Boyfriend' => 'other',
          'Co-Owner'                => 'other',
          'Co-Habitant'             => 'other',
      ),
      'structureTypes' => array(
          'default' => null,
          'Brick' => 'brick',
          'Ranch' => 'ranch',
          'Split-Level' => 'split-level',
          'Two-Story' => 'two-story',
          'Town House' => 'town-house',
          // maybe set to multiple?
          'Brick;Ranch' => 'brick',
          'Brick;Split-Level' => 'brick',
          'Brick;Two-Story' => 'brick',
          'Split-Level;Two-Story' => 'brick',
          // '' => 'stone',
          // '' => 'stucco',
          // '' => 'frame',
          // '' => 'condominium',
          // '' => 'mobile-home',
          // '' => 'modular',
          // '' => 'duplex',
          // '',
          // '',
      ),
      // 'bestTimeCalls' => array(
      //   'default' => null,
      //     '' => null,
      //     'morning',
      //     'afternoon',
      //     'evening',
      //     'weekdays',
      //     'weekends'
      // ),
      'homeValues' => array(
          'default' => null,
          // '<50k',
          // '50k-99k',
          // '100k-199k',
          // '200k-299k',
          // '300k-399k',
          // '400k-499k',
          'Over $500K' => '500k+'
      ),
    );

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->loadFromCSV();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 5;
    }

    public function loadFromCSV()
    {
        $manager = $this->getManager();
        $generateIdService = $this->getContainer()->get('lev_crm.service.customer_generate_id');
        $count     = 0;
        $maincount = 0;
        $tempCustomer = array();
        try {
            $customers = $this->getCSV();
            $this->progressStart('customer', count($customers));
            foreach ($customers as $item) {
                $createdBy = $this->getReferenceOrDefault('staff', $item[15]);
                $customer = new Customer();
                $customer->setCreatedBy($createdBy);
                $customer->setCreatedAt(new \DateTime($item[16]));
                $customer
                    ->setSalesforceId($item[0])
                    ->setOffice($this->getObjectOrDefault('office', 1))
                    ->setPrimaryFirstName($item[79])
                    ->setPrimaryLastName($item[80])
                    ->setSecondaryFirstName(!empty($item[94])? $item[94] : null)
                    ->setSecondaryLastName(!empty($item[95])? $item[95] : null)
                    ->setSecondaryRelationship(
                        !empty($item[94]) && !empty($item[91])
                            ? $this->getOption('relationships', $item[91])
                            : null
                    )
                    ->setHomeYearBuilt(!empty($item[105])? $item[105] : null)
                    ->setHomeYearPurchased(!empty($item[106])? $item[106] : null)
                    // Maybe set to multiple?
                    // ->setStructureType($structureTypes[$item[100]])
                    // ->setHouseholdIncome($householdIncome)
                    ->setHomeValue(
                        !empty($item[48])
                            ? $this->getOption('homeValues', $item[48])
                            : null
                    )
                    ->setAddressStreet(!empty($item[1]) ? $item[1] : null)
                    ->setAddressCity(!empty($item[11]) ? $item[11] : null)
                    ->setAddressState(!empty($item[99]) ? $item[99] : null)
                    ->setAddressZip(!empty($item[107]) ? $item[107] : null)
                    ->setAddressLat(!empty($item[58]) ? $item[58] : null)
                    ->setAddressLng(!empty($item[62]) ? $item[62] : null)
                    ->setPhone1Number(!empty($item[72]) ? $item[72] : null)
                    ->setPhone1Type(
                        !empty($item[72])
                            ? $this->getOption('phoneTypes', $item[73])
                            : null
                    )
                    ->setPhone2Number(!empty($item[74]) ? $item[74] : null)
                    ->setPhone2Type(
                        !empty($item[74])
                            ? $this->getOption('phoneTypes', $item[75])
                            : null
                    )
                    ->setPhone3Number(!empty($item[76]) ? $item[76] : null)
                    ->setPhone3Type(
                        !empty($item[76])
                            ? $this->getOption('phoneTypes', $item[77])
                            : null
                    )
                    // ->setPrimaryPhone($primaryPhone)
                    // Maybe set to multiple?
                    // ->setBestTimeCall(!empty($item[5]) ? $phoneTypes[$item[5]] : null)
                    ->setEmail(!empty($item[78])? $item[78] : null)
                    ->setTcpa($item[102] === 'true')
                    ->setWrongNumber($item[104] === 'true')
                    // Maybe set to multiple?
                    // ->setRestrictionComments(!empty($item[92]) ? $item[92] : null)
                    ;

                $maincount++;
                $gid = $generateIdService->getCustomGeneratedId($customer, $maincount);
                $customer->setGid($gid);
                $manager->persist($customer);

                $count++;
                if ($count === 1) {
                  $manager->flush();
                  $this->setDefault('customer', $customer);
                }
                if ($count === 150) {
                  $manager->flush();
                  // $manager->clear();
                  $count = 0;
                }
                $this->progressAdvance();
            }
            $manager->flush();
            $manager->clear();
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    protected function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/customer.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }
}
