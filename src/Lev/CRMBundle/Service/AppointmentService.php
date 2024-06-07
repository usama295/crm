<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;
use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Sale;
use App\Lev\CRMBundle\Entity\SaleProduct;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\AppointmentProduct;
use App\Lev\CRMBundle\Traits\ProgressBar;

/**
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class AppointmentService
{
    use ProgressBar;

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function createSaleByAppointment(Appointment $appointment)
    {

        $sale = new Sale;
        $sale
            ->setOffice($appointment->getOffice())
            ->setAppointment($appointment)
            ->setCustomer($appointment->getCustomer())
            ->setPaymentType($appointment->getPaymentType())
            ->setDownPayment($appointment->getDownPayment())
            ->setDiscountPercentage($appointment->getDiscountPercentage())
            ->setDiscountMethod($appointment->getDiscountMethod())
            ->setSalesTax($appointment->getSalesTax())
            ->setSoldOnDate(new \DateTime())
            ;

        /* @var \Lev\CRMBundle\Entity\AppointmentProduct */
        foreach ($appointment->getProducts() as $appProd) {
            $productItem = new SaleProduct;
            $productItem
                ->setSale($sale)
                ->setProduct($appProd->getProduct())
                ->setQuantity($appProd->getQuantity())
                ->setOptions($appProd->getOptions())
                ->setExtras($appProd->getExtras())
                ->setNotes($appProd->getNotes());
            $sale->addProduct($productItem);
        }
        $this->doctrine->getManager()->persist($sale);
        $this->doctrine->getManager()->flush();
        $sale->setSoldPrice(-1);
        $this->doctrine->getManager()->persist($sale);
        $this->doctrine->getManager()->flush();

        return $sale;
    }

    public function uptadeAllJobCeilings($output = null)
    {
        $this->output = $output;
        $offset = 0;
        $end   = false;
        while (!$end) {
            $qb = $this->doctrine->getManager()
                ->getRepository('LevCRMBundle:Appointment')
                ->createQueryBuilder('a')
                ->leftJoin('a.products', 'p')
                ->setFirstResult($offset)
                ->setMaxResults(200);
            $qb->where($qb->expr()->isNotNull('p.id'));
            $appointments = $qb->getQuery()->execute();
            if (count($appointments) === 0) {
                $end = true;
                continue;
            }
            $this->progressStart("Appointment Job Ceiling Costs Update", count($appointments));
            foreach($appointments as $appointment) {
                if (count($appointment->getProducts())) {
                  $appointment->setJobCeiling(1);
                  $this->doctrine->getManager()->persist($appointment);
                }
                $this->progressAdvance();
            }
            $this->doctrine->getManager()->flush();
            $this->doctrine->getManager()->clear();
            $offset += 200;
            $this->progressFinish();
        }
    }
}
