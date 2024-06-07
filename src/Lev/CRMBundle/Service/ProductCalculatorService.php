<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
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
class ProductCalculatorService
{
    use ProgressBar;

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, Logger $logger)
    {
        $this->doctrine = $doctrine;
        $this->logger   = $logger;
    }

    public function updateProducts($record, $data)
    {
        if (!($record instanceof Appointment || $record instanceof Sale)) {
            return $record;
        }

        $toRemove = array();
        foreach ($record->getProducts() as $productItem) {
            $toRemove[$productItem->getId()] = $productItem;
        }

        if (array_key_exists('products', $data) && !empty($data['products'])) {
            foreach ($data['products'] as $prod) {
                if (is_object($prod)){
                    $prod = get_object_vars($prod);
                }

                if ($record instanceof Sale) {
                    $productItem = (array_key_exists('id', $prod) && !empty($prod['id']))
                        ? $this->getSaleProductById($prod['id'])
                        : new SaleProduct();
                }
                if ($record instanceof Appointment) {
                    $productItem = (array_key_exists('id', $prod) && !empty($prod['id']))
                        ? $this->getAppointmentProductById($prod['id'])
                        : new AppointmentProduct();
                }

                if ($productItem->getId()) {
                    unset($toRemove[$productItem->getId()]);
                }

                $optionsArray = array();
                if(array_key_exists('options', $prod)) {
                    foreach ($prod['options'] as $opt) {
                        $option = $this->getProductOptionById($opt['id'])->toArrayShort();
                        if (array_key_exists('value', $opt)) {
                            $option['value'] = $this->getProductOptionValueById($opt['value']['id'])->toArray();
                            $optionsArray[]  = $option;
                        }
                    }
                }

                if ($record instanceof Sale) {
                    $productItem->setSale($record);
                }
                if ($record instanceof Appointment) {
                    $productItem->setAppointment($record);
                }

                $product = $this->getProductById($prod['product']['id']);
                $productItem
                    ->setProduct($product)
                    ->setQuantity(array_key_exists('quantity', $prod) ? $prod['quantity'] : 1)
                    ->setOptions($optionsArray)
                    ->setExtras(array_key_exists('extras', $prod) ? $prod['extras'] : array())
                    ->setNotes(array_key_exists('notes', $prod) ? $prod['notes'] : '');
                if ($productItem->getId()) {
                    $this->doctrine->getManager()->persist($productItem);
                } else {
                    $record->addProduct($productItem);
                }
            }
        }

        foreach($toRemove as $productItem) {
            $this->doctrine->getManager()->remove($productItem);
            $record->removeProduct($productItem);
        }

        return $record;
    }

    /**
     * @param $entityName
     * @param $value
     * @return null|object
     */
    public function getById($entityName, $value)
    {
        if(is_object($value)) {
            $value = $value->id;
        }
        if(is_array($value) && array_key_exists('id', $value)) {
            $value = $value['id'];
        }

        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:' . $entityName)
            ->findOneBy(array('id' => $value));
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Product
     */
    protected function getProductById($value)
    {
        return $this->getById('Product', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductExtraById($value)
    {
        return $this->getById('ProductExtra', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductOptionById($value)
    {
        return $this->getById('ProductOption', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductOptionValueById($value)
    {
        return $this->getById('ProductOptionValue', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\SaleProduct
     */
    protected function getSaleProductById($value)
    {
        return $this->getById('SaleProduct', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\AppointmentProduct
     */
    protected function getAppointmentProductById($value)
    {
        return $this->getById('AppointmentProduct', $value);
    }

    /**
     * UptadeAllCosts
     * @return integer
     */
    public function uptadeAllCosts($output = null)
    {
        $this->output = $output;
        $prods = $this->doctrine->getManager()
          ->getRepository('LevCRMBundle:AppointmentProduct')
          ->findAll();
        $this->progressStart('Appointment Product Costs Update', count($prods));
        foreach($prods as $prod) {
            $prod->setCost(1);
            $this->doctrine->getManager()->persist($prod);
            $this->progressAdvance();
        }
        $this->doctrine->getManager()->flush();
        $count = count($prods);
        $this->progressFinish();

        $prods = $this->doctrine->getManager()
          ->getRepository('LevCRMBundle:SaleProduct')
          ->findAll();
        $this->progressStart('Sale Product Costs Update', count($prods));
        foreach($prods as $prod) {
            $prod->setCost(1);
            $this->doctrine->getManager()->persist($prod);
            $this->progressAdvance();
        }
        $this->doctrine->getManager()->flush();
        $count += count($prods);
        $this->progressFinish();
    }
}
