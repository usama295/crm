<?php
namespace App\Lev\CRMBundle\Test\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Lev\CRMBundle\Entity\Product;


class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $count = 0;
        foreach ($this->getProducts() as $p) {
            $count++;
            $product = new Product();
            foreach($p as $method => $value) {
                $product->$method($value);
            }
            $manager->persist($product);
            $manager->flush();
            $this->addReference('product' . (string) $product->getId(), $product);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 6;
    }

    public function getProducts()
    {
        return  array(
            array('setName' => 'Mallet', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 5),
            array('setName' => 'Chisels', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 50),
            array('setName' => 'Edger', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 3),
            array('setName' => 'Screwdriver', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 10),
            array('setName' => 'Hammer', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 20),
            array('setName' => 'Bucket', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 30),
            array('setName' => 'Floor Scrapper', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 40),
            array('setName' => 'Squeegee', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 55),
            array('setName' => 'Starway', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 60),
            array('setName' => 'Hammer', 'setType' => 'good', 'setCostMethod' => 'fixed', 'setCostAmount' => 70.3),
            array('setName' => 'Removing', 'setType' => 'labor', 'setCostMethod' => 'sqft', 'setCostAmount' => 81.5),
            array('setName' => 'Painting', 'setType' => 'labor', 'setCostMethod' => 'sqft', 'setCostAmount' => 90),
        );
   }
}
