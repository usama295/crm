<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Oauth2\Client;

class LoadClientData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $clientBase = new Client();
        $clientBase->setRandomId('4uloy33yimqscoo40gg0sgkgcw4s04scsc0gcgos008ogskcs');
        $clientBase->setSecret('nlbr5j37du8cwkgw08w0kgssoc4w4os8kgogo4k40gwkkgs84');
        $clientBase->setAllowedGrantTypes(array('password', 'refresh_token'));
        $manager->persist($clientBase);
        $manager->flush();

        $client = new Client();
        $client->setAllowedGrantTypes(array('password', 'refresh_token'));
        $manager->persist($client);
        $manager->flush();
        $this->addReference('client', $client);
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}