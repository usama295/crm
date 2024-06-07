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

/**
 * @RouteResource("Callrecord")
 */
class CallRecordController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\CallRecord';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'office', 'exposed' => true, 'saved' => false, 'filter'=>'objectid','object'=>'\App\Lev\CRMBundle\Entity\Office'),
            
            array('name' => 'customer', 'exposed' => true, 'saved' => true, 'filter'=>'objectid_in', 'object'=>'\App\Lev\CRMBundle\Entity\Customer'),
            array('name' => 'callStatus', 'exposed' => true, 'saved' => true, 'filter' => 'string_search'),
            
            array('name' => 'customerID', 'exposed' => true, 'saved' => false),
            array('name' => 'appointment', 'exposed' => true, 'saved'=>true,'filter'=>'objectid_in','object'=>'\App\Lev\CRMBundle\Entity\Appointment'),
            array('name' => 'appointmentID', 'exposed' => true, 'saved' => true),
            array('name' => 'callSid', 'exposed' => true, 'saved' => true),
            array('name' => 'fromNumber', 'exposed' => true, 'saved' => true),
            array('name' => 'toNumber', 'exposed' => true, 'saved' => true),
            array('name' => 'recordingUrl', 'exposed' => true, 'saved' => false),
            array('name' => 'recordingSid', 'exposed' => true, 'saved' => false, 'filter' => 'string'),
            array('name' => 'recordingDuration', 'exposed' => true, 'saved' => false),
            array('name' => 'timestamp', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'callbackSource', 'exposed' => true, 'saved' => false),
            array('name' => 'sequenceNumber', 'exposed' => true, 'saved' => false),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => false,'filter'=>'objectid', 'object'=>'\App\Lev\CRMBundle\Entity\Staff'),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('callrecord')
            ->setQuerySort(array(
                'timestamp' => 'DESC'
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
          ->innerJoin('e.appointment', 'appointment')
          ->leftJoin('e.createdBy', 'createdBy');

        return $qb;
    }

}
