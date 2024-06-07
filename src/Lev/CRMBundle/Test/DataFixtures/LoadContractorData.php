<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Contractor;

class LoadContractorData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->getContractors() as $c) {
            $constructor = new Contractor();
            $constructor->setName("{$c['surname']} {$c['company']}");
            $manager->persist($constructor);
            $manager->flush();
            $this->addReference('constructor' . (string) $constructor->getId(), $constructor);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 7;
    }

    public function getContractors()
    {
        return array(
            array("surname"=>"Benson","company"=>"Construction, Inc."),
            array("surname"=>"Wagner","company"=>"Roofing, Inc."),
            array("surname"=>"Spence","company"=>"Roofing, Inc."),
            array("surname"=>"Hendricks","company"=>"Painting, Inc."),
            array("surname"=>"Gould","company"=>"Construction, Inc."),
            array("surname"=>"Coffey","company"=>"Construction, Inc."),
            array("surname"=>"Combs","company"=>"Painting, Inc."),
            array("surname"=>"Petty","company"=>"Masonry, Inc."),
            array("surname"=>"French","company"=>"Painting, Inc."),
            array("surname"=>"Dodson","company"=>"Painting, Inc."),
            array("surname"=>"Zimmerman","company"=>"Painting, Inc."),
            array("surname"=>"Cain","company"=>"Masonry, Inc."),
            array("surname"=>"Hansen","company"=>"Roofing, Inc."),
            array("surname"=>"Walker","company"=>"Masonry, Inc."),
            array("surname"=>"Williams","company"=>"Construction, Inc."),
            array("surname"=>"Allison","company"=>"Painting, Inc."),
            array("surname"=>"Kaufman","company"=>"Roofing, Inc."),
            array("surname"=>"Walters","company"=>"Masonry, Inc."),
            array("surname"=>"Mccormick","company"=>"Painting, Inc."),
            array("surname"=>"Morris","company"=>"Construction, Inc."),
            array("surname"=>"Bradford","company"=>"Construction, Inc."),
            array("surname"=>"Mccray","company"=>"Masonry, Inc."),
            array("surname"=>"Hatfield","company"=>"Masonry, Inc."),
            array("surname"=>"Cook","company"=>"Painting, Inc."),
            array("surname"=>"Dyer","company"=>"Masonry, Inc."),
            array("surname"=>"Solis","company"=>"Painting, Inc."),
            array("surname"=>"Charles","company"=>"Construction, Inc."),
            array("surname"=>"Stafford","company"=>"Painting, Inc."),
            array("surname"=>"Bird","company"=>"Masonry, Inc."),
            array("surname"=>"Green","company"=>"Roofing, Inc."),
            array("surname"=>"Andrews","company"=>"Construction, Inc."),
            array("surname"=>"Foreman","company"=>"Masonry, Inc."),
            array("surname"=>"Boyer","company"=>"Roofing, Inc."),
            array("surname"=>"Ellison","company"=>"Masonry, Inc."),
            array("surname"=>"Rose","company"=>"Painting, Inc."),
            array("surname"=>"Silva","company"=>"Construction, Inc."),
            array("surname"=>"Clayton","company"=>"Painting, Inc."),
            array("surname"=>"Dawson","company"=>"Construction, Inc."),
            array("surname"=>"Murray","company"=>"Painting, Inc."),
            array("surname"=>"Lane","company"=>"Construction, Inc."),
            array("surname"=>"Chapman","company"=>"Masonry, Inc."),
            array("surname"=>"Wall","company"=>"Masonry, Inc."),
            array("surname"=>"Brooks","company"=>"Masonry, Inc."),
            array("surname"=>"Brown","company"=>"Roofing, Inc."),
            array("surname"=>"Hart","company"=>"Roofing, Inc."),
            array("surname"=>"Lynch","company"=>"Masonry, Inc."),
            array("surname"=>"Kaufman","company"=>"Painting, Inc."),
            array("surname"=>"Boyle","company"=>"Roofing, Inc."),
            array("surname"=>"Santos","company"=>"Painting, Inc."),
            array("surname"=>"Blackwell","company"=>"Roofing, Inc."),
            array("surname"=>"Cervantes","company"=>"Masonry, Inc."),
            array("surname"=>"Underwood","company"=>"Roofing, Inc."),
            array("surname"=>"Fox","company"=>"Roofing, Inc."),
            array("surname"=>"Dodson","company"=>"Masonry, Inc."),
            array("surname"=>"Noel","company"=>"Construction, Inc.")
        );
    }
}