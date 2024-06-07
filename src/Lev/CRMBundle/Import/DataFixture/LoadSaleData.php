<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Sale;
use Doctrine\Common\Collections\ArrayCollection;

class LoadSaleData extends AbstractDataFixture implements DataFixtureInterface
{

    protected $options = array(
        // "cash", "credit-card", "financing", "other"
        'paymentTypes' => array(
            ''            => null,
            'default'     => null,
            'Cash'        => 'cash',
            'Credit Card' => 'credit-card',
            'Financing'   => 'financing',
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
        return 7;
    }

    public function loadFromCSV()
    {

        $manager = $this->getManager();
        $count = 0;
        $sales = $this->getCSV();

        // print_r($this->options['paymentTypes']). PHP_EOL;

        $this->progressStart('sales', count($sales));
        try {
            foreach ($this->getCSV() as $item) {
                // echo "'{$item[57]}' => " . $this->getOption('paymentTypes', $item[57]) . PHP_EOL;
                $count++;
                $createdBy = $this->getObjectBySalesforceUserId('staff', $item[24]);
                if (null === $createdBy) {
                  $createdBy = $this->getObject('staff', 1);
                }
                $appointment = $this->getObjectBySalesforceId('appointment', $item[1]); // ProspectId
                if (null === $appointment) {
                  echo "null appointment found: {$item[1]} on sale id {$item[0]}";
                  continue;
                }
                $sale = new Sale();
                $sale->setCreatedBy($createdBy);
                $sale->setCreatedAt(new \DateTime($item[25]));
                $sale
                    ->setSalesforceId($item[0])
                    ->setOffice($appointment->getOffice())
                    ->setSalesTax($item[93])
                    ->setPaymentType($this->getOption('paymentTypes', $item[57]))
                    ->setJobCeiling($item[101])
                    ->setStatus($this->getStatus($item[106], $item[107]))
                    ->setSoldOnDate(new \DateTime($item[99]))
                    ->setNetOnDate(!empty($item[52]) ? new \DateTime($item[52]) : null)
                    ->setNetOnDate(null)
                    ->setPaidDate(!empty($item[56]) ? new \DateTime($item[56]) : null)
                    ->setCustomer($appointment->getCustomer())
                    ->setAppointment($appointment)
                    ->setDiscountMethod('daily-price')
                    ;
                $manager->persist($sale);
                $appointment->setStatus('sold');
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
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    protected function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/sale.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }

    public function getStatus($status, $statusDetail)
    {
        // "approved", "on-hold", "completed", "declined", "canceled"
        switch($statusDetail) {
          case 'Declined':
          case 'Declined & Rescinded':
              $return = 'declined'; break;

          case 'On Hold':
          case 'Pending Loan Approval':
          case 'New':
              $return = 'on-hold'; break;

          case 'Approved':
              $return = 'approved'; break;

          case 'Rescinded':
          case 'Management':
          default:
              $return = 'canceled';
        }

        if ($status === 'Final') {
            $return = 'completed';
        }

        return $return;
    }

}
