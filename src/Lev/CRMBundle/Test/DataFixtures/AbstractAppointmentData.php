<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Sale;

abstract class AbstractAppointmentData extends AbstractFixture implements OrderedFixtureInterface
{
    static public $usedAppointments = array();
    static public $soldAppointments = array();
    static public $usedSales = array();
    static public $totalSales = 0;

    /**
     * @param null $officeId
     * @return \Lev\CRMBundle\Entity\Staff
     */
    public function getRandomStaff($officeId = null)
    {
        $min = 1;
        $max = 102;
        $exc = array(1, 2, 10, 11);
        return $this->getRandomObject('staff', $min, $max, $exc, $officeId);
    }

    /**
     * @param null $officeId
     * @return \Lev\CRMBundle\Entity\Customer
     */
    public function getRandomCustomer($officeId = null)
    {
        $min = 1;
        $max = 350;
        $exc = array(4, 5);
        return $this->getRandomObject('customer', $min, $max, $exc, $officeId);
    }

    /**
     * @param null $officeId
     * @return \Lev\CRMBundle\Entity\Appointment
     */
    public function getRandomAppointment($officeId = null)
    {
        $min = 1;
        $max = 200;
        $exc = array_unique(array_merge(array(8), self::$usedAppointments));
        $object = $this->getRandomObject('appointment', $min, $max, $exc, $officeId);
        $exc[] = $object->getId();
        self::$usedAppointments[] = array_unique($exc);
        return $object->getStatus() === 'sold' ? $object : $this->getRandomAppointment($officeId);
    }

    /**
     * @param null $officeId
     * @return \Lev\CRMBundle\Entity\Sale
     */
    public function getRandomSale($officeId = null)
    {
        $min = 1;
        $max = self::$totalSales;
        $exc = array_unique(array_merge(array(7), self::$usedSales));
        $object = $this->getRandomObject('sale', $min, $max, $exc, $officeId);
        $exc[] = $object->getId();
        self::$usedSales[] = array_unique($exc);
        return $object;
    }

    /**
     * @param string $type
     * @param int $min
     * @param int $max
     * @param array $exc
     * @param $officeId
     * @return int
     */
    protected function getRandomObject($type, $min, $max, array $exc, $officeId = null)
    {
        $object = $this->getReference($type . $this->getRandom($min, $max, $exc));

        if (null !== $officeId) {
            if ($object->getOffice()->getId() !== $officeId) {
                return $this->getRandomObject($type, $min, $max, $exc, $officeId);
            }
        }

        return $object;
    }

    /**
     * @param $min
     * @param $max
     * @param array $exc
     * @return int
     */
    protected function getRandom($min, $max, array $exc)
    {
        $random = rand($min, $max);
//        $test = !in_array($random, $exc) ? $random : 0;
//        echo "( $random, $test ) => $min, $max " . print_r($exc, true). PHP_EOL;
        return !in_array($random, $exc) ? $random : $this->getRandom($min, $max, $exc);
    }

}