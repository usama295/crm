<?php

namespace App\Lev\CRMBundle\Test;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class BaseAPIControllerTestCase extends WebTestCase
{
    static public $accessToken;
    static public $refreshToken;
    static public $fixturesLoaded = false;

    abstract public function getUri();

    protected function setUp()
    {

        if (!self::$fixturesLoaded) {
            $this->loadFixtures(array(
                'Lev\CRMBundle\Test\DataFixtures\LoadClientData',
                'Lev\CRMBundle\Test\DataFixtures\LoadStaffRoleData',
                'Lev\CRMBundle\Test\DataFixtures\LoadOfficeData',
                'Lev\CRMBundle\Test\DataFixtures\LoadStaffData',
                'Lev\CRMBundle\Test\DataFixtures\LoadProductData',
                'Lev\CRMBundle\Test\DataFixtures\LoadCustomerData',
                'Lev\CRMBundle\Test\DataFixtures\LoadContractorData',
                'Lev\CRMBundle\Test\DataFixtures\LoadInstitutionData',
                'Lev\CRMBundle\Test\DataFixtures\LoadAdvisoryZipCodeData',
                'Lev\CRMBundle\Test\DataFixtures\LoadAppointmentData',
//                'Lev\CRMBundle\Test\DataFixtures\LoadSaleData',
            ));
            $container = $this->getContainer();
            $securityContext = $container->get('security.context');
            $userProvider = $container->get('fos_user.user_provider.username_email');
            $user = $userProvider->loadUserByUsername('admin@test.com');
            $token = new UsernamePasswordToken($user, null, 'main', array('ROLE_USER'));
            $securityContext->setToken($token);
            self::$fixturesLoaded = true;
        }

   }

    protected function request($uri = '', $method = 'GET', array $parameters = array())
    {
        $uri = $this->getUri() . $uri;
        if ($method !== 'GET') {
            $uri .='?access_token='. $this->getAccessToken();
        } else {
            $parameters['access_token'] = $this->getAccessToken();
        }
        $client = static::createClient();
        $crawler = $client->request(
            $method,
            $uri,
            $parameters,
            array(),
            array(
                'CONTENT_TYPE'          => 'application/json',
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ),
            array()
        );
        $dataResponse = json_decode($client->getResponse()->getContent());
        return array($client, $dataResponse);
    }

    protected function getAccessToken()
    {
        if (null === self::$accessToken) {
            $client = static::createClient();
            $em = $client->getContainer()->get('doctrine')->getManager();
            $oauthClient = $em->getRepository('App\Lev\CRMBundle\Entity\Oauth2\Client')->find(1);
            $crawler = $client->request(
                'GET',
                '/oauth/v2/token',
                array(
                    'username'      => 'admin@test.com',
                    'password'      => '654321',
                    'client_secret' => $oauthClient->getSecret(),
                    'client_id'     => $oauthClient->getPublicId(),
                    'grant_type'    => 'password',
                ),
                array(),
                array(
                    'CONTENT_TYPE'          => 'application/json',
                    'HTTP_X-Requested-With' => 'XMLHttpRequest'
                ),
                json_encode(array())
            );
            $responseData = json_decode($client->getResponse()->getContent());
            self::$accessToken = $responseData->access_token;
            self::$refreshToken = $responseData->refresh_token;
        }

        return self::$accessToken;
    }

}
