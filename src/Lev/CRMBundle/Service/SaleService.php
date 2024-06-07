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
use App\Lev\CRMBundle\Traits\ProgressBar;

/**
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class SaleService
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

    public function uptadeSoldPrice(Sale $sale)
    {
        if(
            ($sale->getSoldPrice() === null || $sale->getSoldPrice() === 0)
            && count($sale->getProducts())
        ) {
            $sale->setSoldPrice(1);
            $this->doctrine->getManager()->persist($sale);
            $this->doctrine->getManager()->flush();
        }

        return $sale;
    }

    public function uptadeAllSoldSales($output = null)
    {
      $this->output = $output;
      $offset = 0;
      $end   = false;
      while (!$end) {
          $qb = $this->doctrine->getManager()
              ->getRepository('LevCRMBundle:Sale')
              ->createQueryBuilder('s')
              ->leftJoin('s.products', 'p');
          $qb
              ->where($qb->expr()->isNotNull('p.id'))
              ->setFirstResult($offset)
              ->setMaxResults(200);
          $sales = $qb->getQuery()->execute();
          if (count($sales) === 0) {
              $end = true;
              continue;
          }
          $this->progressStart("Sale Job Ceiling Costs Update", count($sales));
          foreach($sales as $sale) {
              if (count($sale->getProducts())) {
                $sale->setSoldPrice(0);
                $this->uptadeSoldPrice($sale);
              }
              $this->progressAdvance();
          }
          $this->doctrine->getManager()->clear();
          $offset += 200;
          $this->progressFinish();
      }

    }
}
