<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class StaffControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/staffs';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'firstName' => 'string',
            'lastName' => 'string',
            'email' => 'string',
            'office'  => 'object',
            'enabled' => 'boolean',
            'superAdmin' => 'boolean',
            //'roles' => 'array',
            //'staffroles' => 'array',
            'positionTitle' => 'string',
            'employmentType' => 'string',
            'employmentDateStart' => 'object', // phpunit doesn't have date
            'employmentDateEnd' => 'object', // phpunit doesn't have date
            'salesCapabComp' => 'string',
            'projectCapabComp' => 'string',
            'certifiedRenovator' => 'string',
            'lswpJobTraining' => 'string',
            'certificationId' => 'string',
            'certificationExpiration' => 'string',
            'addressStreet' => 'string',
            'addressCity' => 'string',
            'addressState' => 'string',
            'addressZip' => 'string',
            'addressLat' => 'string',
            'addressLng' => 'string',
            'phoneHome' => 'string',
            'phoneMobile' => 'string',
            'phoneWork' => 'string',
            'emergencyContactName' => 'string',
            'emergencyContactPhone' => 'string',
            'emergencyContactRelation' => 'string',
            'driverLicenceNumber' => 'string',
            'driverLicenceState' => 'string',
            'driverLicenceExpiration' => 'string',
            'autoLiabInsProvider' => 'string',
            'autoLiabInsCoverage' => 'string',
            'autoLiabInsExpiration' => 'string',
            'workersCompInsProvider' => 'string',
            'workersCompInsCoverage' => 'string',
            'workersCompInsExpiration' => 'object', // phpunit doesn't have date
            'liabInsProvider' => 'string',
            'liabInsCoverage' => 'string',
            'liabInsExpiration' => 'object', // phpunit doesn't have date
            'password' => 'string',
            'confirm_password' => 'string',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'roles' => array(),
                'firstName' => 'Admin',
                'lastName' => 'Sample',
                'email' => 'adminsample@test.com',
                'staffroles' => array(2, 3),
                'password' => 'blah',
                'confirm_password' => 'blah',
            )),
            array(array(
                'roles' => array(),
                'firstName' => 'Michael',
                'lastName' => 'Sample',
                'email' => 'michael@test.com',
                'staffroles' => array(3),
                'password' => 'blah',
                'confirm_password' => 'blah',
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 10,
                'firstName' => 'Admin Other',
                'staffroles' => array(3, 4),
            )),
        );
        $records = array(
            array(array(
                'id' => 11,
                'firstName' => 'John Other',
                'staffroles' => array(3, 4),
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
        return array(999, 1900, 2000);
    }

}
