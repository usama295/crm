<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class InstitutionControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/institutions';
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
                'name' => 'Goulart Programming, Inc.',
            )),
            array(array(
                'name' => 'Rodrigues Management, Inc.',
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 4,
                'name' => 'Old School, Inc.',
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
