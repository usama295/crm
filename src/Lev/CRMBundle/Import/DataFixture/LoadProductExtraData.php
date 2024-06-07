<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\ProductExtra;
use Doctrine\Common\Collections\ArrayCollection;

class LoadProductExtraData extends AbstractDataFixture implements DataFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->loadFromCSV();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 10;
    }

    // category,optionName,valueName,type,cost
    // 0        1          2         3    4
    public function loadFromCSV()
    {
        $manager = $this->getManager();
        try {
            $productExtras = $this->getCSV();
            $this->progressStart('productextra', count($productExtras));
            $createdBy = $this->getReferenceOrDefault('staff', 0);
            foreach ($productExtras as $item) {
                $productExtra = new ProductExtra();
                $productExtra
                    ->setCreatedBy($createdBy)
                    ->setCreatedAt(new \DateTime())
                    ->setCategory($item[0])
                    ->setName("{$item[1]} {$item[2]}")
                    ->setType($item[3])
                    ->setCost($item[4]);
                $manager->persist($productExtra);
                $this->progressAdvance();
                $manager->flush();
            }
            $manager->clear();
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    protected function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/productextras_pricelist.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }
}
