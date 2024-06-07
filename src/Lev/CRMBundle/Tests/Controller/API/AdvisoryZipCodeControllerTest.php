<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class AdvisoryZipCodeControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/advisoryzipcodes';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'zipCode' => 'string',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'zipCode' => '99524',
            )),
            array(array(
                'zipCode' => '32675',
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 4,
                'zipCode' => '85522',
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
