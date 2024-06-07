<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\AdvisoryZipCode;

class LoadAdvisoryZipCodeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getAdvisoryZipCode() as $z) {
            $zip = new AdvisoryZipCode();
            $zip->setZipCode($z);
            $manager->persist($zip);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 8;
    }

    public function getAdvisoryZipCode()
    {
        $zipCodes  = array();
        for ($i = 1; $i <= 150; $i++) {
            $zipCodes[] = str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
        }

        return $zipCodes;
    }
}