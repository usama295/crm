<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Office;
use Symfony\Component\Console\Helper\ProgressBar;

class LoadOfficeData extends AbstractDataFixture implements DataFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->setDefault('office', 1);

        $manager = $this->getManager();
        $offices = array(
            'Greenbelt, Maryland',
	          'Quincy, Massachusetts',
        );

        $this->progressStart('office', count($offices));
        foreach ($offices as $o) {
            $office = new Office();
            $office->setName($o);
            $manager->persist($office);
            $manager->flush();
            $this->addReference('office', (string) $office->getId(), $office);
            $this->progressAdvance();
        }
        $this->progressFinish();
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}
