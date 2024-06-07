<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Oauth2\Client;

class LoadClientData extends AbstractDataFixture implements DataFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $manager = $this->getManager();

        $this->progressStart('client', 1);
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
        $this->addReference('client', 'default', $client);
        $this->progressAdvance();
        $this->progressFinish();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}
