<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class OfficeControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/offices';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'name' => 'string',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'name' => 'San Francisco CA',
            )),
            array(array(
                'name' => 'San Diego',
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 4,
                'name' => 'New San Francisco',
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
        return array(999);
    }



}
