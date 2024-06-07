<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Office;

class LoadOfficeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $offices = array(
            'Greenbelt, Maryland',
	        'New York City, New York',
            'Washington DC',
            'Miami, Florida',
        );

        foreach ($offices as $o) {
            $office = new Office();
            $office->setName($o);
            $manager->persist($office);
            $manager->flush();
            $this->addReference('office' . (string) $office->getId(), $office);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}