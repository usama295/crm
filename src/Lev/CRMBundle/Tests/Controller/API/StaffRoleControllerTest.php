<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPICRUDControllerTestCase;

class StaffRoleControllerTest extends BaseAPICRUDControllerTestCase
{
    public function getUri()
    {
        return '/api/v1/staffroles';
    }

    public function getExposedFields()
    {
        $exposedFields = array(
            'id' => 'integer',
            'name' => 'string',
            'roles' => 'array',
            'superadmin' => 'boolean',
        );
        return $exposedFields;
    }

    public function samplePostProvider()
    {
        $records = array(
            array(array(
                'name' => 'New Role Admin',
                'roles' => array('STAFF_VIEW', 'OFFICE_EDIT'),
                'superadmin' => true,
            )),
            array(array(
                'name' => 'New Role Common',
                'roles' => array('STAFF_VIEW', 'OFFICE_EDIT'),
                'superadmin' => false,
            )),
        );

        return $records;
    }

    public function sampleUpdateProvider()
    {
        $records = array(
            array(array(
                'id' => 5,
                'name' => 'Updated Role',
                'roles' => array('STAFF_VIEW', 'OFFICE_EDIT'),
                'superadmin' => false,
            )),
            array(array(
                'id' => 6,
                'name' => 'Updated Role Again',
                'roles' => array('STAFF_VIEW', 'OFFICE_EDIT'),
                'superadmin' => false,
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
