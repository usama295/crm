<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class CustomerControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/customers';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'office' => 'object',
            'gid' => 'string',
            'primaryFirstName' => 'string',
            'primaryLastName' => 'string',
            'secondaryFirstName' => 'string',
            'secondaryLastName' => 'string',
            'secondaryRelationship' => 'string',
            'homeYearBuilt' => 'integer',
            'homeYearPurchased' => 'integer',
            'structureType' => 'string',
            'householdIncome' => 'string',
            'homeValue' => 'string',
            'addressStreet' => 'string',
            'addressCity' => 'string',
            'addressState' => 'string',
            'addressZip' => 'string',
            'addressLat' => 'string',
            'addressLng' => 'string',
            'phone1Number' => 'string',
            'phone1Type' => 'string',
            'phone2Number' => 'string',
            'phone2Type' => 'string',
            'phone3Number' => 'string',
            'phone3Type' => 'string',
            'primaryPhone' => 'string',
            'bestTimeCall' => 'string',
            'email' => 'string',
            'tcpa' => 'boolean',
            'wrongNumber' => 'boolean',
            'restrictionComments' => 'string',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'primaryFirstName' => 'Admin',
                'primaryLastName' => 'Smith',
            )),
            array(array(
                'primaryFirstName' => 'Michael',
                'primaryLastName' => 'Bloomberg',
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 4,
                'primaryFirstName' => 'Admin Other',
                'primaryLastName' => 'Doe',
            )),
            array(array(
                'id' => 5,
                'primaryFirstName' => 'Maxwell',
                'primaryLastName' => 'Smart',
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
        return array(999, 1000);
    }

}
