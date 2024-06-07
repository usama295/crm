<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Appointment;
use Doctrine\Common\Collections\ArrayCollection;

class LoadAppointmentData extends AbstractDataFixture implements DataFixtureInterface
{

    protected $options = array(
        // "rehash", "reset", "new", "vivint", "follow-ups", "cancel-save"
        'types' => array(
            'default'               => 'new',
            // 'Retry'                 => 'new',
            'Follow-up Appointment' => 'follow-ups',
            'Follow-up'             => 'follow-ups',
            'New'                   => 'new',
            'Rehash'                => 'rehash',
            'Reset'                 => 'reset',
        ),
        // "pending", "scheduled", "issued", "no-pitch", "confirmed", "sold", "pitch-miss", "canceled"
        'status' => array(
            'default'               => 'pending',
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
        return 6;
    }

    public function loadFromCSV()
    {

        $manager = $this->getManager();
        $count = 0;
        $appointments = $this->getCSV();
        $this->progressStart('appointments', count($appointments));
        try {
            foreach ($this->getCSV() as $item) {
                $count++;
                $createdBy = $this->getObjectBySalesforceUserId('staff', $item[25]);
                // $createdBy = $this->getReferenceOrDefault('staff', $item[25]);
                $customer = $this->getObjectBySalesforceId('customer', $item[91]); // ProspectId
                if (null === $customer) {
                  $customer = $this->getObject('customer', 2);
                  // $customer = $this->getDefaultReference('customer');
                }
                $salesRep = !empty($item[116])
                    ? $this->getObjectBySalesforceId('staff', $item[116])
                    // ? $this->getReferenceOrDefault('staff', $item[116])
                    : null;

                $appointment = new Appointment();
                $appointment->setCreatedBy($createdBy);
                $appointment->setCreatedAt(new \DateTime($item[26]));
                $appointment
                    ->setSalesforceId($item[0])
                    ->setOffice($customer->getOffice())
                    ->setProductInterest(array(strtolower($item[14])))
                    // Next 4 can be got with a BIG effort from 47 Interesed In
                    // ->setRoofAge($roofAge) ??
                    // ->setWindowsQty($windowsQty)
                    // ->setWindowsLastReplaced($windowsLastReplaced)
                    // ->setSidesQty($sidesQty)
                    ->setType($this->getOption('types', $item[144])) // Check 144 - some not match
                    ->setNotes($item[13]) // ??
                    ->setDatetime(new \DateTime($item[130])) // 130
                    ->setCustomer($customer)
                    ->setSalesRep($salesRep)
                    ->setAddressStreet($item[2])
                    ->setAddressCity($item[10])
                    ->setAddressState($item[131])
                    ->setAddressZip($item[148])
                    ->setAddressLat(!empty($item[54]) ? $item[54] : null)
                    ->setAddressLng(!empty($item[66]) ? $item[66] : null)
                    ->setStatus($item[8] === 'true' ? 'canceled' : 'archived') // Check 132 - maybe + 8
                    ->setCancelReason(null) // ??
                    ;

                $manager->persist($appointment);
                $count++;
                if ($count === 150) {
                  $manager->flush();
                  $manager->clear();
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
        $file = file(__DIR__ . '/../../../../../importData/appointment.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }
}
