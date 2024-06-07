<?php

namespace App\Lev\CRMBundle\Test;

use App\Lev\CRMBundle\Test\BaseAPIControllerTestCase;

abstract class AuthTestCase extends BaseAPIControllerTestCase
{

//    public function testAccessDenied()
//    {
//        $client = static::createClient();
//        $dados = array();
//        $crawler = $client->request(
//            'GET',
//            $this->getUri(),
//            array(),
//            array(),
//            array(
//                'CONTENT_TYPE'          => 'application/json',
//                'HTTP_X-Requested-With' => 'XMLHttpRequest'
//            ),
//            json_encode($dados)
//        );
//        $dataResponse = json_decode($client->getResponse()->getContent());
//        $this->assertEquals(401, $client->getResponse()->getStatusCode());
//        $this->assertEquals('access_denied', $dataResponse->error);
//    }
//
//    public function testInvalidGrant()
//    {
//        $client = static::createClient();
//        $dados = array();
//        $crawler = $client->request(
//            'GET',
//            $this->getUri(),
//            array('access_token' => 'foo'),
//            array(),
//            array(
//                'CONTENT_TYPE'          => 'application/json',
//                'HTTP_X-Requested-With' => 'XMLHttpRequest'
//            ),
//            json_encode($dados)
//        );
//        $dataResponse = json_decode($client->getResponse()->getContent());
//        $this->assertEquals(401, $client->getResponse()->getStatusCode());
//        $this->assertEquals('invalid_grant', $dataResponse->error);
//    }

}
