<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class ProductControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/products';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'name' => 'string',
            'type' => 'string',
            'costMethod' => 'string',
//            'costAmount' => 'float',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'name' => 'Bullet',
                'type' => 'good',
                'costMethod' => 'sqft',
                'costAmount' => 2.4,
            )),
            array(array(
                'name' => 'Sharpening',
                'type' => 'labor',
                'costMethod' => 'fixed',
                'costAmount' => 250,
            )),
        );

        return $records;
    }


    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id'  => 11,
                'name' => 'Bullet',
                'type' => 'good',
                'costMethod' => 'sqft',
                'costAmount' => 2.4,
            )),
            array(array(
                'id'  => 12,
                'name' => 'Sharpening',
                'type' => 'labor',
                'costMethod' => 'fixed',
                'costAmount' => 250,
            )),
        );

        return $records;
    }

    /**
     * Choose some out of the user's office
     * @return array
     */
    public function getOneNotFound()
    {
        return array(500, 501, 502);
    }

}
