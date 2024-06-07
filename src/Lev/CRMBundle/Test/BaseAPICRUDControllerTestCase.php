<?php

namespace App\Lev\CRMBundle\Test;

abstract class BaseAPICRUDControllerTestCase extends BaseAPIControllerTestCase
{
    static public $accessToken;
    static public $refreshToken;
    static public $fixturesLoaded = false;

    abstract public function getExposedFields();
    abstract public function samplePostProvider();
    abstract public function sampleUpdateProvider();

    /**
     * Must check access to each endpoint
     * ROLE_*_VIEW, ROLE_*_UPDATE, ROLE_*_CREATE, ROLE_*_DELETE
     * @todo
     */
//    public function testAccessDenied()
//    {
//        $this->assertTrue(false);
//    }

    /**
     * @dataProvider samplePostProvider
     * @param $record
     */
    public function testPostOk($record)
    {
        list($client, $dataResponse) = $this->request('', 'POST', $record);
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 201);

        foreach ($this->getExposedFields() as $field => $type) {
            if (array_key_exists($field, $dataArray)) {
                $this->assertArrayHasKey($field, $dataArray, "Field '$field'");
                if (
                    !is_object($dataArray[$field])
                    && !is_object($dataArray[$field])
                    && !is_array($dataArray[$field])
                    && !is_array($dataArray[$field])
                ) {
                    $this->assertInternalType($type, $dataArray[$field], "Field '$field'");
                }
            }
        }
    }

    /**
     * @return bool
     */
    public function testGet()
    {
        list($client, $dataResponse) = $this->request('', 'GET', array());
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 200);

        $this->assertArrayHasKey('pagination', $dataArray);
        $this->assertArrayHasKey('results', $dataArray);
        $this->assertInternalType('array', $dataArray['results']);
        $pagination = get_object_vars($dataArray['pagination']);
        $fields = array(
            'paginate' => 'boolean',
            'total' => 'integer',
            'limit' => 'integer',
            'pages' => 'integer',
            'currentPage' => 'integer',
        );
        foreach ($fields as $field => $type) {
            $this->assertArrayHasKey($field, $pagination, "Pagination '$field'");
            $this->assertInternalType($type, $pagination[$field], "Pagination '$field'");
        }
        $this->assertTrue(is_int($pagination['next']) || is_bool($pagination['next']), "Pagination 'next'");
        $this->assertTrue(is_int($pagination['prev']) || is_bool($pagination['prev']), "Pagination 'prev'");

    }

    /**
     * @return bool
     */
    public function testGetAll()
    {
        list($client, $dataResponse) = $this->request('', 'GET', array('limit' => -1));
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 200);

        $this->assertArrayHasKey('pagination', $dataArray);
        $this->assertArrayHasKey('results', $dataArray);
        $this->assertInternalType('array', $dataArray['results']);
        $pagination = get_object_vars($dataArray['pagination']);
        $fields = array(
            'paginate' => 'boolean',
            'total' => 'integer',
            'limit' => 'integer',
            'pages' => 'integer',
            'currentPage' => 'integer',
        );
        foreach ($fields as $field => $type) {
            $this->assertArrayHasKey($field, $pagination, "Pagination '$field'");
            $this->assertInternalType($type, $pagination[$field], "Pagination '$field'");
        }

        $this->assertTrue($pagination['limit'] === 9999999999);
        $this->assertTrue($pagination['paginate'] === false);
        $this->assertTrue($pagination['pages'] === 1);
        $this->assertTrue($pagination['currentPage'] === 1);
    }

    /**
     * @return bool
     */
    public function testGetOne()
    {
        list($client, $dataResponse) = $this->request('/1', 'GET', array());
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 200);

        foreach ($this->getExposedFields() as $field => $type) {
            if (array_key_exists($field, $dataArray)) {
                $this->assertArrayHasKey($field, $dataArray, "Field '$field'");
                $this->assertInternalType($type, $dataArray[$field], "Field '$field'");
            }
        }
    }

    public function testGetOneError()
    {
        foreach ($this->getOneNotFound() as $id) {
            list($client, $dataResponse) = $this->request("/$id", 'GET', array());
            $dataArray = get_object_vars($dataResponse);
            $this->checkClientCode($client, $dataArray, 404);

            $this->assertArrayHasKey('error', $dataArray, "Error must exist");
            $this->assertEquals($dataArray['error'], 50, "Internal error code");
            $this->assertArrayHasKey('error_description', $dataArray, "Error description must exist");
            $this->assertEquals($dataArray['error_description'], 'Record not found', "Error description");
        }
    }

    /**
     * @dataProvider sampleUpdateProvider
     * @param $record
     */
    public function testUpdate($record)
    {
        $id = $record['id'];
        unset($record['id']);

        list($client, $dataResponse) = $this->request("/{$id}", 'GET', array());
        $dataGetArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataGetArray, 200);
        $record = array_merge($dataGetArray, $record);

        list($client, $dataResponse) = $this->request("/{$id}", 'PUT', $record);
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 200);

        foreach ($this->getExposedFields() as $field => $type) {
            if (array_key_exists($field, $dataArray)) {
                $this->assertArrayHasKey($field, $dataArray, "Field '$field'");
                $this->assertInternalType($type, $dataArray[$field], "Field '$field'");
            }
        }
        foreach($record as $field => $value) {
            $this->assertArrayHasKey($field, $dataArray, "Update field '$field'");
            if (
                !is_object($dataArray[$field])
                && !is_object($dataArray[$field])
                && !is_array($dataArray[$field])
                && !is_array($dataArray[$field])
            ) {
                $this->assertEquals($value, $dataArray[$field], "Update Field '$field'");
            }
        }
        foreach ($this->getExposedFields() as $field => $type) {
            if (array_key_exists($field, $dataArray)) {
                $this->assertArrayHasKey($field, $dataArray, "Field '$field'");
                if (
                    !is_object($dataArray[$field])
                    && !is_object($dataArray[$field])
                    && !is_array($dataArray[$field])
                    && !is_array($dataArray[$field])
                ) {
                    $this->assertInternalType($type, $dataArray[$field], "Field '$field'");
                }
            }
        }
    }

    /**
     * @dataProvider sampleUpdateProvider
     * @param $record
     */
    public function testDelete($record)
    {
        list($client, $dataResponse) = $this->request("/{$record['id']}", 'DELETE');
        $dataArray = get_object_vars($dataResponse);
        $this->checkClientCode($client, $dataArray, 200);

        $this->assertArrayHasKey('message', $dataArray, 'Delete record');
        $this->assertEquals($dataArray['message'], 'Record deleted', 'Delete record message');
    }

    public function checkClientCode($client, $dataArray, $code)
    {
        if (array_key_exists('error', $dataArray) && $code !== 404) {
            print_r($dataArray);
        }
        $this->assertEquals($code, $client->getResponse()->getStatusCode());
    }
}
