<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class AppointmentControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/appointments';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'office' => 'object',
//            'customer' => 'object',
            'name' => 'string',
            'addressStreet' => 'string',
            'addressCity' => 'string',
            'addressState' => 'string',
            'addressZip' => 'string',
            'addressLat'=>'string',
            'addressLng'=>'string',
            'productInterest' => 'string',
            'roofAge' => 'string',
            'windowsQty' => 'string',
            'windowsLastReplaced' => 'string',
            'sidesQty' => 'string',
            'type' => 'string',
            //'canceled' => 'boolean',
            'cancelReason' => 'string',
            //'lost' => 'boolean',
            'notes' => 'string',
            'datetime' => 'string', // phpunit doesn't have date
            'duration' => 'string',
            'salesRep' => 'object',
            'status' => 'string',
            'products' => 'array',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'customer' => array('id' => 3),
                'name' => 'Some Appointment',
                'addressStreet' => 'Ap #414-335 Lobortis Street',
                'addressCity' => 'Caruaru',
                'addressState' => 'RS',
                'addressZip' => '93328',
                'addressLat'=>'-37.95802',
                'addressLng'=>'167.03549',
                'productInterest' => 'roofing',
                'roofAge' => '12',
                'windowsQty' => '',
                'windowsLastReplaced' => '',
                'sidesQty' => '',
                'type' => 'rehash',
                'canceled' => 0,
                'cancelReason' => null,
                'lost' => 0,
                'notes' => 'Some note just for you',
                'datetime' => \DateTime::createFromFormat('Y-m-d', '2015-12-28'),
                'duration' => '03:30:00',
                'salesRep' => array('id' => 2),
                'status' => 'pending',
                'products' => array()
            )),
            array(array(
                'customer' => array('id' => 2),
                'name' => 'Another Appointment',
                'addressStreet' => 'Ap #414-335 Lobortis Street',
                'addressCity' => 'Caruaru',
                'addressState' => 'RS',
                'addressZip' => '93328',
                'addressLat'=>'-37.95802',
                'addressLng'=>'167.03549',
                'productInterest' => 'windows',
                'roofAge' => null,
                'windowsQty' => '12',
                'windowsLastReplaced' => '1990',
                'sidesQty' => null,
                'type' => 'rehash',
                'canceled' => 0,
                'cancelReason' => null,
                'lost' => 0,
                'notes' => 'Another note just for you',
                'datetime' => \DateTime::createFromFormat('Y-m-d', '2015-12-28'),
                'duration' => '02:30:00',
                'salesRep' => array('id' => 2),
                'status' => 'pending',
                'products' => array()
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 8,
                'name' => 'Change Appointment',
                'addressStreet' => 'Another place',
                'addressCity' => 'Camobi',
                'productInterest' => 'windows',
                'roofAge' => '',
                'windowsQty' => '8',
                'windowsLastReplaced' => '2010',
            )),
        );

        return $records;
    }

    /**
     * Choose some out of the user's crew
     * @return array
     */
    public function getOneNotFound()
    {
        return array(999);
    }

}
