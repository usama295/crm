<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Customer;

/**
 * Customer Generate Id Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class CustomerGenerateId
{

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

    /**
     * Generate ID for a Customer
     *
     * @param Customer $customer The customer
     *
     * @return string
     * @throws \Exception
     */
    public function generate(Customer $customer, $flush = true)
    {
        if (!$customer->getId()) {
            throw new \Exception(
                'Impossible to generate a GID for a non-persisted customer'
                , 200
            );
        }

        $gid = $this->getGeneratedId($customer);
        $customer->setGid($gid);

        $em = $this->doctrine->getManager();
        $em->persist($customer);
        if ($flush) {
            $em->flush();
        }

        return $customer;
    }

    /**
     * Get GenearetedId
     * @param Customer $customer The customer
     *
     * @return string
     */
    public function getGeneratedId(Customer $customer)
    {
        $gid = str_pad($customer->getOffice()->getId(), 4, '0', STR_PAD_LEFT)
           . str_pad($customer->getId(), 8, '0', STR_PAD_LEFT);

        return $gid;
    }

    /**
     * Get Custom GeneratedId
     * @param Customer $customer The customer
     * @param string   $id       The Id
     *
     * @return string
     */
    public function getCustomGeneratedId(Customer $customer, $id)
    {
        $id = (string) $id;
        $gid = str_pad($customer->getOffice()->getId(), 4, '0', STR_PAD_LEFT)
           . str_pad($id, 8, '0', STR_PAD_LEFT);

        return $gid;
    }

}
