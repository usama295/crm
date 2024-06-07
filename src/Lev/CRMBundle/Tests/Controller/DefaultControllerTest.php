<?php

namespace App\Lev\CRMBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $responseData = json_decode($client->getResponse()->getContent());
        $this->assertEquals($responseData->msg, 'Root of API');
        $this->assertEquals($client->getResponse()->getStatusCode(), 200);
    }
}
