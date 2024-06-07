<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class SaleControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/sales';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'appointment' => 'object',
            'salesTax' => 'string',
            'amountDue' => 'string',
            'amountOwned' => 'string',
            'paymentType' => 'string',
            //'discount' => 'string',
            'jobCeiling' => 'string',
            'notes' => 'string',
            //'attachments' => 'string',
            //'products' => 'array',
            'status' => 'string',
            'soldOnDate' => 'string', // phpunit doesn't have date
            'netOnDate' => 'string', // phpunit doesn't have date
            'paidDate' => 'string', // phpunit doesn't have date
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'appointment' => array('id' => 5 ),
                'salesTax' => 10.5,
                'amountOwned' => 'Some amount',
                'paymentType' => 'cash',
                'discount' => 0.0,
                'notes' => 'Some awesome notes',
                //'attachments' => 'string',
                'products' => array(
                    array('productId' => 1, 'quantity' => 10),
                    array('productId' => 5, 'quantity' => 20),
                    array('productId' => 3, 'quantity' => 30.5),
                ),
                'status' => 'on-hold',
                'soldOnDate' => null, // phpunit doesn't have date
                'netOnDate' => null, // phpunit doesn't have date
            )),
            array(array(
                'appointment' => array('id' => 6 ),
                'salesTax' => 10.5,
                'amountOwned' => 'Some amount',
                'paymentType' => 'cash',
                'discount' => 0.0,
                'notes' => 'Some awesome notes',
                //'attachments' => 'string',
                'products' => array(
                    array('productId' => 1, 'quantity' => 10),
                    array('productId' => 5, 'quantity' => 20),
                    array('productId' => 3, 'quantity' => 30.5),
                ),
                'status' => 'on-hold',
                'soldOnDate' => null, // phpunit doesn't have date
                'netOnDate' => null, // phpunit doesn't have date
            )),
            array(array(
                'appointment' => array('id' => 7 ),
                'salesTax' => 10.5,
                'amountOwned' => 'Some amount',
                'paymentType' => 'cash',
                'discount' => 0.0,
                'notes' => 'Some awesome notes',
                //'attachments' => 'string',
                'products' => array(
                    array('productId' => 1, 'quantity' => 10),
                    array('productId' => 5, 'quantity' => 20),
                    array('productId' => 3, 'quantity' => 30.5),
                ),
                'status' => 'on-hold',
                'soldOnDate' => null, // phpunit doesn't have date
                'netOnDate' => null, // phpunit doesn't have date
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 7   ,
                'amountOwned' => 'Changed amount',
                'status' => 'confirmed',
                'discount' => 15.0,
                'products' => array(
                    array('id' => 2, 'productId' => 10, 'quantity' => 100),
                    array('productId' => 10, 'quantity' => 200),
                    array('productId' => 9, 'quantity' => 300),
                ),
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

    public function deleteTest($record)
    {
        return false;
    }



}
