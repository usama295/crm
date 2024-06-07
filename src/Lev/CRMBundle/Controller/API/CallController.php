<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use App\Lev\CRMBundle\Entity\Call;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;
/**
 * @RouteResource("Call")
 */
class CallController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Call';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'office', 'exposed' => true, 'saved' => false, 'filter' => 'objectid',
                'object' => '\App\Lev\CRMBundle\Entity\Office'),
            array('name' => 'customer', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Customer'),
            array('name' => 'customerID', 'exposed' => true, 'saved' => false),
            array('name' => 'appointment', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Appointment'),
            array('name' => 'appointmentID', 'exposed' => true, 'saved' => false),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'historyNote', 'exposed' => true, 'saved' => false),
            array('name' => 'outcome', 'exposed' => true, 'saved' => true, 'filter' => 'string_in'),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('call')
            ->setQuerySort(array(
                'datetime' => 'DESC'
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
      $qb = $this->getRepository()->createQueryBuilder('e');
      $qb->innerJoin('e.office', 'office')
          ->innerJoin('e.customer', 'customer')
          ->leftJoin('e.appointment', 'appointment')
          ->innerJoin('appointment.customer', 'appcustomer')
          ->leftJoin('appointment.salesRep', 'appsalesRep')
          ->leftJoin('appointment.marketingRep', 'appmarketingRep')
          ->innerJoin('e.createdBy', 'createdBy');

        return $qb;
    }

}
