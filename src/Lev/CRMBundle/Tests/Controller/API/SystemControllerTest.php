<?php

namespace App\Lev\CRMBundle\Tests\Controller\API;

use App\Lev\CRMBundle\Test\BaseAPIControllerTestCase;

class SystemControllerTest extends BaseAPIControllerTestCase
{

    public function getUri()
    {
        return '/api/v1/system';
    }

    public function testGetPermissions()
    {
        list($client, $dataResponse) = $this->request('/permissions', 'GET', array());
        $dataArray = get_object_vars($dataResponse);

        $headers = array(
            'Admin',
            'Marketing Representative',
            'Call Center Representative',
            'Sales Representative'
        );

        $permissionsTests = array(
            'ROLE_STAFF' => array(0, 1, 1, 0),
            'ROLE_OFFICE' => array(0, 0, 1, 1),
            'ROLE_PRODUCT_VIEW' => array(0, 1, 0, 1),
            'ROLE_STAFFROLE' => array(0, 0, 0, 1),
        );

        foreach($dataArray['headers'] as $header) {
            $this->assertTrue(in_array($header, $headers), 'Not in headers list: ' . $header);
        }

        foreach($dataArray['permissions'] as $permission) {
            if (array_key_exists($permission->name, $permissionsTests)) {
                $this->assertTrue(
                    $permission->records === $permissionsTests[$permission->name],
                    'Permission is wrong: ' . $permission->name
                );
            }
        }
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testPutPermissions()
    {

        list($client, $dataResponse) = $this->request('/permissions', 'GET', array());
        $dataArray = get_object_vars($dataResponse);

        $permissionsTests = array(
            'ROLE_ADMIN' => array(1, 0, 1, 0),
            'ROLE_STAFF' => array(1, 1, 0, 0),
            'ROLE_OFFICE' => array(1, 0, 0, 1),
            'ROLE_PRODUCT_VIEW' => array(1, 0, 0, 1),
            'ROLE_STAFFROLE' => array(1, 0, 0, 1),
        );

        foreach($dataArray['permissions'] as $key => $permission) {
            if (in_array($permission->name, array_keys($permissionsTests))) {
                $dataArray['permissions'][$key]->records = $permissionsTests[$permission->name];
            }
        }

        list($client, $dataResponse) = $this->request('/permissions', 'PUT', $dataArray);
        $dataArray = get_object_vars($dataResponse);

        foreach($dataArray['permissions'] as $permission) {
            if (array_key_exists($permission->name, $permissionsTests)) {
                $this->assertTrue(
                    $permission->records === $permissionsTests[$permission->name],
                    'Permission is wrong: ' . $permission->name
                );
            }
        }
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
